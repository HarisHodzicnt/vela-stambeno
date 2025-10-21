import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: () => import('@/views/HomeView.vue')
    },
    {
      path: '/search',
      name: 'Search',
      component: () => import('@/views/SearchView.vue')
    },
    {
      path: '/properties/:id',
      name: 'PropertyDetail',
      component: () => import('@/views/PropertyDetailView.vue')
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/forgot-password',
      name: 'ForgotPassword',
      component: () => import('@/views/auth/ForgotPasswordView.vue'),
      meta: { guest: true }
    },
    {
      path: '/dashboard',
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: () => import('@/views/dashboard/DashboardOverview.vue')
        },
        {
          path: 'properties',
          name: 'DashboardProperties',
          component: () => import('@/views/dashboard/DashboardProperties.vue')
        },
        {
          path: 'bookings',
          name: 'DashboardBookings',
          component: () => import('@/views/dashboard/DashboardBookings.vue')
        },
        {
          path: 'offers',
          name: 'DashboardOffers',
          component: () => import('@/views/dashboard/DashboardOffers.vue')
        },
        {
          path: 'payouts',
          name: 'DashboardPayouts',
          component: () => import('@/views/dashboard/DashboardPayouts.vue')
        },
        {
          path: 'trips',
          name: 'DashboardTrips',
          component: () => import('@/views/dashboard/DashboardTrips.vue')
        },
        {
          path: 'saved',
          name: 'DashboardSaved',
          component: () => import('@/views/dashboard/DashboardSaved.vue')
        },
        {
          path: 'profile',
          name: 'DashboardProfile',
          component: () => import('@/views/dashboard/DashboardProfile.vue')
        },
        {
          path: 'messages',
          name: 'DashboardMessages',
          component: () => import('@/views/dashboard/DashboardMessages.vue')
        },
        {
          path: 'credits',
          name: 'DashboardCredits',
          component: () => import('@/views/dashboard/CreditsView.vue')
        }
      ]
    },
    {
      path: '/properties/new',
      name: 'PropertyCreate',
      component: () => import('@/views/properties/PropertyCreateView.vue'),
      meta: { requiresAuth: true, requiresOwner: true }
    },
    {
      path: '/properties/:id/edit',
      name: 'PropertyEdit',
      component: () => import('@/views/properties/PropertyEditView.vue'),
      meta: { requiresAuth: true, requiresOwner: true }
    },
    {
      path: '/bookings',
      name: 'Bookings',
      component: () => import('@/views/bookings/BookingsView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/bookings/:id',
      name: 'BookingDetail',
      component: () => import('@/views/bookings/BookingDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/book/:id',
      name: 'BookingConfirmation',
      component: () => import('@/views/bookings/BookingConfirmationView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/booking-success',
      name: 'BookingSuccess',
      component: () => import('@/views/bookings/BookingSuccessView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/offers',
      name: 'Offers',
      component: () => import('@/views/offers/OffersView.vue'),
      meta: { requiresAuth: true }
    },
    // Catch-all 404 route
    { 
      path: '/:pathMatch(.*)*', 
      name: 'NotFound', 
      component: () => import('@/views/NotFoundView.vue') 
    }
  ],
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach((to, _from, next) => {
  const authStore = useAuthStore()

  // Initialize auth store from localStorage
  if (!authStore.user && localStorage.getItem('token')) {
    authStore.initialize()
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const guestOnly = to.matched.some(record => record.meta.guest)

  if (requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (guestOnly && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
