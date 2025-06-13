<template>
  <section id="clients" class="section-clients flex flex-center">
    <!-- Onda decorativa -->
    <div class="section-wave-top">
      <svg width="100%" height="90" viewBox="0 0 1440 90" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,72 C320,110 1120,-10 1440,65 L1440,0 L0,0 Z" fill="#f9e9dd" />
      </svg>
    </div>

    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md">Grandes Clientes</h2>

      <q-tabs
        v-model="clientTab"
        class="q-mt-md text-primary bg-transparent"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        narrow-indicator
      >
        <q-tab
          v-for="c in clientCards"
          :key="c.id"
          :name="c.id"
          :label="c.tabLabel"
          class="text-subtitle2 text-weight-medium"
        />
      </q-tabs>

      <q-tab-panels v-model="clientTab" animated class="q-mt-xl">
        <q-tab-panel
          v-for="c in clientCards"
          :key="c.id"
          :name="c.id"
          class="q-pa-none"
        >
          <div class="row q-col-gutter-xl items-center">
            <div class="col-12 col-md-5">
              <component
                :is="c.isVideo ? 'video' : 'q-img'"
                :src="c.img"
                :alt="c.name"
                v-bind="c.isVideo ? { controls: true, autoplay: true, muted: true, loop: true } : {}"
                class="client-media"
              />
            </div>
            <div class="col-12 col-md-7">
              <p class="text-primary text-bold text-subtitle1 q-mb-sm">{{ c.name }}</p>
              <p class="text-body1 q-mb-md">"{{ c.quote }}"</p>
              <h5 class="text-subtitle1 q-mb-xs">
                {{ c.person }} |
                <span class="text-primary">{{ c.name }}</span>
              </h5>
              <p class="text-caption text-grey-8">{{ c.personRole }}</p>
              <q-btn
                v-if="c.link"
                color="primary"
                label="Ler sobre o Case"
                :href="c.link"
                target="_blank"
                class="q-mt-sm"
              />
            </div>
          </div>
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const clientTab = ref('rg');
const clientCards = [
  {
    id: 'rg',
    tabLabel: 'Rio Grande',
    name: 'Port of Rio Grande',
    quote: 'Sistema de marés e correntes em tempo real com IA.',
    person: 'Capitão Pilot Board',
    personRole: 'Harbor Operations',
    // eslint-disable-next-line global-require
    img: require('src/assets/clients/RGPilots.png'),
    link: '#',
    isVideo: false,
  },
  {
    id: 'vm',
    tabLabel: 'Via Marte',
    name: 'Via Marte',
    quote: 'Automação de mídia para e-commerce com crescimento de 40 %.',
    person: 'Equipe Growth',
    personRole: 'Marketing Digital',
    // eslint-disable-next-line global-require
    img: require('src/assets/graphics.png'),
    link: '#',
    isVideo: false,
  },
  {
    id: 'ar',
    tabLabel: 'Agency Rfill',
    name: 'Agency Rfill',
    quote: 'Dashboards de leads de imóveis de luxo em tempo real.',
    person: 'Diretoria de Vendas',
    personRole: 'Imobiliário Premium',
    // eslint-disable-next-line global-require
    img: require('src/assets/videos/minedashboard.mp4'),
    link: '#',
    isVideo: true,
  },
];
</script>

<style scoped>
.section-clients {
  position: relative;
  background: #fff;
  overflow: hidden;
  z-index: 1;
  padding-top: 90px;
}

.section-wave-top {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 80px;
  z-index: 2;
  pointer-events: none;
}

.section-content {
  position: relative;
  z-index: 3;
  width: 100%;
  max-width: 1200px;
  padding: 40px 20px;
  margin: 0 auto;
}

.client-media {
  width: 100%;
  max-width: 680px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
  display: block;
}
</style>
