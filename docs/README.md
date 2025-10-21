# stambeno.ba MVP Documentation

Complete technical specification for building a dual-purpose real estate marketplace (rentals + sales).

---

## 📚 Documentation Files

### 1. **MVP-OVERVIEW.md** ← START HERE
High-level summary of the entire platform. Read this first to understand the project scope, features, and structure.

### 2. **mvp-core-models.yaml**
Complete database schema with 14 entities, all fields, relationships, indexes, and constraints.  
**Use for**: Database design, ORM setup, understanding data structure.

### 3. **mvp-api-endpoints.yaml**
RESTful API specification with 80+ endpoints, request/response formats, and authentication.  
**Use for**: Backend API implementation, frontend API integration.

### 4. **mvp-business-logic.yaml**
Business rules, pricing calculations, workflows, policies, and platform logic.  
**Use for**: Service layer implementation, understanding business requirements.

### 5. **mvp-user-flows.yaml**
12 detailed user journeys from search to booking/purchase, including error handling.  
**Use for**: Frontend UX design, feature implementation, testing scenarios.

### 6. **mvp-technical-architecture.yaml**
Technology stack, system architecture, infrastructure, deployment, and implementation phases.  
**Use for**: Tech decisions, DevOps setup, project planning.

### 7. **QUICK-REFERENCE.md**
Fast lookup guide with common queries, calculations, checklists, and code snippets.  
**Use for**: Daily development reference, quick answers.

---

## 🎯 What is stambeno.ba?

A **dual-purpose real estate platform** for Bosnia and Herzegovina that combines:

✅ **Airbnb-style rentals** (short-term and long-term)  
✅ **Property sales marketplace** with offer/negotiation system  
✅ **Dual listing capability** (rent while selling the same property)  

**Key differentiator**: Properties can be listed for rent, for sale, or BOTH simultaneously.

---

## 🏗️ Platform Components

### For Property Owners
- List properties for rent, sale, or both
- Manage bookings and availability
- Receive and negotiate purchase offers
- Automated payments and payouts
- Guest communication and reviews

### For Guests/Buyers
- Search properties by location, price, dates
- Book rentals with instant confirmation
- Submit purchase offers with negotiation
- Secure payments via Stripe
- Review properties and owners

### Platform Features
- Real-time messaging
- Review system (mutual reveal)
- Email and push notifications
- Identity verification
- Stripe payment processing
- Availability calendar
- Map-based search

---

## 🛠️ Tech Stack

**Frontend**: React 18 + TypeScript + Tailwind CSS + shadcn/ui  
**Backend**: Node.js + Express + TypeScript  
**Database**: PostgreSQL 15+  
**Cache**: Redis 7+  
**Storage**: S3-compatible (AWS/R2/Spaces)  
**Payments**: Stripe (Checkout + Connect)  
**Maps**: Mapbox or Google Maps  
**Email**: SendGrid or AWS SES  

---

## 📊 Data Model Summary

```
Users (guests/owners)
  ├── Properties (with listing_type: rent/sale/both)
  │     ├── Images (1-20 photos)
  │     ├── Amenities (WiFi, parking, etc.)
  │     ├── Availability Calendar (for rentals)
  │     ├── Rental Bookings
  │     │     ├── Transactions (payments/refunds)
  │     │     └── Reviews
  │     └── Purchase Offers
  │           └── Transactions (sale payments)
  ├── Messages (conversations)
  ├── Saved Properties (favorites)
  └── Notifications
```

**14 core tables** with complete relationships documented in `mvp-core-models.yaml`.

---

## 🔑 Key Features

### Rental System
- ✓ Instant book or approval-required
- ✓ Dynamic pricing (per night/month)
- ✓ Availability calendar
- ✓ Automated booking workflow
- ✓ Security deposit handling
- ✓ Cancellation policies
- ✓ Check-in/out tracking

### Sales System
- ✓ Purchase offer submission
- ✓ Accept/reject/counter offer
- ✓ Multiple competing offers
- ✓ Under contract status
- ✓ 2% platform commission

### Communication
- ✓ Direct messaging
- ✓ Email notifications
- ✓ Push notifications
- ✓ Review system
- ✓ Owner responses

---

## 💰 Pricing Model

**Rentals**: 10% service fee (charged to guest)  
**Sales**: 2% commission (charged to seller on closing)  
**Owner payouts**: 24h after check-in (rentals), on closing (sales)  
**Currency**: BAM (Bosnian Convertible Mark)

---

## 📈 Implementation Timeline

| Phase | Duration | Focus |
|-------|----------|-------|
| 1. Foundation | 2-3 weeks | Setup, database, auth |
| 2. Core Features | 4-5 weeks | Properties, search, UI |
| 3. Transactions | 3-4 weeks | Bookings, offers, payments |
| 4. Communication | 2-3 weeks | Messaging, notifications, reviews |
| 5. Dashboards | 2-3 weeks | User/owner interfaces |
| 6. Polish | 2-3 weeks | Testing, optimization, deployment |

**Total**: 15-21 weeks (4-5 months)

---

## 🚀 Getting Started

### 1. **Understand the Platform**
Read `MVP-OVERVIEW.md` to grasp the full scope and features.

### 2. **Database Design**
Implement schema from `mvp-core-models.yaml`:
- Create PostgreSQL database
- Set up ORM (Prisma/TypeORM)
- Run migrations
- Add indexes

### 3. **Backend API**
Build REST API from `mvp-api-endpoints.yaml`:
- Set up Express server
- Implement authentication (JWT)
- Create endpoints by domain
- Add business logic from `mvp-business-logic.yaml`
- Integrate Stripe

### 4. **Frontend Application**
Build React app following `mvp-user-flows.yaml`:
- Set up React + TypeScript + Tailwind
- Create page components
- Implement user journeys
- Connect to API
- Add Stripe Elements

### 5. **Deploy**
Follow `mvp-technical-architecture.yaml`:
- Containerize with Docker
- Deploy to cloud (AWS/DO/Vercel)
- Set up CI/CD
- Configure monitoring

---

## 📖 Documentation Reading Order

**For Product Managers / Stakeholders**:
1. MVP-OVERVIEW.md
2. mvp-user-flows.yaml
3. mvp-business-logic.yaml

**For Backend Engineers**:
1. MVP-OVERVIEW.md
2. mvp-core-models.yaml
3. mvp-api-endpoints.yaml
4. mvp-business-logic.yaml
5. mvp-technical-architecture.yaml

**For Frontend Engineers**:
1. MVP-OVERVIEW.md
2. mvp-user-flows.yaml
3. mvp-api-endpoints.yaml
4. mvp-technical-architecture.yaml

**For DevOps Engineers**:
1. MVP-OVERVIEW.md
2. mvp-technical-architecture.yaml

**For QA Engineers**:
1. MVP-OVERVIEW.md
2. mvp-user-flows.yaml
3. mvp-business-logic.yaml

---

## 🔍 Finding Information

**"How should X work?"** → Check `mvp-business-logic.yaml`  
**"What fields does X have?"** → Check `mvp-core-models.yaml`  
**"What's the API for X?"** → Check `mvp-api-endpoints.yaml`  
**"What's the user experience for X?"** → Check `mvp-user-flows.yaml`  
**"What technology for X?"** → Check `mvp-technical-architecture.yaml`  
**"Quick lookup for X?"** → Check `QUICK-REFERENCE.md`

---

## ✅ Implementation Checklist

### Phase 1: Foundation
- [ ] Initialize git repository
- [ ] Set up project structure (frontend + backend)
- [ ] Create PostgreSQL database
- [ ] Implement schema from models doc
- [ ] Set up authentication (JWT)
- [ ] Configure environment variables
- [ ] Set up CI/CD pipeline

### Phase 2: Core Features
- [ ] User registration and login
- [ ] Property CRUD operations
- [ ] Image upload to S3
- [ ] Property search and filters
- [ ] Map integration (Mapbox/Google)
- [ ] Homepage UI
- [ ] Property detail page
- [ ] User profile page

### Phase 3: Transactions
- [ ] Rental booking flow
- [ ] Stripe integration
- [ ] Payment processing
- [ ] Availability calendar
- [ ] Purchase offer system
- [ ] Offer management (accept/reject/counter)
- [ ] Check-in/out tracking

### Phase 4: Communication
- [ ] Messaging system
- [ ] Notification system
- [ ] Email templates (SendGrid)
- [ ] Review submission
- [ ] Review display and responses
- [ ] Push notifications (optional)

### Phase 5: Dashboards
- [ ] User dashboard (bookings, saved properties)
- [ ] Owner dashboard (properties, bookings, offers)
- [ ] Booking management UI
- [ ] Offer management UI
- [ ] Transaction history
- [ ] Profile settings

### Phase 6: Launch
- [ ] Write tests (unit + integration + E2E)
- [ ] Security audit
- [ ] Performance optimization
- [ ] SEO optimization
- [ ] Legal pages (privacy, terms)
- [ ] Deploy to production
- [ ] Monitor and fix bugs

---

## 🎯 Success Criteria

### Technical Metrics
- API response time < 500ms (p95)
- Page load time < 3 seconds
- Uptime > 99.5%
- Test coverage > 70%

### Business Metrics
- 100+ properties in first 3 months
- 50+ completed bookings in first 3 months
- 10+ successful sales in first 6 months
- User satisfaction > 4.5/5 average

---

## 🔒 Security Highlights

- Passwords hashed with bcrypt (cost 12)
- JWT authentication (1h access, 30d refresh)
- HTTPS only in production
- Input validation on all endpoints
- Rate limiting per IP/user
- PCI compliant payments (Stripe)
- CSRF protection
- XSS prevention

---

## 🆘 Common Questions

**Q: Can a property be listed for both rent and sale?**  
A: Yes! Set `listing_type = 'both'`. Property can accept rentals while listed for sale.

**Q: What happens when a sale offer is accepted?**  
A: Property status changes to `under_contract`. Rentals can continue if `listing_type = 'both'`.

**Q: How are payments handled?**  
A: Stripe processes all payments. Owner receives payout 24h after check-in. Platform fee deducted automatically.

**Q: What's the cancellation policy?**  
A: Tiered refunds: 100% if 14+ days before, 50% if 7-13 days, 0% if < 7 days. See `mvp-business-logic.yaml`.

**Q: How long to build MVP?**  
A: 4-5 months with one full-stack developer. See implementation phases in architecture doc.

**Q: What's the minimum viable feature set?**  
A: User auth, property listings, search, rental bookings, purchase offers, payments, messaging, reviews. See MVP-OVERVIEW.md.

---

## 📞 Support

For questions about this documentation:
1. Search within the YAML files (they're comprehensive)
2. Check QUICK-REFERENCE.md for fast lookups
3. Review MVP-OVERVIEW.md for high-level context

---

## 📝 Notes

- All YAML files are structured for both human and machine readability
- Files can be used to generate code (schema, types, API routes)
- Documentation is version-controlled with the codebase
- Update docs when making architectural changes
- Keep MVP-OVERVIEW.md in sync with other docs

---

**Version**: 1.0  
**Status**: Ready for Implementation  
**Last Updated**: 2025-10-20

---

## File Summary

| File | Lines | Purpose | Read Time |
|------|-------|---------|-----------|
| MVP-OVERVIEW.md | ~700 | Executive summary | 15 min |
| mvp-core-models.yaml | ~900 | Database schema | 30 min |
| mvp-api-endpoints.yaml | ~200 | API specification | 20 min |
| mvp-business-logic.yaml | ~700 | Business rules | 30 min |
| mvp-user-flows.yaml | ~800 | User journeys | 30 min |
| mvp-technical-architecture.yaml | ~800 | Tech stack & infrastructure | 30 min |
| QUICK-REFERENCE.md | ~400 | Quick lookups | 10 min |

**Total reading time**: ~3 hours for complete understanding  
**Quick start**: 30 minutes (read MVP-OVERVIEW.md + QUICK-REFERENCE.md)

---

🏠 **Happy Building!** 🇧🇦
