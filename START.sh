#!/bin/bash

echo "🚀 Starting stambeno.ba Development Environment"
echo "================================================"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker is not running${NC}"
    echo "Please start Docker Desktop and run this script again"
    exit 1
fi

echo -e "${GREEN}✓ Docker is running${NC}"

# Start Docker services
echo ""
echo "📦 Starting infrastructure (PostgreSQL, Redis, MailHog)..."
docker-compose up -d

echo "⏳ Waiting for PostgreSQL to be ready..."
sleep 5

# Backend setup
echo ""
echo "🔧 Setting up backend..."
cd backend

if [ ! -d "vendor" ]; then
    echo "Installing PHP dependencies..."
    composer install
fi

if [ ! -f "config/jwt/private.pem" ]; then
    echo "Generating JWT keys..."
    mkdir -p config/jwt
    php bin/console lexik:jwt:generate-keypair
fi

# Create database if it doesn't exist
php bin/console doctrine:database:create --if-not-exists

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Start Symfony server in background
echo "Starting Symfony server..."
if command -v symfony &> /dev/null; then
    symfony server:start -d
else
    php -S localhost:8010 -t public/ > /dev/null 2>&1 &
    echo $! > .symfony-server.pid
fi

cd ..

# Frontend setup
echo ""
echo "🌐 Setting up web frontend..."

# Kill any existing Vite servers
echo "Cleaning up old processes..."
lsof -ti:5173,5174 | xargs kill -9 2>/dev/null || true

cd web

if [ ! -d "node_modules" ]; then
    echo "Installing Node dependencies (this may take a few minutes)..."
    npm install
fi

if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cat > .env << EOF
VITE_API_URL=http://localhost:8010/api/v1
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
VITE_MAPBOX_TOKEN=
EOF
fi

# Start Vue dev server
echo "Starting Vue.js dev server..."
npm run dev &
WEB_PID=$!
sleep 3

cd ..

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ All services started successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "📍 Access your application:"
echo "   🌐 Web App:      http://localhost:5173"
echo "   🔧 Backend API:  http://localhost:8010"
echo "   📚 API Docs:     http://localhost:8010/api/docs"
echo "   📧 MailHog:      http://localhost:8025"
echo ""
echo "💡 Open http://localhost:5173 in your browser"
echo ""
echo -e "${YELLOW}Press Ctrl+C to stop all services${NC}"

# Handle cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Stopping services..."
    kill $WEB_PID 2>/dev/null
    if [ -f "backend/.symfony-server.pid" ]; then
        kill $(cat backend/.symfony-server.pid) 2>/dev/null
        rm backend/.symfony-server.pid
    fi
    if command -v symfony &> /dev/null; then
        cd backend && symfony server:stop && cd ..
    fi
    docker-compose stop
    echo "✓ All services stopped"
    exit 0
}

trap cleanup INT TERM

# Wait for Ctrl+C
wait $WEB_PID
