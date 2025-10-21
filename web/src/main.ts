import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'

// Import PrimeVue styles
import 'primevue/resources/themes/lara-light-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
// Import Tailwind CSS
import './assets/main.css'

async function main() {
  async function initApp() {
    const app = createApp(App);

    app.use(createPinia());

    // Initialize auth store and fetch user before mounting
    const authStore = useAuthStore();
    if (localStorage.getItem('token')) {
      await authStore.fetchUser();
    }

    app.use(router);
    app.use(PrimeVue, { ripple: true });
    app.use(ToastService);
    app.use(ConfirmationService);

    app.mount('#app');
  }

  await initApp();
}

main();
