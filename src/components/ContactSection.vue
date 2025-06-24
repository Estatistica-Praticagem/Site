<template>
  <section id="footer" class="section-footer" data-gtm="section-footer">
    <div class="footer-container">

      <!-- Missão centralizada no topo -->
      <div class="footer-mission text-center q-mb-xl">
        <div class="text-h6 text-white text-weight-bold q-mb-xs">Missão</div>
        <div class="text-caption text-white" style="max-width:360px;margin:0 auto;">
          Transformar dados em resultados concretos para nossos clientes
        </div>
      </div>

      <div class="footer-main row q-col-gutter-md items-start justify-center">

        <!-- BLOCO 1: Esquerda -->
        <div class="footer-left col-12 col-md-4 flex flex-column items-center items-md-start text-center text-md-left">
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
        </div>

        <!-- BLOCO 2: Centro -->
        <div class="footer-center col-12 col-md-4 flex flex-column items-center text-center q-mt-md q-mt-none--md">
          <!-- Deixe vazio ou adicione mais algo aqui -->
        </div>

        <!-- BLOCO 3: Direita -->
        <div
          class="footer-right col-12 col-md-4 flex flex-column items-center items-md-end text-center text-md-right q-mt-md q-mt-none--md"
          id="footer-contato"
          data-gtm="footer-contato"
        >
          <div class="text-subtitle2 text-white q-mb-sm">
            Contate‑nos diretamente:
          </div>
          <q-btn
            outline
            color="white"
            icon="email"
            label="horizontetbi@gmail.com"
            class="footer-contact-btn"
            @click="mailto"
            id="btn-footer-email"
            data-gtm="btn-footer-email"
          />
          <div class="footer-desc text-white q-mt-lg q-mb-xs">
            Redes sociais:
          </div>
          <div class="footer-social row q-mt-xs justify-center justify-md-end" id="footer-social" data-gtm="footer-social-icons">
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
@media (max-width: 900px) {
  .footer-main {
    flex-direction: column !important;
  }
  .footer-left,
  .footer-center,
  .footer-right {
    align-items: center !important;
    text-align: center !important;
    margin-bottom: 22px;
  }
}
</style>
