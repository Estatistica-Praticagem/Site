import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import App from './App.vue';
import messages from './i18n/messages';

const i18n = createI18n({
  legacy: false, // Se você usa composition API
  locale: 'pt', // Idioma inicial
  messages,
});

const app = createApp(App);
app.use(i18n);
app.mount('#app');
