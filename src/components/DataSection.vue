<template>
  <section id="data" class="section-data flex flex-center">
    <div class="section-content row items-center q-col-gutter-md relative-position">
      <div class="col-12 col-md-6">
        <q-img src="~assets/About the Company.png" style="width: 100%; max-width: 450px; height: auto;" />
        <!-- BALÕES SUSPENSOS -->
        <q-chip
          v-for="(b, idx) in dataPills"
          :key="'dataPill'+idx"
          class="floating-pill text-body1"
          color="white"
          text-color="primary"
          :icon="b.icon"
          :style="{top: `${32 + idx * 62}px`}"
        >{{ b.text }}</q-chip>
      </div>
      <div class="col-12 col-md-6">
        <h2 class="text-h5 text-primary">Negócios Orientados a Dados</h2>
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
.section-data     { background: #f7e5d8; }
.floating-pill{
  position:absolute; left:12px;
  min-width: 235px;
  font-size:1.1rem;
  animation:floatY 7s ease-in-out infinite;
  pointer-events:none;
  box-shadow:0 2px 10px 0 rgba(0,0,0,0.12);
}
@keyframes floatY { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
</style>
