<template>
  <section id="footer" class="section-footer" data-gtm="section-footer">
    <div class="footer-container">

      <!-- Centered mission at the top -->
      <div class="footer-mission text-center q-mb-xl">
        <div class="text-h6 text-white text-weight-bold q-mb-xs">Mission</div>
        <div class="text-caption text-white" style="max-width:360px;margin:0 auto;">
          Transforming data into real results for our clients
        </div>
      </div>

      <div class="footer-main row q-col-gutter-md items-start justify-center">

        <!-- BLOCK 1: Left (logo and slogan) -->
        <div class="footer-left col-12 col-md-4">
          <div class="row items-center no-wrap">
            <q-img
              src="~assets/icons/icon.png"
              alt="Horizonte BI Logo"
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
        </div>

        <!-- BLOCK 2: Center (just the contact button, centered) -->
        <div class="footer-center col-12 col-md-4">
          <div class="text-subtitle2 text-white q-mb-sm">
            Contact us directly:
          </div>
          <q-btn
            outline
            color="white"
            icon="email"
            label="horizontebi.contato@gmail.com"
            class="footer-contact-btn"
            @click="mailto"
            id="btn-footer-email"
            data-gtm="btn-footer-email"
          />
        </div>

        <!-- BLOCK 3: Right (social media) -->
        <div class="footer-right col-12 col-md-4" id="footer-contato" data-gtm="footer-contato">
          <div class="footer-social-wrapper">
            <div class="footer-desc text-white q-mb-xs">
              Social media:
            </div>
            <div class="footer-social-icons-row">
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
                :id="`btn-social-${logo.alt.toLowerCase().replace(/\s+/g, '-')}`"
                :data-gtm="`btn-social-${logo.alt.toLowerCase().replace(/\s+/g, '-')}`"
              >
                <q-img :src="logo.src" :alt="logo.alt" class="footer-social-logo" />
              </q-btn>
            </div>
          </div>
        </div>

      </div>

      <!-- BOTTOM FOOTER -->
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
  window.open('mailto:horizontebi.contato@gmail.com', '_blank');
}

const partnerLogos = [
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
  background: linear-gradient(135deg, #0b9dd3 0%, #0b4f76 100%);
  padding: 48px 24px 24px;
  font-size: 0.9rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.footer-mission {
  margin-bottom: 38px;
}

/* BLOCK 1: Left - Logo */
.footer-left {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
@media (min-width: 901px) {
  .footer-left {
    align-items: flex-start;
    text-align: left;
  }
}

.footer-logo {
  width: 72px;
  height: 72px;
}

/* BLOCK 2: Center - Email button */
.footer-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.footer-contact-btn {
  font-size: 0.9rem;
  text-transform: none;
  padding: 10px 20px;
}

/* BLOCK 3: Right - Social media */
.footer-right {
  display: flex;
  flex-direction: column;
  padding-right: 36px;
  box-sizing: border-box;
  align-items: center;
  text-align: center;
}
@media (min-width: 901px) {
  .footer-right {
    align-items: flex-end;
    text-align: right;
  }
}

.footer-social-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.footer-social-icons-row {
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 12px;
  margin-top: 4px;
}

.footer-social-btn {
  padding: 4px;
}

.footer-social-logo {
  width: 46px;
  height: 46px;
  object-fit: contain;
  transition: filter 0.3s ease;
}

.footer-social-logo:hover {
  filter: brightness(1.2);
}
</style>
