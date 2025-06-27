import { createI18n } from 'vue-i18n';
import messages from 'src/i18n/messages';

// Prioridade: localStorage > navegador > 'en'
const browserLang = (navigator.language || navigator.userLanguage || '').substring(0, 2);
const savedLang = localStorage.getItem('lang');
const defaultLang = savedLang || (['en', 'pt'].includes(browserLang) ? browserLang : 'en');

export default ({ app }) => {
  const i18n = createI18n({
    legacy: false,
    locale: defaultLang,
    fallbackLocale: 'en',
    messages,
  });

  app.use(i18n);
};
