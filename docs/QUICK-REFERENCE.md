# Quick Reference - stambeno.ba MVP

Fast lookup guide for common implementation questions.

---

## Database Tables (14 Core)

```
users
├── properties (owner_id)
│   ├── property_images
│   ├── property_amenities → amenities
│   ├── property_availability
│   ├── rental_bookings
│   │   ├── transactions
│   │   └── reviews
│   └── purchase_offers
│       └── transactions
├── rental_bookings (guest_id)
├── purchase_offers (buyer_id)
├── reviews (reviewer_id)
├── messages (sender_id, recipient_id)
├── saved_properties
└── notifications
```

---

## Key Enums

**listing_type**: `rent` | `sale` | `both`  
**property_status**: `draft` | `active` | `inactive` | `sold` | `rented_long_term` | `under_contract`  
**booking_status**: `pending` | `confirmed` | `cancelled` | `completed` | `disputed`  
**offer_status**: `pending` | `accepted` | `rejected` | `countered` | `withdrawn` | `expired`  
**payment_status**: `pending` | `paid` | `partially_refunded` | `refunded` | `failed`  

---

## Essential API Endpoints

### Auth
```
POST /auth/register
POST /auth/login
POST /auth/logout
POST /auth/refresh
```

### Properties
```
GET    /properties              # Search
GET    /properties/:id          # Details
POST   /properties              # Create (owner)
PATCH  /properties/:id          # Update (owner)
DELETE /properties/:id          # Delete (owner)
POST   /properties/:id/publish  # Go live
```

### Bookings
```
POST /bookings/rental                    # Create booking
GET  /bookings/rental/:id                # View details
POST /bookings/rental/:id/confirm        # Owner confirms
POST /bookings/rental/:id/cancel         # Cancel
POST /bookings/rental/:id/check-in       # Check-in
POST /bookings/rental/:id/check-out      # Check-out
```

### Offers
```
POST /offers/purchase              # Make offer
GET  /offers/purchase/:id          # View details
POST /offers/purchase/:id/accept   # Owner accepts
POST /offers/purchase/:id/reject   # Owner rejects
POST /offers/purchase/:id/counter  # Counter offer
POST /offers/purchase/:id/withdraw # Buyer withdraws
```

### Reviews
```
POST /reviews                  # Create review
POST /reviews/:id/respond      # Owner responds
```

### Messages
```
POST /messages                          # Send message
GET  /messages/conversations            # List conversations
GET  /messages/conversations/:id        # Get messages
POST /messages/conversations/:id/read   # Mark read
```

---

## Pricing Calculations

### Rental Booking
```javascript
base_price = price_per_night * total_nights
// OR
base_price = price_per_month * months  // if > 30 days

service_fee = base_price * 0.10  // 10% to guest
cleaning_fee = property.cleaning_fee
deposit = property.deposit_amount

total_price = base_price + cleaning_fee + service_fee
total_to_pay = total_price + deposit

// Owner receives (after check-in):
owner_payout = base_price + cleaning_fee - stripe_fee
// Platform keeps: service_fee
// Held separately: deposit (released after 48h)
```

### Property Sale
```javascript
sale_commission = final_sale_price * 0.02  // 2% to platform
seller_receives = final_sale_price - sale_commission
```

---

## Cancellation Refunds

**By Guest**:
- **14+ days before check-in**: 100% base + cleaning + 50% service fee
- **7-13 days before**: 50% base, no cleaning, no service fee
- **< 7 days**: No refund
- **Deposit**: Always 100% refunded (unless damage)

**By Owner**:
- **Anytime**: Guest gets 100% full refund
- **Penalty**: Owner charged 10% of booking value

---

## Property Statuses

```
draft → active (publish)
  ↓
active → inactive (unpublish, temporary)
  ↓
active → under_contract (offer accepted)
  ↓
under_contract → sold (mark sold)

active → rented_long_term (long-term lease)
```

---

## Booking Workflow

### Instant Book ON
```
Guest books → Payment → Confirmed → Check-in → Check-out → Completed
                         ↓
                    Owner notified
```

### Instant Book OFF
```
Guest requests → Payment hold → Pending → Owner reviews
                                  ↓
                         Owner accepts → Confirmed → ...
                         Owner rejects → Cancelled → Refund
                         No response 24h → Cancelled → Refund
```

---

## Offer Workflow

```
Buyer submits offer → status: pending
         ↓
Owner response:
  ├─ Accept → status: accepted, property: under_contract
  ├─ Reject → status: rejected
  ├─ Counter → status: countered (buyer can accept/counter back)
  └─ Ignore → status: expired (when valid_until passes)
```

---

## Review Rules

**Eligibility**: Must have completed booking  
**Window**: 14 days after checkout  
**Mutual reveal**: Hidden until both submit OR window closes  
**One per booking**: Cannot review same booking twice  
**Owner response**: Allowed once within 30 days  

---

## File Upload Locations

```
S3 Bucket Structure:
├── users/
│   └── {userId}/
│       ├── avatar.jpg
│       └── id_verification.pdf
└── properties/
    └── {propertyId}/
        └── images/
            ├── {imageId}.jpg
            └── thumbnails/
                └── {imageId}_thumb.jpg
```

---

## Environment Variables Needed

```bash
# Server
NODE_ENV=development
PORT=3000
API_URL=http://localhost:3000

# Database
DATABASE_URL=postgresql://user:pass@localhost:5432/stambeno
REDIS_URL=redis://localhost:6379

# JWT
JWT_SECRET=your-secret-key-here
JWT_EXPIRES_IN=1h
JWT_REFRESH_SECRET=your-refresh-secret
JWT_REFRESH_EXPIRES_IN=30d

# Stripe
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Storage
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_S3_BUCKET=stambeno-uploads
AWS_REGION=eu-central-1

# Email
SENDGRID_API_KEY=SG...
FROM_EMAIL=noreply@stambeno.ba

# Maps
MAPBOX_TOKEN=pk.ey...
# OR
GOOGLE_MAPS_API_KEY=AIza...

# Frontend
VITE_API_URL=http://localhost:3000/api/v1
VITE_STRIPE_PUBLISHABLE_KEY=pk_test_...
VITE_MAPBOX_TOKEN=pk.ey...
```

---

## Common Queries

### Search properties
```sql
SELECT * FROM properties
WHERE status = 'active'
  AND listing_type IN ('rent', 'both')  -- if searching rentals
  AND city ILIKE '%Sarajevo%'
  AND bedrooms >= 2
  AND price_per_night BETWEEN 50 AND 150
  AND NOT EXISTS (
    SELECT 1 FROM rental_bookings
    WHERE property_id = properties.id
      AND status = 'confirmed'
      AND check_in_date <= '2024-12-31'
      AND check_out_date >= '2024-12-25'
  )
ORDER BY average_rating DESC
LIMIT 20 OFFSET 0;
```

### Check availability
```sql
SELECT date, status
FROM property_availability
WHERE property_id = 'uuid-here'
  AND date BETWEEN '2024-12-01' AND '2024-12-31'
  AND status = 'available';
```

### Get user's bookings
```sql
SELECT rb.*, p.title, p.city, u.first_name, u.last_name
FROM rental_bookings rb
JOIN properties p ON rb.property_id = p.id
JOIN users u ON p.owner_id = u.id
WHERE rb.guest_id = 'user-uuid'
ORDER BY rb.check_in_date DESC;
```

---

## Tech Stack Quick List

**Frontend**: React 18, TypeScript, Tailwind, shadcn/ui, React Query, React Hook Form  
**Backend**: Node.js 20, Express, TypeScript, Prisma/TypeORM  
**Database**: PostgreSQL 15  
**Cache**: Redis 7  
**Storage**: S3/R2/Spaces  
**Payments**: Stripe  
**Maps**: Mapbox/Google Maps  
**Email**: SendGrid/SES  
**Testing**: Jest, Supertest, Playwright  
**Deployment**: Docker, AWS/DO/Vercel  

---

## Performance Targets

- **API Response**: < 500ms (p95)
- **Page Load**: < 3 seconds
- **Uptime**: > 99.5%
- **Image Load**: < 2 seconds (via CDN)
- **Search Results**: < 1 second

---

## Security Checklist

✓ Passwords hashed with bcrypt (cost 12)  
✓ JWT tokens with 1h expiry  
✓ HTTPS only in production  
✓ Input validation on all endpoints  
✓ SQL injection prevention (ORM)  
✓ XSS prevention (sanitization)  
✓ CSRF tokens  
✓ Rate limiting (API)  
✓ Helmet.js security headers  
✓ No card storage (use Stripe)  

---

## Testing Strategy

**Unit Tests**: Business logic functions (70%+ coverage)  
**Integration Tests**: API endpoints with test DB  
**E2E Tests**: Critical flows (booking, offer, payment)  

Key flows to test:
1. User registration and login
2. Property creation and publishing
3. Property search and filtering
4. Rental booking (instant and approval)
5. Purchase offer submission
6. Payment processing
7. Review submission
8. Messaging

---

## Common Validation Rules

```javascript
// User
email: valid_email_format
password: min_8_chars + uppercase + lowercase + number
phone: valid_phone_format
date_of_birth: user_must_be_18+

// Property
title: max_200_chars
bedrooms: min_0, max_50
bathrooms: min_0, max_50
size_sqm: min_1
price_per_night: min_0 (if listing_type = rent or both)
sale_price: min_0 (if listing_type = sale or both)

// Booking
check_out_date: must_be_after_check_in_date
total_nights: must_be >= property.min_rental_nights
number_of_guests: must_be > 0

// Offer
offer_amount: must_be > 0
offer_valid_until: must_be_future_timestamp
```

---

## Cron Jobs to Implement

```javascript
// Every 15 minutes
check_and_cancel_unconfirmed_bookings()

// Every hour
expire_purchase_offers()

// Every 6 hours
release_deposits_after_48h()

// Daily at 9am
send_checkin_reminders()  // 24h before
send_review_reminders()

// Daily at 1am
cleanup_expired_sessions()
aggregate_daily_stats()
```

---

## Webhook Events to Handle

**Stripe**:
- `payment_intent.succeeded` → Confirm booking
- `payment_intent.payment_failed` → Cancel booking
- `charge.refunded` → Update booking status
- `account.updated` → Update owner Stripe account info

---

## Critical User Permissions

```javascript
// Can create property
user.is_owner === true && user.is_verified === true

// Can confirm booking
user.id === property.owner_id

// Can cancel booking
user.id === booking.guest_id || user.id === property.owner_id

// Can accept/reject offer
user.id === property.owner_id

// Can view booking details
user.id === booking.guest_id || user.id === property.owner_id

// Can message
user.is_verified === true (prevents spam)
```

---

## Error Handling Template

```javascript
try {
  // Business logic
} catch (error) {
  if (error instanceof ValidationError) {
    return res.status(400).json({ error: 'validation_error', message: error.message });
  }
  if (error instanceof NotFoundError) {
    return res.status(404).json({ error: 'not_found', message: error.message });
  }
  if (error instanceof UnauthorizedError) {
    return res.status(403).json({ error: 'forbidden', message: error.message });
  }
  // Unknown error
  logger.error(error);
  return res.status(500).json({ error: 'internal_error', message: 'An unexpected error occurred' });
}
```

---

## MVP Launch Checklist

**Technical**:
- [ ] All tests passing
- [ ] Security audit completed
- [ ] Performance benchmarks met
- [ ] Error tracking configured (Sentry)
- [ ] Monitoring and alerts setup
- [ ] Database backups automated
- [ ] SSL certificates configured
- [ ] CDN configured for images
- [ ] Email templates tested
- [ ] Payment flows tested (test mode)

**Legal**:
- [ ] Privacy policy published
- [ ] Terms of service published
- [ ] Cookie consent implemented
- [ ] GDPR compliance verified

**Content**:
- [ ] Homepage content
- [ ] About page
- [ ] FAQ page
- [ ] Contact page
- [ ] Help documentation

**Business**:
- [ ] Stripe account verified
- [ ] Bank account connected
- [ ] Domain configured
- [ ] Email domain verified
- [ ] Support email setup

---

For complete details, refer to the main documentation files in `/docs/`.
