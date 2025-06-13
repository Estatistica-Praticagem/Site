<template>
  <section class="hero-video-section relative-position">
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

    <div class="hero-content q-pa-xl text-white">
      <h3 class="text-h4 text-weight-bold">
        <span class="gradiente">Marketing</span> só faz sentido se você vender
      </h3>
      <p class="text-subtitle1 q-mt-sm">
        E nós somos especialistas em transformar o conteúdo no seu principal aliado para vender mais, melhor e com continuidade.
      </p>

      <div class="q-mt-lg row q-col-gutter-md">
        <div class="col-auto">
          <q-btn color="" label="Soluções em Marketing" to="#marketing" />
        </div>
        <div class="col-auto">
          <q-btn color="" label="Soluções em Vendas" to="#vendas" />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import dashboardVideo from 'assets/videos/heroabistract.mp4';

const videoSrc = computed(() => dashboardVideo);
</script>

<style scoped>
.hero-video-section {
  position: relative;
  height: 600px; /* Mantém altura semelhante ao seu print */
  min-height: 350px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #000; /* Garantia de fundo escuro se o vídeo não carregar */
}

.bg-video {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  color: #fff;
  padding: 48px 16px 32px 16px;
}

.hero-content h3 {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.1;
  margin-bottom: 16px;
}

.hero-content .gradiente {
  color: #f950ae;
  background: linear-gradient(90deg, #f950ae 0%, #ff7bc7 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 800;
}

.hero-content p {
  font-size: 1.1rem;
  margin-bottom: 32px;
  color: #f4f4f4;
}

.hero-content .q-btn {
  min-width: 230px;
  margin: 0 10px 10px 0;
  font-weight: 600;
  font-size: 1.05rem;
  border-radius: 100px;
  box-shadow: 0 3px 0 #f950ae40;
}

.hero-content .q-btn:last-child {
  background: #161616;
  color: #fff;
  border: 1.5px solid #f950ae;
}

@media (max-width: 600px) {
  .hero-content h3 { font-size: 2rem; }
  .hero-content { padding: 28px 8px 16px 8px; }
  .hero-video-section { height: 380px; }
}

</style>
