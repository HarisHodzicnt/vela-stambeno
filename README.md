# stambeno.ba - Real Estate Marketplace

Complete real estate platform for Bosnia & Herzegovina with rental and sales capabilities.

## 🏗️ Architecture

- **Backend**: PHP Symfony 7 + API Platform + Doctrine ORM
- **Web Frontend**: Vue.js 3 + TypeScript + Tailwind CSS + PrimeVue
- **Mobile Apps**: Flutter (iOS & Android)
- **Database**: PostgreSQL 15+
- **Cache**: Redis 7+
- **Storage**: S3-compatible (AWS/CloudFlare R2)
- **Payments**: Stripe

## 📁 Project Structure

```
stambeno.ba/
├── backend/           # Symfony API
├── web/              # Vue.js web app
├── mobile/           # Flutter mobile app
├── docs/             # Complete documentation
└── docker-compose.yml
```

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- PHP 8.2+ & Composer (for local dev)
- Node.js 20+ & npm (for web)
- Flutter 3.x (for mobile)

### 1. Start Infrastructure

```bash
# Start PostgreSQL, Redis, and MailHog
docker-compose up -d postgres redis mailhog
```

### 2. Backend Setup (Symfony)

```bash
cd backend

# Install dependencies
composer install

# Configure environment
cp .env .env.local
# Edit .env.local with your database credentials

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Generate JWT keys
php bin/console lexik:jwt:generate-keypair

# Start server
symfony server:start
# or: php -S localhost:8010 -t public/
```

Backend will run on: http://localhost:8010

API Documentation: http://localhost:8010/api/docs

### 3. Web Frontend Setup (Vue.js)

```bash
cd web

# Install dependencies
npm install

# Configure environment
cp .env.example .env
# Edit .env with API URL

# Start dev server
npm run dev
```

Web app will run on: http://localhost:5173

### 4. Mobile App Setup (Flutter)

```bash
cd mobile

# Install dependencies
flutter pub get

# Configure app_config.dart
# Edit lib/core/config/app_config.dart with your API URL

# Run on iOS Simulator
flutter run -d iPhone

# Run on Android Emulator
flutter run -d emulator-5554

# Or run on physical device
flutter run
```

## 🗄️ Database Setup

The database schema is automatically created via Doctrine migrations.

**Key entities:**
- Users (guests & owners)
- Properties (for rent/sale/both)
- RentalBookings
- PurchaseOffers
- PropertyImages
- Messages
- Reviews
- Notifications

### Create Admin User

```bash
cd backend
php bin/console app:create-admin admin@stambeno.ba password123
```

### Seed Test Data

```bash
php bin/console doctrine:fixtures:load
```

## 🔑 Environment Configuration

### Backend (.env.local)

```env
DATABASE_URL="postgresql://stambeno:password@127.0.0.1:5432/stambeno_db"
REDIS_URL=redis://localhost:6379
JWT_PASSPHRASE=your_jwt_passphrase
STRIPE_SECRET_KEY=sk_test_your_key
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_S3_BUCKET=stambeno-uploads
MAILER_DSN=sendgrid://KEY@default
```

### Web Frontend (.env)

```env
VITE_API_URL=http://localhost:8010/api/v1
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_your_key
VITE_MAPBOX_TOKEN=pk.ey...
```

### Mobile App

Edit `mobile/lib/core/config/app_config.dart`:

```dart
static const String apiBaseUrl = 'http://10.0.2.2:8010/api/v1'; // Android
static const String stripePublishableKey = 'pk_test_your_key';
```

## 📱 Features

### Core Features (MVP)
✅ User registration & authentication  
✅ Property listings (rent, sale, or both)  
✅ Advanced search & filters  
✅ Property detail view with gallery  
✅ Rental booking system  
✅ Purchase offer system  
✅ Messaging between users  
✅ Review & rating system  
✅ User dashboard  
✅ Owner dashboard  
✅ Payment processing (Stripe)  
✅ Email notifications  

### Key Differentiators
- **Dual listing**: Properties can be listed for rent AND sale simultaneously
- **Flexible rental terms**: Short-term (per night) or long-term (per month)
- **Built-in negotiation**: Purchase offers with counter-offer workflow
- **Local focus**: Bosnia & Herzegovina market with BAM currency

## 🔐 Authentication Flow

### Web & Mobile

1. **Register**: POST `/api/v1/auth/register`
2. **Login**: POST `/api/v1/auth/login` → Returns JWT token
3. **Store token**: LocalStorage (web) / SecureStorage (mobile)
4. **Include in requests**: `Authorization: Bearer {token}`
5. **Refresh**: POST `/api/v1/auth/refresh`
6. **Logout**: POST `/api/v1/auth/logout`

## 📊 API Endpoints

See complete API documentation: `/docs/mvp-api-endpoints.yaml`

**Key endpoints:**

```
# Auth
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout

# Properties
GET    /api/v1/properties
GET    /api/v1/properties/{id}
POST   /api/v1/properties
PATCH  /api/v1/properties/{id}
DELETE /api/v1/properties/{id}

# Bookings
POST   /api/v1/bookings/rental
GET    /api/v1/bookings/rental/{id}
POST   /api/v1/bookings/rental/{id}/confirm
POST   /api/v1/bookings/rental/{id}/cancel

# Offers
POST   /api/v1/offers/purchase
POST   /api/v1/offers/purchase/{id}/accept
POST   /api/v1/offers/purchase/{id}/counter
```

## 🎨 Design System

### Colors

```css
--primary: #3B82F6;
--primary-dark: #1E40AF;
--success: #10B981;
--error: #EF4444;
--warning: #F59E0B;
```

### Typography

- **Font**: Inter (Google Fonts)
- **Sizes**: 12px, 14px, 16px, 18px, 20px, 24px, 30px, 36px

### Spacing

8px base unit: 4px, 8px, 12px, 16px, 24px, 32px, 48px, 64px

## 🧪 Testing

### Backend (PHPUnit)

```bash
cd backend
php bin/phpunit
```

### Web (Vitest)

```bash
cd web
npm run test
```

### Mobile (Flutter Test)

```bash
cd mobile
flutter test
```

## 🚀 Deployment

### Backend (Symfony)

```bash
# Build for production
composer install --no-dev --optimize-autoloader

# Clear cache
php bin/console cache:clear --env=prod

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Deploy to server (example)
rsync -avz . user@server:/var/www/stambeno-api/
```

### Web (Vue.js)

```bash
cd web
npm run build

# Deploy to Vercel
vercel deploy --prod

# Or deploy to Netlify
netlify deploy --prod
```

### Mobile (Flutter)

```bash
cd mobile

# Build Android APK
flutter build apk --release

# Build iOS IPA
flutter build ipa --release

# Submit to stores
# Android: Upload to Google Play Console
# iOS: Upload to App Store Connect
```

## 📖 Documentation

Comprehensive documentation in `/docs/`:

1. **MVP-OVERVIEW.md** - Complete platform overview
2. **mvp-core-models.yaml** - Database schema (14 entities)
3. **mvp-api-endpoints.yaml** - All API endpoints (80+)
4. **mvp-business-logic.yaml** - Business rules & calculations
5. **mvp-user-flows.yaml** - User journeys (12 flows)
6. **mvp-technical-architecture.yaml** - Tech stack & infrastructure
7. **QUICK-REFERENCE.md** - Quick lookups
8. **IMPLEMENTATION-STACK.md** - Tech choices for this implementation

## 🔧 Development Commands

### Backend

```bash
# Create new entity
php bin/console make:entity Property

# Create migration
php bin/console make:migration

# Create controller
php bin/console make:controller PropertyController

# Clear cache
php bin/console cache:clear
```

### Web

```bash
# Run dev server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Type check
npm run type-check

# Lint
npm run lint
```

### Mobile

```bash
# Run app
flutter run

# Build APK
flutter build apk

# Run tests
flutter test

# Analyze code
flutter analyze

# Format code
flutter format .
```

## 📝 Common Tasks

### Add New Property Type

1. **Backend**: Add to `Property` entity enum
2. **Backend**: Update validation in `PropertyType` constraint
3. **Web**: Add to `PropertyType` type in `types/index.ts`
4. **Mobile**: Add to `PropertyType` enum in `models/property.dart`

### Add New API Endpoint

1. **Backend**: Create controller method with API Platform annotations
2. **Web**: Add API function in `src/api/properties.ts`
3. **Mobile**: Add API call in `data/repositories/property_repository.dart`

## 🐛 Troubleshooting

### Backend Issues

**Problem**: Database connection error  
**Solution**: Check `DATABASE_URL` in `.env.local`, ensure PostgreSQL is running

**Problem**: JWT authentication error  
**Solution**: Regenerate keys: `php bin/console lexik:jwt:generate-keypair`

**Problem**: CORS error  
**Solution**: Update `CORS_ALLOW_ORIGIN` in `.env` with frontend URL

### Web Issues

**Problem**: API requests failing  
**Solution**: Check `VITE_API_URL` in `.env`, ensure backend is running

**Problem**: Components not rendering  
**Solution**: Clear node_modules and reinstall: `rm -rf node_modules && npm install`

### Mobile Issues

**Problem**: API connection refused (Android)  
**Solution**: Use `10.0.2.2` instead of `localhost` for Android emulator

**Problem**: Build errors  
**Solution**: Clean and rebuild: `flutter clean && flutter pub get && flutter build`

## 🤝 Contributing

This is a proprietary project. For development questions, contact the team.

## 📄 License

Proprietary - All rights reserved

## 📞 Support

- **Email**: support@stambeno.ba
- **Documentation**: See `/docs/` directory
- **API Docs**: http://localhost:8010/api/docs

---

**Platform Version**: 1.0.0  
**Last Updated**: 2025-10-20

## 🎯 Next Steps

1. ✅ Review documentation in `/docs/`
2. ✅ Set up development environment
3. ⏳ Complete remaining entities (see TODO below)
4. ⏳ Implement authentication UI
5. ⏳ Build property search & listing
6. ⏳ Implement booking flow
7. ⏳ Add payment integration
8. ⏳ Build mobile screens
9. ⏳ Testing & refinement
10. ⏳ Production deployment

## 📋 Implementation TODO

### Backend
- [ ] Complete all entity files (PropertyImage, PurchaseOffer, Message, Review, etc.)
- [ ] Implement authentication controllers
- [ ] Create property search with filters
- [ ] Add booking business logic
- [ ] Integrate Stripe payment
- [ ] Set up email templates
- [ ] Add file upload service (S3)
- [ ] Implement notification system

### Web Frontend
- [ ] Complete all page components
- [ ] Build property search UI
- [ ] Create property detail page
- [ ] Implement booking flow
- [ ] Add offer submission UI
- [ ] Build messaging interface
- [ ] Create dashboard views
- [ ] Add image upload component

### Mobile App
- [ ] Complete router configuration
- [ ] Build authentication screens
- [ ] Create home & search screens
- [ ] Implement property detail view
- [ ] Add booking flow screens
- [ ] Build messaging UI
- [ ] Create profile & settings
- [ ] Integrate camera & image picker

---

**Ready to build!** 🚀
