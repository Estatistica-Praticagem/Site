<template>
  <section id="footer" class="section-footer" data-gtm="section-footer">
    <div class="footer-container">
      <div class="footer-main row q-col-gutter-md items-start justify-between">

        <!-- ESQUERDA: logo + texto + social -->
        <div class="footer-left col-12 col-md-6" id="footer-logo-social" data-gtm="footer-logo-social">
          <div class="row items-center no-wrap">
            <q-img
              src="~assets/icons/icon.png"
              alt="Logo Horizonte BI"
              class="footer-logo"
              spinner-color="primary"
              fit="contain"
              id="footer-logo"
              data-gtm="footer-logo"
            />
            <div class="q-ml-md">
              <div class="text-h6 text-white">Horizonte BI</div>
              <div class="text-caption text-white q-mt-xs">Global Solutions, Local Results</div>
            </div>
          </div>
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

        <!-- DIREITA: contato -->
        <div
          class="footer-right col-12 col-md-6 text-md-right text-center q-mt-md q-mt-none--md"
          id="footer-contato"
          data-gtm="footer-contato"
        >
          <q-btn
            outline
            color="white"
            icon="email"
            label="E-mail: horizontetbi@gmail.com"
            class="footer-contact-btn"
            @click="mailto"
            id="btn-footer-email"
            data-gtm="btn-footer-email"
          />
        </div>
      </div>

      <!-- INFERIOR: direitos autorais + localização -->
      <div class="footer-bottom row justify-between items-center q-mt-lg" id="footer-bottom" data-gtm="footer-bottom">
        <div class="text-caption text-white">
          © 2025 Horizonte BI
        </div>
        <div class="text-caption text-white">
          Headquartered in Brazil
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
function mailto() {
  window.open('mailto:horizontetbi@gmail.com', '_blank');
}

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
