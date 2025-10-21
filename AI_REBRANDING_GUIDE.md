# 🤖 AI Rebranding Guide: Property Rental Platform MVP

> **For AI Assistants**: This guide will help you rebrand and redesign a property rental application based on this MVP's design patterns, UI/UX philosophy, and architecture.

---

## 📋 Overview

This MVP is a **full-stack property rental platform** (Stambeno.ba clone) with:
- **Backend**: Symfony 6.3 + API Platform (PHP)
- **Frontend**: Vue.js 3 + TypeScript + Vite
- **Mobile**: Flutter/Dart (basic setup)
- **Design**: Modern, clean, card-based UI with responsive layouts

---

## 🎨 Design Philosophy

### Core Principles
1. **Clean & Minimal** - White backgrounds, subtle shadows, generous spacing
2. **Card-Based Layout** - Everything is a card with hover effects
3. **Pink Accent Color** (#e91e63) - Used for CTAs, prices, and important actions
4. **Purple Primary** (#5a55ea) - Used for primary actions and highlights
5. **Responsive Grid** - Mobile-first, adapts from 1 to 4 columns
6. **Smooth Animations** - 200-300ms transitions for all interactions
7. **Icon-Driven** - PrimeIcons for consistent iconography

### Color Palette
```css
--color-primary: #5a55ea;        /* Purple - Primary actions */
--color-secondary: #e91e63;      /* Pink - Prices, CTAs */
--color-text-primary: #212529;   /* Dark text */
--color-text-secondary: #6c757d; /* Muted text */
--color-border-light: #e9ecef;   /* Subtle borders */
```

---

## 🏗️ Frontend Architecture

### Key Directories
```
web/src/
├── api/              # API client with interceptors
├── assets/           # Global CSS, fonts, images
├── components/       # Reusable components
│   ├── dashboard/    # Dashboard-specific cards
│   ├── forms/        # Form inputs (CounterInput, ImageUploader)
│   ├── layout/       # Header, Footer
│   └── properties/   # Property cards, previews
├── composables/      # Vue composables (useStripe, etc.)
├── router/           # Vue Router with auth guards
├── stores/           # Pinia stores (auth, etc.)
├── types/            # TypeScript interfaces
└── views/            # Page components
    ├── auth/         # Login, Register, Forgot Password
    ├── dashboard/    # User dashboard pages
    ├── properties/   # Property CRUD
    └── bookings/     # Booking management
```

---

## 🎯 Key UI/UX Patterns

### 1. Property Cards
**Design**: Image on top, content below, hover effects reveal actions

```vue
<!-- Structure -->
<div class="property-card">
  <div class="image-wrapper">
    <img />
    <div class="status-badge">DRAFT</div>
    <div class="actions-overlay">
      <button>Edit</button>
      <button>View</button>
    </div>
  </div>
  <div class="content">
    <h3>Title</h3>
    <p>Location</p>
    <div class="price">150 BAM</div>
  </div>
</div>
```

**Key Features**:
- Image: 200-240px height, `object-fit: cover`
- Hover: Image scales to 1.05, actions slide up from bottom
- Status badges: Absolute positioned top-right
- Price: Large, bold, pink color

### 2. Dashboard Layout
**Pattern**: Grid of cards (auto-fill, minmax(280px, 1fr))

```vue
<div class="properties-grid">
  <DashboardPropertyCard v-for="property in properties" />
</div>

<style>
.properties-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
</style>
```

### 3. Stepper Forms
**Pattern**: Multi-step wizard for complex forms (Property Create/Edit)

```
Step Navigation: [1] → [2] → [3] → [4]
Step Content: Show only current step
Navigation: Back/Next buttons at bottom
```

**Implementation**:
- `currentStep` ref to track progress
- Computed property for step validation
- Visual progress indicator at top

### 4. Authentication Flow
**Pattern**: Modal-based login/register overlays

- Clean forms with validation
- JWT token storage in localStorage
- Auth guard on protected routes
- Auto-redirect after login

### 5. Image Upload
**Pattern**: Drag-drop + file picker with previews

- Multiple image support
- Preview grid with remove buttons
- Existing + new images side-by-side
- FormData for multipart uploads

---

## 🔧 Technical Implementation

### API Client Setup
```typescript
// src/api/client.ts
const client = axios.create({
  baseURL: 'http://localhost:8010/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor for JWT
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})
```

### Route Guards
```typescript
// src/router/index.ts
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresOwner) {
    // Check ownership logic
  } else {
    next()
  }
})
```

### Property Edit Flow
```
1. Load property with owner data
2. Pre-populate form fields
3. Track existing images + new uploads
4. Mark images for deletion
5. On save:
   - PATCH property data
   - DELETE marked images
   - POST new images
   - Redirect to detail page
```

---

## 🎨 Component Patterns to Replicate

### 1. Card with Hover Actions
```vue
<template>
  <div class="card">
    <div class="image-wrapper">
      <img :src="image" />
      <div class="actions-overlay">
        <button @click="edit">Edit</button>
        <button @click="view">View</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.actions-overlay {
  position: absolute;
  bottom: 0;
  opacity: 0;
  transform: translateY(100%);
  transition: all 0.3s;
}

.card:hover .actions-overlay {
  opacity: 1;
  transform: translateY(0);
}
</style>
```

### 2. Empty State
```vue
<div class="empty-state">
  <i class="pi pi-home" style="font-size: 64px"></i>
  <h3>No items found</h3>
  <p>Add your first item to get started.</p>
  <button class="btn btn-primary">Add Item</button>
</div>
```

### 3. Loading Spinner
```vue
<div class="loading-state">
  <div class="spinner"></div>
  <p>Loading...</p>
</div>

<style>
.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f1f3f5;
  border-top-color: #5a55ea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
```

---

## 🔄 How to Adapt This MVP to Your Use Case

### Step 1: Analyze Your Backend
**Questions to ask**:
- What entities do you have? (Properties → Your Domain Objects)
- What are the CRUD operations?
- What relationships exist? (User → Properties → Bookings)
- What authentication method? (JWT, OAuth, Session)

**Action**: Map your backend entities to frontend types
```typescript
// src/types/index.ts
export interface YourEntity {
  id: string
  name: string
  // ... your fields
}
```

### Step 2: Replicate the Design System
**Keep these from MVP**:
- ✅ Color palette (customize primary/secondary)
- ✅ Card-based layouts
- ✅ Grid responsiveness (auto-fill pattern)
- ✅ Hover interactions
- ✅ Typography scale
- ✅ Spacing system

**Customize**:
- 🎨 Brand colors (replace pink/purple)
- 🖼️ Placeholder images (your domain)
- 📝 Copy/labels (your terminology)
- 🎭 Icons (match your domain)

### Step 3: Component Mapping
Map MVP components to your domain:

| MVP Component | Your Equivalent | Example |
|--------------|-----------------|---------|
| PropertyCard | ProductCard | E-commerce product |
| PropertyEditView | ProductEditView | Product editor |
| DashboardProperties | DashboardInventory | Inventory list |
| BookingCard | OrderCard | Order details |

### Step 4: Adapt Forms
**Stepper Pattern** works for any multi-step process:
- Product creation (Details → Images → Pricing → Review)
- User onboarding (Profile → Preferences → Payment → Done)
- Order checkout (Cart → Shipping → Payment → Confirm)

**Implementation**:
1. Copy `PropertyCreateView.vue` structure
2. Replace steps with your flow
3. Adjust form fields to your data model
4. Keep navigation pattern

### Step 5: Dashboard Layout
**Grid Pattern** works for any card-based content:

```vue
<div class="dashboard-grid">
  <YourCard v-for="item in items" :key="item.id" :item="item" />
</div>

<style>
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
</style>
```

---

## 🔐 Backend Integration Advice

### DO NOT Change Backend Structure
**Instead, provide advice on**:

#### 1. API Response Format
✅ **Good** (Current MVP pattern):
```json
{
  "id": "uuid",
  "title": "Property Title",
  "owner": {
    "id": "uuid",
    "firstName": "John",
    "lastName": "Doe"
  },
  "images": [
    { "id": "uuid", "url": "/uploads/..." }
  ]
}
```

💡 **Advice**: Ensure nested objects (owner, images) are fully serialized, not just IRI references.

#### 2. Authentication
✅ **Current MVP**: JWT tokens with `Authorization: Bearer <token>` header

💡 **Advice**: If backend uses different auth:
- Session-based → Add `credentials: 'include'` to axios
- OAuth → Implement OAuth flow in frontend
- API Key → Add to headers

#### 3. File Uploads
✅ **Current MVP**: FormData with multiple files

💡 **Advice**: If backend expects different format:
- Single file → Change to single file input
- Base64 → Encode before sending
- Pre-signed URLs → Get URL first, then upload

#### 4. CORS Configuration
💡 **Advice**: Ensure backend allows:
```
Access-Control-Allow-Origin: http://localhost:5173
Access-Control-Allow-Methods: GET, POST, PATCH, DELETE
Access-Control-Allow-Headers: Authorization, Content-Type
```

#### 5. Pagination
✅ **Current MVP**: Handles Hydra format (`hydra:member`)

💡 **Advice**: If using different pagination:
```typescript
// Adapt in API client
const response = await api.get('/items')
const items = response.data || response['hydra:member'] || response.items
```

---

## 📦 Key Files to Study

### Frontend Structure
1. **`web/src/views/dashboard/DashboardProperties.vue`** - Grid layout pattern
2. **`web/src/components/dashboard/DashboardPropertyCard.vue`** - Card with hover actions
3. **`web/src/views/properties/PropertyEditView.vue`** - Multi-step form
4. **`web/src/views/properties/PropertyCreateView.vue`** - Form wizard pattern
5. **`web/src/components/properties/PropertyCard.vue`** - Public card design
6. **`web/src/api/client.ts`** - API client setup
7. **`web/src/router/index.ts`** - Auth guards
8. **`web/src/stores/auth.ts`** - State management

### Styling
1. **`web/src/assets/main.css`** - Global styles, CSS variables
2. Component `<style scoped>` - Card patterns, animations

---

## 🚀 Rebranding Checklist

### Phase 1: Setup & Analysis
- [ ] Clone this repository
- [ ] Study backend API endpoints
- [ ] Map entities to frontend types
- [ ] Identify key user flows

### Phase 2: Design System
- [ ] Choose primary/secondary colors
- [ ] Update CSS variables in `main.css`
- [ ] Gather domain-specific images/icons
- [ ] Define typography if different

### Phase 3: Component Adaptation
- [ ] Create new entity types in `types/index.ts`
- [ ] Adapt property cards to your domain
- [ ] Update form fields for your data model
- [ ] Replicate dashboard grid layout
- [ ] Implement search/filter for your entities

### Phase 4: Backend Integration
- [ ] Update API baseURL in `client.ts`
- [ ] Test authentication flow
- [ ] Verify CORS settings
- [ ] Test file uploads (if needed)
- [ ] Handle pagination format

### Phase 5: Polish
- [ ] Replace placeholder images
- [ ] Update all copy/labels
- [ ] Test responsive layouts
- [ ] Add loading states
- [ ] Implement error handling
- [ ] Test all user flows

---

## 💡 Design Patterns Quick Reference

### Responsive Grid
```css
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
```

### Card Hover Effect
```css
.card {
  transition: all 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
```

### Action Overlay
```css
.overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  opacity: 0;
  transform: translateY(100%);
  transition: all 0.3s;
}

.card:hover .overlay {
  opacity: 1;
  transform: translateY(0);
}
```

### Loading Spinner
```html
<div class="spinner"></div>

<style>
.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f1f3f5;
  border-top-color: #5a55ea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
```

---

## 🎯 Success Criteria

Your rebranded app should have:
- ✅ Clean, modern UI matching the MVP aesthetic
- ✅ Responsive grid layouts (mobile to desktop)
- ✅ Smooth hover animations
- ✅ Card-based design throughout
- ✅ Multi-step forms for complex workflows
- ✅ Authentication with route guards
- ✅ Loading and empty states
- ✅ Image upload with previews (if applicable)
- ✅ Dashboard with entity management
- ✅ CRUD operations for main entities

---

## 📚 Additional Resources

### Styling
- **PrimeIcons**: https://primevue.org/icons/ (Icon set used)
- **CSS Grid**: https://css-tricks.com/snippets/css/complete-guide-grid/
- **Vue 3 Composition API**: https://vuejs.org/guide/introduction.html

### Backend Integration
- **Axios**: https://axios-http.com/docs/intro
- **JWT Auth**: https://jwt.io/introduction
- **FormData**: https://developer.mozilla.org/en-US/docs/Web/API/FormData

---

## 🤝 AI Assistant Instructions

When helping a user rebrand this app:

1. **First, understand their domain**:
   - What is the app about? (e-commerce, rentals, social, etc.)
   - What are the main entities? (Products, Users, Posts, etc.)
   - What are the key user flows?

2. **Map components**:
   - Property → Their main entity
   - PropertyCard → TheirEntityCard
   - Dashboard → TheirDashboard

3. **Preserve patterns**:
   - Keep grid layouts
   - Keep card-based design
   - Keep hover effects
   - Keep multi-step form structure

4. **Customize visuals**:
   - Replace colors (CSS variables)
   - Update images (placeholders)
   - Change copy (labels, buttons)
   - Adjust icons (domain-appropriate)

5. **Backend integration**:
   - **DO NOT modify backend code**
   - Only provide advice on what might need adjustment
   - Test API calls thoroughly
   - Handle different response formats in frontend

6. **Test thoroughly**:
   - All CRUD operations
   - Authentication flow
   - Responsive layouts
   - Form validations
   - Image uploads (if applicable)

---

## 📝 Example Adaptation: E-commerce Store

Let's say you're adapting this to an **e-commerce product management system**:

### Entity Mapping
- Property → Product
- PropertyCard → ProductCard
- PropertyEditView → ProductEditView
- BookingCard → OrderCard

### Design Changes
- Primary color: Blue (#007bff) instead of purple
- Accent color: Orange (#ff6b35) for "Add to Cart"
- Images: Products instead of properties

### Form Steps
Instead of Property creation steps:
1. **Product Info** (Name, Description, Category)
2. **Pricing** (Price, Discount, Tax)
3. **Inventory** (Stock, SKU, Variants)
4. **Images** (Product photos)
5. **Shipping** (Weight, Dimensions)
6. **Review** (Preview before publish)

### Component Reuse
- ✅ Grid layout for product listings
- ✅ Card with hover for product cards
- ✅ Multi-step form for product creation
- ✅ Image uploader for product photos
- ✅ Dashboard for inventory management

---

## 🎓 Final Notes

This MVP demonstrates modern web development best practices:
- TypeScript for type safety
- Vue 3 Composition API for reactivity
- Pinia for state management
- Axios with interceptors for API calls
- Route guards for authentication
- Responsive design with CSS Grid
- Smooth animations and transitions

**The goal is to replicate the FEEL and QUALITY of this UI, not copy it exactly.**

Adapt the components, colors, and content to your specific domain while maintaining the clean, professional aesthetic demonstrated here.

---

**Good luck with your rebranding! 🚀**

If you have questions, refer to the actual component implementations in `web/src/` for detailed patterns.
