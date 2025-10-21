# 🚀 Quick Start Guide

## Option 1: Start Everything (Recommended)

**Requirements:**
- Docker Desktop must be running
- PHP 8.2+ and Composer installed
- Node.js 20+ and npm installed

**Run:**
```bash
cd /Users/harishodzic/stambeno.ba
./START.sh
```

This will:
- ✅ Start PostgreSQL, Redis, and MailHog in Docker
- ✅ Set up and start Symfony backend
- ✅ Set up and start Vue.js frontend
- ✅ Open everything on correct ports

**Then visit:** http://localhost:5173

---

## Option 2: Start Web Only (If Docker isn't available)

**Requirements:**
- Node.js 20+ and npm installed

**Run:**
```bash
cd /Users/harishodzic/stambeno.ba
./START-WEB-ONLY.sh
```

This will:
- ✅ Install dependencies
- ✅ Start Vue.js dev server
- ⚠️ Note: Backend API won't be available

**Then visit:** http://localhost:5173

---

## Option 3: Manual Steps

### 1. Start Docker Services
```bash
# Make sure Docker Desktop is running, then:
docker-compose up -d
```

### 2. Setup Backend
```bash
cd backend
composer install
php bin/console doctrine:database:create
php bin/console lexik:jwt:generate-keypair
php bin/console doctrine:migrations:migrate
symfony server:start
```

### 3. Setup Web Frontend (separate terminal)
```bash
cd web
npm install
npm run dev
```

---

## 🎯 Access Points

Once running, you can access:

- **Web App**: http://localhost:5173
- **Backend API**: http://localhost:8010
- **API Documentation**: http://localhost:8010/api/docs
- **Email Testing (MailHog)**: http://localhost:8025

---

## 🛑 To Stop

If using START.sh:
- Press `Ctrl+C` in the terminal

Or manually:
```bash
# Stop Docker services
docker-compose stop

# Stop Symfony server
cd backend && symfony server:stop

# Kill npm dev server (Ctrl+C)
```

---

## ⚠️ Troubleshooting

**"Docker is not running"**
- Open Docker Desktop application
- Wait for it to fully start
- Run the script again

**"Port already in use"**
```bash
# Kill processes using ports
lsof -ti:8010 | xargs kill
lsof -ti:5173 | xargs kill
```

**Dependencies not installing**
```bash
# Backend
cd backend && composer clear-cache && composer install

# Frontend
cd web && rm -rf node_modules && npm install
```

---

**Need help?** Check the main README.md for detailed documentation.
