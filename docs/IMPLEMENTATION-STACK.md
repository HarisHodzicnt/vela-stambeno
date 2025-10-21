# Implementation Stack - stambeno.ba MVP

## Technology Choices

### Backend: PHP Symfony 7.x
**Rationale**: Enterprise-grade PHP framework with excellent API support
- **Framework**: Symfony 7.x
- **ORM**: Doctrine ORM
- **API**: API Platform (RESTful JSON API)
- **Auth**: LexikJWTAuthenticationBundle
- **Validation**: Symfony Validator
- **Testing**: PHPUnit + Symfony Test Suite
- **Documentation**: API Platform OpenAPI/Swagger

### Frontend Web: Vue.js 3
**Rationale**: Progressive framework, excellent DX, Composition API
- **Framework**: Vue 3 with Composition API
- **Language**: TypeScript
- **Build Tool**: Vite
- **Router**: Vue Router 4
- **State**: Pinia (Vue 3 state management)
- **UI Library**: PrimeVue + Tailwind CSS
- **Forms**: VeeValidate + Yup
- **HTTP**: Axios
- **Maps**: Leaflet or Google Maps
- **Payments**: Stripe Vue SDK

### Mobile Apps: Flutter
**Rationale**: Single codebase for iOS/Android, native performance
- **Framework**: Flutter 3.x
- **Language**: Dart
- **State**: Riverpod or BLoC pattern
- **HTTP**: Dio
- **Storage**: SharedPreferences + Hive
- **Maps**: Google Maps Flutter
- **Payments**: flutter_stripe
- **Navigation**: go_router

### Infrastructure (Same as Docs)
- **Database**: PostgreSQL 15+
- **Cache**: Redis 7+
- **Storage**: S3-compatible (AWS/CloudFlare R2)
- **Payments**: Stripe
- **Email**: SendGrid/AWS SES
- **Hosting**: AWS/DigitalOcean/Hetzner

---

## Project Structure

```
stambeno.ba/
├── backend/                 # Symfony API
│   ├── bin/
│   ├── config/
│   ├── migrations/
│   ├── public/
│   ├── src/
│   │   ├── Controller/     # API Controllers
│   │   ├── Entity/         # Doctrine Entities
│   │   ├── Repository/     # Database Repositories
│   │   ├── Service/        # Business Logic
│   │   ├── EventListener/  # Event Subscribers
│   │   └── Security/       # Auth & Permissions
│   ├── tests/
│   ├── var/
│   ├── composer.json
│   └── .env
│
├── web/                    # Vue.js Web App
│   ├── public/
│   ├── src/
│   │   ├── api/           # API client
│   │   ├── assets/        # Images, styles
│   │   ├── components/    # Reusable components
│   │   ├── composables/   # Composition functions
│   │   ├── layouts/       # Page layouts
│   │   ├── pages/         # Page components
│   │   ├── router/        # Vue Router config
│   │   ├── stores/        # Pinia stores
│   │   ├── utils/         # Helpers
│   │   ├── App.vue
│   │   └── main.ts
│   ├── package.json
│   ├── vite.config.ts
│   └── tsconfig.json
│
├── mobile/                 # Flutter App
│   ├── android/
│   ├── ios/
│   ├── lib/
│   │   ├── core/          # Core utilities
│   │   ├── data/          # Data layer
│   │   ├── models/        # Data models
│   │   ├── providers/     # State management
│   │   ├── screens/       # App screens
│   │   ├── widgets/       # Reusable widgets
│   │   └── main.dart
│   ├── test/
│   ├── pubspec.yaml
│   └── README.md
│
├── docs/                   # Documentation (existing)
├── docker-compose.yml      # Local development
└── README.md
```

---

## Development Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 20+
- npm/pnpm
- PostgreSQL 15+
- Redis 7+
- Flutter SDK 3.x
- Docker (optional)

### Quick Start

```bash
# 1. Backend (Symfony)
cd backend
composer install
cp .env.example .env
# Configure database in .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
symfony server:start  # or php -S localhost:8010 -t public/

# 2. Frontend (Vue)
cd web
npm install
cp .env.example .env
npm run dev  # Runs on localhost:5173

# 3. Mobile (Flutter)
cd mobile
flutter pub get
flutter run  # Choose device/emulator
```

---

## Design System - Clean & Simple

### Color Palette
```scss
// Primary brand colors
$primary: #3B82F6;      // Blue
$primary-dark: #1E40AF;
$primary-light: #DBEAFE;

// Neutrals
$white: #FFFFFF;
$gray-50: #F9FAFB;
$gray-100: #F3F4F6;
$gray-200: #E5E7EB;
$gray-300: #D1D5DB;
$gray-500: #6B7280;
$gray-700: #374151;
$gray-900: #111827;

// Semantic colors
$success: #10B981;
$warning: #F59E0B;
$error: #EF4444;
$info: #3B82F6;
```

### Typography
```scss
// Font family
$font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

// Font sizes
$text-xs: 0.75rem;    // 12px
$text-sm: 0.875rem;   // 14px
$text-base: 1rem;     // 16px
$text-lg: 1.125rem;   // 18px
$text-xl: 1.25rem;    // 20px
$text-2xl: 1.5rem;    // 24px
$text-3xl: 1.875rem;  // 30px
$text-4xl: 2.25rem;   // 36px
```

### Spacing
```scss
// 8px base unit
$space-1: 0.25rem;  // 4px
$space-2: 0.5rem;   // 8px
$space-3: 0.75rem;  // 12px
$space-4: 1rem;     // 16px
$space-6: 1.5rem;   // 24px
$space-8: 2rem;     // 32px
$space-12: 3rem;    // 48px
$space-16: 4rem;    // 64px
```

### UI Principles
1. **Minimalist**: Clean interfaces, plenty of white space
2. **Consistent**: Same patterns across web and mobile
3. **Accessible**: WCAG AA compliant, clear hierarchy
4. **Fast**: Optimistic updates, skeleton loaders
5. **Mobile-first**: Responsive design, touch-friendly

---

## Shared Flows (Web & Mobile)

### Authentication
1. Splash screen with login/register options
2. Email + password or social login (future)
3. Simple 2-step registration (details → verification)
4. JWT token storage (localStorage for web, secure storage for mobile)

### Property Search
1. Search bar with location autocomplete
2. Filter sheet/modal (price, type, bedrooms, amenities)
3. Grid view with cards (image, price, location, rating)
4. Map view toggle
5. Property detail on tap/click

### Booking Flow
1. Select dates on calendar
2. Enter guest count
3. See price breakdown
4. Payment form (Stripe)
5. Confirmation screen with booking details

### Offer Flow
1. View property details
2. "Make Offer" button
3. Offer form (amount, financing, message)
4. Confirmation with status tracking

### Messaging
1. Conversations list with unread badges
2. Chat interface with property context
3. Real-time or polling updates

---

## API Communication

### Base URL
```
Development: http://localhost:8010/api/v1
Production: https://api.stambeno.ba/v1
```

### Authentication
```http
Authorization: Bearer <jwt_token>
```

### Request/Response Format
```json
// Success
{
  "data": { ... },
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 100
  }
}

// Error
{
  "error": {
    "code": "validation_error",
    "message": "Invalid input",
    "details": {
      "email": ["Email is required"]
    }
  }
}
```

---

## Next Steps

1. ✅ Documentation complete
2. 🔄 Set up Symfony backend
3. ⏳ Implement core entities
4. ⏳ Build API endpoints
5. ⏳ Set up Vue.js frontend
6. ⏳ Build UI components
7. ⏳ Set up Flutter app
8. ⏳ Implement features end-to-end
9. ⏳ Testing and refinement
10. ⏳ Deployment

---

**Status**: Ready to implement  
**Updated**: 2025-10-20
