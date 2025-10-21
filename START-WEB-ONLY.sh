#!/bin/bash

echo "🌐 Starting Web Frontend Only"
echo "=============================="

cd web

# Create .env if it doesn't exist
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cat > .env << EOF
VITE_API_URL=http://localhost:8010/api/v1
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_key_here
VITE_MAPBOX_TOKEN=
EOF
fi

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo ""
    echo "📦 Installing dependencies (this will take a few minutes)..."
    npm install
fi

echo ""
echo "✅ Starting Vue.js development server..."
echo ""
npm run dev
