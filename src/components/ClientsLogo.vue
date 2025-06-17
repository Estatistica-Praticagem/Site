<template>
  <section id="footer" class="section-footer" data-gtm="section-footer">
    <div class="footer-container">
      <div class="footer-main row q-col-gutter-md items-start justify-between">
          <div class="footer-social row q-mt-md" id="footer-social" data-gtm="footer-social-icons">
            <q-btn
              v-for="logo in partnerLogos"
              :key="logo.alt"
              flat
              round
              dense
              tag="a"
              :href="logo.link"
              target="_blank"
              class="footer-social-btn"
              :id="`social-${logo.alt.toLowerCase()}`"
              :data-gtm="`social-${logo.alt.toLowerCase()}`"
            >
              <q-img :src="logo.src" :alt="logo.alt" class="footer-social-logo" />
            </q-btn>
          </div>
      </div>
    </div>
  </section>
</template>

<script setup>

const partnerLogos = [
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/icons/Google.svg'),
    alt: 'Google',
    link: 'https://www.google.com/partners/',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/icons/Facebook.svg'),
    alt: 'Facebook',
    link: 'https://www.facebook.com/business/partner-directory',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/icons/Instagram.svg'),
    alt: 'Instagram',
    link: 'https://www.instagram.com/',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/icons/Linkedin.svg'),
    alt: 'LinkedIn',
    link: 'https://www.linkedin.com/company/orizzonttebi/about/',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/fr-imoveis.png'),
    alt: 'Rfill',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/logo-rgpilots.png'),
    alt: 'Praticagem',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/unisinos.jpeg'),
    alt: 'Unisinos',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/viamarte.png'),
    alt: 'Via Marte',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/edenred.png'),
    alt: 'Edenred',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/oakberry.svg'),
    alt: 'Oakberry',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/Stihl_logo.png'),
    alt: 'Stihl',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/tke_logo.jpg'),
    alt: 'TKE',
    link: '',
  },
  {
    // eslint-disable-next-line global-require
    src: require('src/assets/clients/DreamLand.png'),
    alt: 'DreamLand',
    link: '',
  },
];
</script>

<style scoped>
.section-footer {
  background: linear-gradient(135deg, #47e295 0%, #0b4f76 100%);
  padding: 48px 24px 24px;
  font-size: 0.9rem;
}
.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}
.footer-logo {
  width: 72px;
  height: 72px;
}
.footer-desc {
  font-size: 1rem;
}
.footer-social {
  gap: 12px;
}
.footer-social-btn {
  padding: 4px;
}
.footer-social-logo {
  width: 36px;
  height: 36px;
  object-fit: contain;
  transition: filter 0.3s ease;
}
.footer-social-logo:hover {
  filter: brightness(1.2);
}
.footer-contact-btn {
  font-size: 0.9rem;
  text-transform: none;
  padding: 10px 20px;
}
.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 16px;
  font-size: 0.75rem;
}
</style>
