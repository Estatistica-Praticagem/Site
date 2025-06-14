// eslint-disable-next-line no-unused-vars
export default async ({ app }) => {
  if (typeof window !== 'undefined') {
    // Cria a camada de dados se não existir
    window.dataLayer = window.dataLayer || [];

    // Insere o script do GTM (head)
    (function (w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
      const f = d.getElementsByTagName(s)[0];
      const j = d.createElement(s);
      const dl = l !== 'dataLayer' ? `&l=${l}` : '';
      j.async = true;
      j.src = `https://www.googletagmanager.com/gtm.js?id=${i}${dl}`;
      f.parentNode.insertBefore(j, f);
    }(window, document, 'script', 'dataLayer', 'GTM-PVGTFMLL'));
  }
};
