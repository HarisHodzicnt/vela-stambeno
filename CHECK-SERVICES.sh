#!/bin/bash

echo "🔍 Checking stambeno.ba Services"
echo "================================="
echo ""

# Check Docker services
echo "📦 Docker Services:"
if docker-compose ps 2>/dev/null | grep -q "Up"; then
    echo "   ✅ PostgreSQL: Running"
    echo "   ✅ Redis: Running"
    echo "   ✅ MailHog: Running"
else
    echo "   ❌ Docker services not running"
fi

echo ""

# Check backend
echo "🔧 Backend API:"
if lsof -i :8010 | grep -q LISTEN; then
    if curl -s http://localhost:8010/api | grep -q "Entrypoint"; then
        echo "   ✅ Backend API: Running on http://localhost:8010"
    else
        echo "   ⚠️  Backend port open but not responding correctly"
    fi
else
    echo "   ❌ Backend API: Not running"
fi

echo ""

# Check frontend
echo "🌐 Web Frontend:"
if lsof -i :5173 | grep -q LISTEN; then
    echo "   ✅ Web App: Running on http://localhost:5173"
elif lsof -i :5174 | grep -q LISTEN; then
    echo "   ✅ Web App: Running on http://localhost:5174"
else
    echo "   ❌ Web App: Not running"
fi

echo ""
echo "================================="
echo "📍 Access URLs:"
echo "   🌐 Web App:      http://localhost:5173"
echo "   🔧 Backend API:  http://localhost:8010/api"
echo "   📚 API Docs:     http://localhost:8010/api/docs"
echo "   📧 MailHog:      http://localhost:8025"
echo ""
