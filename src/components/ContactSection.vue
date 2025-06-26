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

        <!-- BLOCO 1: Esquerda (logo e slogan) -->
        <div class="footer-left col-12 col-md-4">
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

        <!-- BLOCO 2: Centro (só o botão de contato centralizado) -->
        <div class="footer-center col-12 col-md-4">
          <div class="text-subtitle2 text-white q-mb-sm">
            Contate‑nos diretamente:
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

        <!-- BLOCO 3: Direita (texto em cima, ícones lado a lado embaixo) -->
        <div class="footer-right col-12 col-md-4" id="footer-contato" data-gtm="footer-contato">
          <div class="footer-social-wrapper">
            <div class="footer-desc text-white q-mb-xs">
              Redes sociais:
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
              >
                <q-img :src="logo.src" :alt="logo.alt" class="footer-social-logo" />
              </q-btn>
            </div>
          </div>
        </div>

      </div>

      <!-- RODAPÉ inferior -->
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

/* BLOCO 1: Esquerda - Logo */
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

/* BLOCO 2: Centro - Botão de e-mail */
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

/* BLOCO 3: Direita - Redes sociais */
.footer-right {
  display: flex;
  flex-direction: column;
  padding-right: 36px; /* mais seguro que margin */
  box-sizing: border-box; /* garante que o padding não estoure a coluna */
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

/* Agora os ícones ficam lado a lado */
.footer-social-icons-row {
  display: flex;
  flex-direction: row; /* <- lado a lado */
  justify-content: center;
  gap: 12px;
  margin-top: 4px;
}

/* Ícone visual */
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
