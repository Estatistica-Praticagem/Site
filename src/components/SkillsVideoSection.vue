<template>
  <section
    id="skills-expertise"
    data-gtm="skills-expertise"
    class="skills-video-section relative-position"
  >
    <video
      autoplay
      muted
      loop
      playsinline
      class="absolute-full bg-video"
    >
      <source :src="videoSrc" type="video/mp4" />
      Seu navegador não suporta vídeos em HTML5.
    </video>

    <div class="skills-content row items-center q-col-gutter-md">
      <div class="col-12 col-md-6">
        <h2 class="text-h5 text-white">Habilidades e Expertise</h2>
        <ul class="text-body1 text-white">
          <li>Análise de dados e insights para decisões em todas as fases do marketing.</li>
          <li>Processo ETL: múltiplas fontes, dados limpos e confiáveis.</li>
          <li>Pipelines SQL, DWs e dashboards interativos.</li>
          <li>Automação com Inteligência Artificial.</li>
        </ul>
      </div>
      <div class="col-12 col-md-6 relative-position">
        <q-img
          src="~assets/Skills&Expertise.png"
          style="width: 100%; max-width: 400px; height: auto;"
        />
        <q-chip
          v-for="(b, idx) in skillPills"
          :key="'skillPill'+idx"
          class="floating-pill text-body1"
          color="white"
          text-color="primary"
          :icon="b.icon"
          :style="{ top: `${32 + idx * 62}px` }"
        >
          {{ b.text }}
        </q-chip>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import dashboardVideo from 'assets/videos/dashboard.mp4';

const videoSrc = computed(() => dashboardVideo);

const skillPills = [
  { icon: 'show_chart', text: 'Power BI & Analytics' },
  { icon: 'integration_instructions', text: 'Integração Total' },
  { icon: 'lightbulb', text: 'IA em Vendas' },
];
</script>

<style scoped>
.skills-video-section {
  position: relative;
  height: auto;
  min-height: 550px;
  overflow: hidden;
  padding: 48px 16px;
  background: #000;
}

.bg-video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
  filter: brightness(0.3);
  pointer-events: none;
}

.skills-content {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.floating-pill {
  position: absolute;
  left: 16px;
  min-width: 230px;
  animation: floatY 7s ease-in-out infinite;
  pointer-events: none;
  box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.12);
}

@keyframes floatY {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@media (max-width: 600px) {
  .skills-video-section {
    padding: 24px 8px;
  }
}
</style>
