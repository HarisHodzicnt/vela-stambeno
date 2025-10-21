# stambeno.ba - MVP Documentation Overview

## Platform Description

**stambeno.ba** is a dual-purpose real estate marketplace platform for Bosnia and Herzegovina, combining:
- **Airbnb-style short-term and long-term rentals**
- **Property sales marketplace**

Users can list properties for rent, for sale, or both simultaneously. The platform facilitates the entire transaction lifecycle from discovery to booking/purchase.

---

## Core Value Proposition

### For Property Owners
- **Dual monetization**: Rent property while listing for sale
- **Automated booking management**: Calendar, payments, guest communication
- **Sales pipeline**: Receive and manage purchase offers
- **10% service fee on rentals** (competitive with Airbnb)
- **2% commission on sales** (lower than traditional agencies)

### For Guests/Buyers
- **Unified platform**: Search rentals and sales in one place
- **Trust and transparency**: Reviews, verified owners, secure payments
- **Instant booking option**: Skip approval process
- **Flexible rental terms**: Short-term or long-term
- **Direct offer submission**: Make offers with negotiation built-in

---

## Documentation Structure

This MVP documentation consists of 5 comprehensive files:

### 1. `mvp-core-models.yaml`
**Complete data model specification**
- 14 core entities (User, Property, RentalBooking, PurchaseOffer, etc.)
- All fields with types, constraints, and relationships
- Enums, indexes, and database design
- **Use this to build database schema**

### 2. `mvp-api-endpoints.yaml`
**RESTful API specification**
- 80+ endpoints organized by domain
- Request/response formats
- Authentication requirements
- Query parameters and pagination
- **Use this to build backend API**

### 3. `mvp-business-logic.yaml`
**Business rules and calculations**
- Rental booking logic (availability, pricing, cancellations)
- Purchase offer workflow (accept, reject, counter)
- Review system rules
- Payment and payout logic
- Search algorithm and ranking
- Security and fraud prevention
- **Use this to implement service layer**

### 4. `mvp-user-flows.yaml`
**Complete user journeys**
- 12 detailed flows covering all user interactions
- Guest rental booking flow
- Guest purchase offer flow
- Owner property listing flow
- Owner booking/offer management
- Messaging, reviews, notifications
- Error handling flows
- **Use this to build frontend UI/UX**

### 5. `mvp-technical-architecture.yaml`
**Technical implementation guide**
- Full technology stack (React, Node.js, PostgreSQL, Redis, Stripe)
- System architecture and layers
- Infrastructure and deployment
- Performance optimizations
- Development workflow
- Implementation phases (4-5 months total)
- Success metrics
- **Use this as implementation roadmap**

---

## Key Features

### Property Management
✓ Create listings for rent, sale, or both  
✓ Upload up to 20 photos per property  
✓ Set dynamic pricing and availability  
✓ Instant book or approval-required modes  
✓ Comprehensive property details (beds, baths, size, amenities)  
✓ Geolocation and map integration  

### Rental System
✓ Real-time availability checking  
✓ Flexible pricing (per night or per month)  
✓ Automated booking workflow  
✓ Stripe payment processing  
✓ Security deposit handling  
✓ Cancellation policies with automatic refunds  
✓ Check-in/check-out tracking  

### Sales System
✓ Purchase offer submission  
✓ Offer management (accept, reject, counter)  
✓ Multiple competing offers  
✓ Contract tracking  
✓ "Under Contract" status  
✓ Offline transaction coordination  

### Communication
✓ Direct messaging between users  
✓ Context-aware conversations (linked to properties/bookings/offers)  
✓ Email notifications  
✓ Push notifications  
✓ Real-time unread counts  

### Reviews & Ratings
✓ Property reviews by guests  
✓ Guest reviews by owners  
✓ 5-star rating system with detailed categories  
✓ Mutual review reveal (prevents bias)  
✓ Owner response to reviews  
✓ Verified booking reviews  

### User Management
✓ Registration and authentication (JWT)  
✓ User profiles (guest and/or owner)  
✓ Identity verification  
✓ Payment setup (Stripe Connect)  
✓ Transaction history  
✓ Saved properties  

### Search & Discovery
✓ Advanced filtering (type, price, location, amenities, dates)  
✓ Sort by price, rating, newest  
✓ Map-based search  
✓ Availability-aware search (for rentals)  
✓ Separate or combined rent/sale results  

---

## Technology Stack Summary

**Frontend**: React 18 + TypeScript + Tailwind CSS + shadcn/ui  
**Backend**: Node.js + Express + TypeScript  
**Database**: PostgreSQL 15+  
**Cache**: Redis 7+  
**Storage**: S3-compatible (images)  
**Payments**: Stripe (Checkout + Connect)  
**Maps**: Mapbox or Google Maps  
**Email**: SendGrid or AWS SES  
**Hosting**: AWS, DigitalOcean, or Vercel/Railway  

---

## Critical Business Rules

### Dual Listing Logic
- Property with `listing_type = 'both'` can:
  - Accept rental bookings while listed for sale
  - Continue rentals even when "under contract"
  - Automatically remove from rentals when status = 'sold'

### Pricing
- **Rental service fee**: 10% charged to guest
- **Sale commission**: 2% charged to seller
- **Owner payout**: 24 hours after check-in (rentals)
- **Deposit**: Held separately, released 48h after checkout

### Booking Flow
- **Instant book**: Immediate confirmation, no owner approval
- **Request to book**: Owner has 24h to accept/reject
- **Cancellation policy**: Tiered refunds based on timing
- **Auto-cancel**: Pending bookings cancelled if owner doesn't respond

### Offer Flow
- Multiple offers allowed simultaneously
- Offers have expiration timestamps
- Owner can accept one (property goes "under contract")
- Counter-offers create negotiation thread
- Accepted offer triggers 2% commission on closing

### Reviews
- Only after completed bookings
- 14-day submission window
- Mutual reveal to prevent bias
- Cannot edit after posting
- Owner can respond once

---

## Database Schema Highlights

**14 Core Tables**:
1. `users` - Platform users (guests/owners)
2. `properties` - Real estate listings
3. `property_images` - Property photos
4. `amenities` - Feature catalog
5. `property_amenities` - Junction table
6. `property_availability` - Rental calendar
7. `rental_bookings` - Rental transactions
8. `purchase_offers` - Purchase proposals
9. `transactions` - Financial records
10. `reviews` - Rating and feedback
11. `messages` - User communication
12. `saved_properties` - Favorites
13. `notifications` - User alerts
14. `sessions` - Auth sessions (or use Redis)

**Key Relationships**:
- User → Properties (1:many - owner)
- Property → RentalBookings (1:many)
- Property → PurchaseOffers (1:many)
- Property → PropertyImages (1:many)
- Property ↔ Amenities (many:many)
- RentalBooking → Transactions (1:many)
- RentalBooking → Review (1:1)

---

## API Endpoint Categories

1. **Authentication** (6 endpoints) - Register, login, password reset
2. **Users** (12 endpoints) - Profile, verification, bookings, offers
3. **Properties** (18 endpoints) - CRUD, search, images, availability
4. **Rental Bookings** (7 endpoints) - Create, manage, check-in/out
5. **Purchase Offers** (7 endpoints) - Submit, accept, reject, counter
6. **Reviews** (3 endpoints) - Create, respond, list
7. **Messaging** (6 endpoints) - Send, conversations, read status
8. **Notifications** (4 endpoints) - List, read, unread count
9. **Payments** (3 endpoints) - Payment intents, confirm, payout
10. **Misc** (2 endpoints) - Amenities, webhooks

**Total**: ~80 RESTful endpoints

---

## Implementation Timeline

### Phase 1: Foundation (2-3 weeks)
- Project setup, database, auth, basic API

### Phase 2: Core Features (4-5 weeks)
- Property CRUD, search, images, frontend UI

### Phase 3: Transactions (3-4 weeks)
- Booking flow, offer flow, Stripe integration

### Phase 4: Communication (2-3 weeks)
- Messaging, notifications, reviews

### Phase 5: Dashboards (2-3 weeks)
- User/owner dashboards, management UI

### Phase 6: Polish (2-3 weeks)
- Testing, optimization, security, deployment

**Total**: 15-21 weeks (4-5 months)

---

## MVP Scope

### ✅ Included in MVP
- All core rental functionality
- All core sales functionality
- Payment processing (Stripe)
- Messaging between users
- Reviews and ratings
- Email notifications
- Identity verification
- Mobile responsive design
- Availability calendar
- Search and filters

### ❌ Post-MVP
- Mobile native apps
- Admin panel
- Advanced analytics
- Multi-language support
- Social login
- AI pricing recommendations
- Virtual tours
- Promoted/featured listings
- Referral program
- SMS notifications

---

## Security Considerations

- **Passwords**: bcrypt hashing (cost 12)
- **Authentication**: JWT tokens (1h access, 30d refresh)
- **Authorization**: Role and resource-based
- **Payments**: PCI compliant via Stripe (no card storage)
- **Input validation**: All inputs validated server-side
- **Rate limiting**: API limits per IP/user
- **HTTPS only**: TLS 1.3 in production
- **CSRF protection**: Tokens for state changes
- **XSS prevention**: Input sanitization, CSP headers

---

## Deployment Architecture

```
┌─────────────┐
│   Client    │ (React SPA on Vercel/Netlify)
│  (Browser)  │
└──────┬──────┘
       │ HTTPS
       ▼
┌─────────────┐
│ Load Balancer│
│   + CDN     │
└──────┬──────┘
       │
       ▼
┌─────────────┐      ┌──────────┐
│  Backend    │────▶ │   Redis  │
│  (Node.js)  │      │  (Cache) │
└──────┬──────┘      └──────────┘
       │
       ├────▶ PostgreSQL (Primary DB)
       │
       ├────▶ S3/R2 (Image Storage)
       │
       └────▶ Stripe API (Payments)
```

---

## Success Criteria

### Technical Metrics
- API response time < 500ms (p95)
- Page load time < 3 seconds
- Uptime > 99.5%
- Test coverage > 70%

### Business Metrics
- 100+ properties in first 3 months
- 50+ completed bookings in first 3 months
- 10+ successful sales in first 6 months
- User satisfaction > 4.5/5

---

## Development Quick Start

```bash
# Clone repository
git clone <repo-url>
cd stambeno.ba

# Backend setup
cd backend
npm install
cp .env.example .env
# Edit .env with your config
docker-compose up -d  # Start PostgreSQL + Redis
npm run migrate       # Run database migrations
npm run seed          # (Optional) Seed test data
npm run dev           # Start backend on :3000

# Frontend setup (new terminal)
cd frontend
npm install
cp .env.example .env
# Edit .env with API URL
npm run dev           # Start frontend on :5173
```

Visit `http://localhost:5173` to see the app.

---

## File Navigation Guide

**Need to understand...**
- **Data structure?** → Read `mvp-core-models.yaml`
- **API contracts?** → Read `mvp-api-endpoints.yaml`
- **Business rules?** → Read `mvp-business-logic.yaml`
- **User experience?** → Read `mvp-user-flows.yaml`
- **Tech stack?** → Read `mvp-technical-architecture.yaml`

**Ready to implement?**
1. Start with database schema from `mvp-core-models.yaml`
2. Build API endpoints from `mvp-api-endpoints.yaml`
3. Implement business logic from `mvp-business-logic.yaml`
4. Design UI following flows in `mvp-user-flows.yaml`
5. Deploy using guide in `mvp-technical-architecture.yaml`

---

## Key Differentiators from Pure Airbnb Model

| Feature | Airbnb | stambeno.ba |
|---------|---------|-------------|
| Property sales | ❌ No | ✅ Yes |
| Dual listing | ❌ No | ✅ Rent + Sale simultaneously |
| Purchase offers | ❌ No | ✅ Built-in offer system |
| Negotiation | ❌ No | ✅ Counter-offer workflow |
| Long-term rental | Limited | ✅ Full support (monthly pricing) |
| Local focus | Global | ✅ Bosnia-specific (BAM currency) |

---

## Questions You Can Answer from This Documentation

1. **How do I structure the database?** → `mvp-core-models.yaml`
2. **What endpoints do I need?** → `mvp-api-endpoints.yaml`
3. **How should booking cancellation work?** → `mvp-business-logic.yaml` (cancellation_policy)
4. **What happens when a user makes a purchase offer?** → `mvp-user-flows.yaml` (search_and_offer flow)
5. **What tech stack should I use?** → `mvp-technical-architecture.yaml` (tech_stack section)
6. **How do I handle payments?** → All docs (search for "Stripe" or "payment")
7. **How long will this take to build?** → `mvp-technical-architecture.yaml` (implementation_phases)
8. **What's the pricing model?** → `mvp-business-logic.yaml` (platform_fees)
9. **How do reviews work?** → `mvp-business-logic.yaml` (review_rules) + `mvp-user-flows.yaml` (review_flow)
10. **Can properties be listed for both rent and sale?** → YES! See `listing_type` enum in all docs

---

## Next Steps

1. **Set up version control**: Initialize git repo
2. **Create project structure**: Frontend and backend folders
3. **Set up database**: PostgreSQL with schema from models doc
4. **Build API skeleton**: Express server with routes from API doc
5. **Implement authentication**: JWT system as specified
6. **Build core entities**: Start with User and Property
7. **Integrate Stripe**: Test mode first
8. **Build frontend**: React with component library
9. **Implement flows**: One user journey at a time
10. **Test thoroughly**: Unit, integration, E2E tests
11. **Deploy to staging**: Test in production-like environment
12. **Launch MVP**: Start with small user group

---

## Support & Maintenance

### Monitoring Required
- Error tracking (Sentry)
- Uptime monitoring
- Performance metrics
- User analytics
- Financial transaction logs

### Regular Tasks
- Database backups (daily)
- Security updates (weekly)
- Performance optimization (monthly)
- User support (ongoing)
- Content moderation (ongoing)

### Scaling Triggers
- Add read replicas when queries slow
- Add Redis caching when DB load high
- Add load balancer when single server insufficient
- Add CDN when images load slowly
- Upgrade server tier incrementally

---

## Legal Considerations (Not Technical, but Important)

⚠️ Before launch, ensure:
- Privacy policy (GDPR compliant)
- Terms of service
- Owner-guest contract template
- Property sale facilitation agreement
- Payment processing terms
- Dispute resolution policy
- Content moderation policy
- Insurance recommendations
- Tax guidance for owners

---

**Documentation Version**: 1.0  
**Last Updated**: 2025-10-20  
**Platform**: stambeno.ba MVP  
**Status**: Ready for Implementation

---

## Summary

This documentation provides **everything needed** to build stambeno.ba from scratch:

✅ Complete database schema (14 tables, all fields, relationships)  
✅ Full API specification (80+ endpoints with request/response formats)  
✅ Detailed business logic (pricing, workflows, rules)  
✅ User flow diagrams (12 complete journeys)  
✅ Technical architecture (tech stack, infrastructure, deployment)  
✅ Implementation timeline (4-5 months with phases)  
✅ Success metrics (technical and business KPIs)

**You can now start building immediately** by following the documentation in order. Each YAML file is structured for machine readability and serves as both specification and implementation guide.

Good luck building stambeno.ba! 🏠🇧🇦
