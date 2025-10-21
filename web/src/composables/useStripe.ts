import { ref, onMounted } from 'vue'
import { loadStripe, type Stripe, type StripeElements } from '@stripe/stripe-js'

const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY)

export function useStripe() {
  const stripe = ref<Stripe | null>(null)
  const elements = ref<StripeElements | null>(null)

  onMounted(async () => {
    stripe.value = await stripePromise
  })

  const createElements = (clientSecret: string) => {
    if (stripe.value) {
      elements.value = stripe.value.elements({ clientSecret })
    }
  }

  const mountPaymentElement = (selector: string) => {
    if (elements.value) {
      const paymentElement = elements.value.create('payment')
      paymentElement.mount(selector)
      return paymentElement
    }
  }

  const confirmPayment = async (redirectUrl: string) => {
    if (stripe.value && elements.value) {
      const { error } = await stripe.value.confirmPayment({
        elements: elements.value,
        confirmParams: {
          return_url: redirectUrl,
        },
      })
      return { error }
    }
    return { error: { message: 'Stripe nije inicijalizovan.' } }
  }

  return {
    stripe,
    elements,
    createElements,
    mountPaymentElement,
    confirmPayment,
  }
}
