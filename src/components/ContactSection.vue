<template>
  <section id="footer" class="section-footer flex flex-center">
    <div class="footer-content q-relative">
      <!-- Logo + Slogan -->
      <div class="row justify-between items-center q-mb-lg">
        <div class="row items-center no-wrap">
          <q-img
            src="~assets/icons/icon.png"
            alt="Logo Horizonte BI"
            class="q-mr-md"
            style="width: 86px; height: 76px;"
            spinner-color="primary"
            fit="contain"
          />
          <div>
            <div class="text-h6 text-white q-mb-xs" style="letter-spacing: 1px;">Horizonte BI</div>
            <div class="text-caption text-white">Global Solutions, Local Results</div>
          </div>
        </div>
        <!-- Headquarter info -->
        <div class="text-caption text-white text-right q-mt-sm q-mr-sm hidden-xs-only">
          Headquartered in Brazil
        </div>
      </div>
           <!-- Contato -->
      <div class="row justify-center q-mt-lg">
        <q-btn
          outline
          color="white"
          icon="email"
          label="E-mail: horizontetbi@gmail.com"
          style="text-transform: none"
          @click="mailto"
        />
      </div>

      <!-- Parceiros -->
      <div class="row justify-center items-center icons-row">
        <q-btn
          v-for="logo in partnerLogos"
          :key="logo.alt"
          flat
          round
          dense
          tag="a"
          :href="logo.link"
          target="_blank"
          class="partner-btn"
        >
          <q-img :src="logo.src" :alt="logo.alt" class="partner-logo" />
        </q-btn>
      </div>

      <!-- Direitos -->
      <div class="text-caption text-white text-center q-mt-md">
        © 2025 Horizonte BI
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
  background: linear-gradient(135deg, #0b4f76 0%, #47e295 100%);
  padding: 60px 24px;
}
.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}
.icons-row {
  margin-top: 16px;
  flex-wrap: wrap;
}
.partner-btn {
  margin: 8px 12px;
  padding: 8px;
  border-radius: 50%;
}
.partner-logo {
  width: 48px;
  height: 48px;
  object-fit: contain;
  transition: filter 0.3s ease;
}
.partner-logo:hover {
  filter: brightness(1.2);
}
</style>
