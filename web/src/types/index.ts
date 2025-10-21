// User types
export interface User {
  id: string
  email: string
  firstName: string
  lastName: string
  phone: string
  avatarUrl?: string
  dateOfBirth: string
  isVerified: boolean
  isOwner: boolean
  isBusiness: boolean
  city?: string
  country: string
  averageRatingAsOwner: number
  averageRatingAsGuest: number
  totalReviewsReceived: number
  createdAt: string
  accountStatus: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  email: string
  password: string
  firstName: string
  lastName: string
  phone: string
  dateOfBirth: string
  isOwner?: boolean
}

// Property types
export interface Property {
  id: string
  owner: User
  propertyType: PropertyType
  listingType: ListingType
  title: string
  description: string
  addressLine1: string
  addressLine2?: string
  city: string
  municipality?: string
  postalCode?: string
  country: string
  latitude?: number
  longitude?: number
  bedrooms: number
  bathrooms: number
  sizeSqm: number
  floor?: number
  totalFloors?: number
  yearBuilt?: number
  pricePerNight?: number
  pricePerMonth?: number
  minRentalNights: number
  maxRentalNights?: number
  cleaningFee: number
  depositAmount: number
  salePrice?: number
  priceNegotiable: boolean
  availableFrom?: string
  availableUntil?: string
  instantBook: boolean
  status: PropertyStatus
  isFeatured: boolean
  coverImageUrl?: string
  virtualTourUrl?: string
  averageRating: number
  totalReviews: number
  totalRentalBookings: number
  slug: string
  images: PropertyImage[]
  createdAt: string
  updatedAt: string
  publishedAt?: string
  soldAt?: string
}

export interface PropertyImage {
  id: string
  url: string
  thumbnailUrl?: string
  caption?: string
  displayOrder: number
}

export type PropertyType = 'apartment' | 'house' | 'studio' | 'villa' | 'condo' | 'land' | 'commercial'
export type ListingType = 'rent' | 'sale' | 'both'
export type PropertyStatus = 'draft' | 'active' | 'inactive' | 'sold' | 'rented_long_term' | 'under_contract'

// Booking types
export interface RentalBooking {
  id: string
  property: Property
  guest: User
  checkInDate: string
  checkOutDate: string
  totalNights: number
  numberOfGuests: number
  guestPhone: string
  specialRequests?: string
  basePrice: number
  cleaningFee: number
  serviceFee: number
  totalPrice: number
  depositAmount: number
  paymentStatus: PaymentStatus
  status: BookingStatus
  requiresOwnerApproval: boolean
  confirmedAt?: string
  createdAt: string
}

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed' | 'disputed'
export type PaymentStatus = 'pending' | 'paid' | 'partially_refunded' | 'refunded' | 'failed'

// Offer types
export interface PurchaseOffer {
  id: string
  property: Property
  buyer: User
  offerAmount: number
  message?: string
  financingType: FinancingType
  contingencies?: string
  proposedClosingDate?: string
  offerValidUntil: string
  status: OfferStatus
  counterOfferAmount?: number
  counterOfferMessage?: string
  counterOfferAt?: string
  respondedAt?: string
  responseMessage?: string
  createdAt: string
  updatedAt: string
}

export type FinancingType = 'cash' | 'mortgage' | 'mixed'
export type OfferStatus = 'pending' | 'accepted' | 'rejected' | 'countered' | 'withdrawn' | 'expired'

// Search types
export interface SearchFilters {
  listingType?: ListingType
  propertyType?: PropertyType
  city?: string
  minPrice?: number
  maxPrice?: number
  bedrooms?: number
  bathrooms?: number
  minSize?: number
  maxSize?: number
  checkIn?: string
  checkOut?: string
  guests?: number
  instantBook?: boolean
  sortBy?: 'price_asc' | 'price_desc' | 'newest' | 'rating'
  page?: number
  limit?: number
}

// Pagination
export interface PaginatedResponse<T> {
  data: T[]
  pagination: {
    page: number
    limit: number
    total: number
    totalPages: number
    hasNext: boolean
    hasPrev: boolean
  }
}

// Message types
export interface Message {
  id: string
  conversationId: string
  sender: User
  recipient: User
  property?: Property
  message: string
  isRead: boolean
  readAt?: string
  createdAt: string
}

export interface Conversation {
  id: string
  participant: User
  property?: Property
  lastMessage: Message
  unreadCount: number
  updatedAt: string
}

// Review types
export interface Review {
  id: string
  property?: Property
  reviewer: User
  reviewee?: User
  reviewType: 'property' | 'guest' | 'owner'
  rating: number
  title?: string
  comment: string
  cleanlinessRating?: number
  accuracyRating?: number
  communicationRating?: number
  locationRating?: number
  valueRating?: number
  response?: string
  responseAt?: string
  isVisible: boolean
  isVerified: boolean
  createdAt: string
}

// Notification types
export interface Notification {
  id: string
  type: NotificationType
  title: string
  message: string
  actionUrl?: string
  isRead: boolean
  readAt?: string
  createdAt: string
}

export type NotificationType = 
  | 'booking_request' 
  | 'booking_confirmed' 
  | 'booking_cancelled'
  | 'purchase_offer_received'
  | 'offer_accepted'
  | 'offer_countered'
  | 'message_received'
  | 'review_received'
  | 'payout_completed'
  | 'system'
