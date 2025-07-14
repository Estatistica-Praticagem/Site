<template>
  <section id="section-hero" class="section-hero flex flex-center">
    <div class="hero-content" id="hero-content">
      <h1 class="text-h3 text-weight-bold text-white" id="titulo-hero">Horizonte BI</h1>
      <p class="text-subtitle1 text-white q-mt-sm" id="subtitulo-hero">
        Global Solutions, Local Results
      </p>
      <p class="q-mt-md text-white" id="descricao-hero">
        Orizzontte BI is a full-service marketing consultancy with deep expertise in Business Intelligence.<br>
        We serve global clients from various sectors throughout the entire digital marketing cycle—from strategy and execution to performance optimization.<br><br>
        We also offer robust data solutions, smart dashboards, and comprehensive analyses—always tailored to each client's objective.<br>
        Our detailed reports and practical recommendations empower companies to make informed decisions that generate real and meaningful results.<br><br>
        <a href="#" @click.prevent="showCard = !showCard" class="leia-mais-link">
          {{ showCard ? 'Hide' : 'Read more' }}
        </a>
      </p>

      <transition name="fade-height">
        <div
          v-if="showCard"
          class="q-mt-md text-white text-body1 text-left leading extra-hero-content"
          id="conteudo-card"
        >
          As a data-driven company, we empower businesses by leveraging relevant and reliable information to evaluate current strategies and design new growth paths.<br><br>
          We use data as a strategic resource to drive informed decisions, optimize processes, improve performance, and unlock potential for sustainable growth.<br><br>
          Our approach is based on a culture that values data-driven insights at every stage of the business.<br>
          We offer the technologies, tools, and expertise needed to collect, store, analyze, and interpret large volumes of data efficiently, helping our clients transform information into actionable strategies that deliver results.
        </div>
      </transition>
    </div>
  </section>
</template>

<script setup>
// eslint-disable-next-line import/newline-after-import
import { ref } from 'vue';
const showCard = ref(false);
</script>

<style scoped>
.section-hero {
  min-height: 60vh;
  background: linear-gradient(135deg, #0b9dd3 0%, #0b4f76 100%);
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
  padding: 32px 16px;
  /* reduced padding on smaller screens */
}

.leading {
  line-height: 1.72;
}

.leia-mais-link {
  display: inline-block;
  margin-top: 12px;
  font-weight: 500;
  color: #ffeb3b;
  text-decoration: underline;
  cursor: pointer;
  transition: color 0.3s ease;
}

.leia-mais-link:hover {
  color: #fff59d;
}

/* Height and fade transition */
.fade-height-enter-active,
.fade-height-leave-active {
  transition: opacity .35s cubic-bezier(.4,0,.2,1), max-height .45s cubic-bezier(.4,0,.2,1), margin .35s;
  overflow: hidden;
}
.fade-height-enter-from,
.fade-height-leave-to {
  opacity: 0;
  max-height: 0;
  margin-top: 0 !important;
}
.fade-height-enter-to,
.fade-height-leave-from {
  opacity: 1;
  max-height: 800px; /* adjust as needed for your text */
  margin-top: 18px;
}

/* Responsiveness */
@media (max-width: 900px) {
  .hero-content {
    max-width: 95vw;
    padding: 28px 8px;
  }
  .section-hero {
    min-height: 50vh;
    padding: 0;
  }
  .text-h3 {
    font-size: 2.2rem;
  }
}
@media (max-width: 600px) {
  .hero-content {
    max-width: 100vw;
    padding: 18px 4px;
  }
  .text-h3 {
    font-size: 1.5rem;
  }
}
</style>
