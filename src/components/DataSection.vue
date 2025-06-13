<template>
  <section id="data" class="section-data flex flex-center">
    <!-- Onda decorativa no topo -->
    <div class="section-wave-top">
      <svg width="100%" height="90" viewBox="0 0 1440 90" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,72 C320,110 1120,-10 1440,65 L1440,0 L0,0 Z" fill="#47e295" />
      </svg>
      <div class="wave-title text-h5 text-primary text-center">habilidades</div>
    </div>

    <div class="section-content row items-center q-col-gutter-xl q-pb-xl">
      <!-- Imagem à esquerda -->
      <div class="col-12 col-md-6 d-flex flex-center relative-position">
        <div class="image-container">
          <q-img src="~assets/About the Company.png" class="img-data" />
          <div class="pills-wrapper">
            <q-chip
              v-for="(b, idx) in dataPills"
              :key="'dataPill'+idx"
              class="floating-pill"
              color="white"
              text-color="primary"
              :icon="b.icon"
              :style="{ top: `${32 + idx * 64}px` }"
            >
              {{ b.text }}
            </q-chip>
          </div>
        </div>
      </div>

      <!-- Texto à direita -->
      <div class="col-12 col-md-6 q-mt-md q-mb-lg">
        <h2 class="text-h4 text-primary q-mb-md text-weight-bold">Negócios Orientados a Dados</h2>
        <p class="text-body1">Utilizamos dados confiáveis para avaliar estratégias atuais e desenhar caminhos de crescimento.</p>
        <p class="text-body1">Nossa cultura valoriza insights em cada ciclo, transformando informação em estratégia.</p>
      </div>
    </div>
  </section>
</template>

<script setup>
const dataPills = [
  { icon: 'visibility', text: 'Aumente Sua Visibilidade' },
  { icon: 'diversity_1', text: 'Engaje Seu Público' },
  { icon: 'trending_up', text: 'Melhore Sua Estratégia' },
];
</script>

<style scoped>
.section-data {
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
  z-index: 5;
  pointer-events: none;
}
.wave-title {
  position: absolute;
  top: 16px;
  left: 50%;
  transform: translateX(-50%);
}

.section-content {
  position: relative;
  z-index: 3;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.image-container {
  width: 100%;
  max-width: 420px;
  position: relative;
}

.img-data {
  width: 100%;
  border-radius: 24px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
}

.pills-wrapper {
  position: relative;
  min-height: 180px;
}

.floating-pill {
  position: absolute;
  left: 16px;
  padding: 8px 16px;
  font-size: 1.05rem;
  font-weight: 500;
  border-radius: 999px;
  white-space: normal;
  background: #fff;
  border: 1.5px solid #bdfadc;
  color: #097f99;
  box-shadow: 0 2px 8px rgba(175, 223, 203, 0.13);
  animation: floatY 7s ease-in-out infinite;
  pointer-events: none;
}

@keyframes floatY {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

@media (max-width: 1024px) {
  .section-content {
    flex-direction: column-reverse;
    padding: 20px 12px;
  }
}
</style>
