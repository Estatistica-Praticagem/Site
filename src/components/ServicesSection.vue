<template>
  <section id="services" class="section-services flex flex-center">
    <div class="section-wave-top">
      <svg width="100%" height="90" viewBox="0 0 1440 90" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,72 C320,110 1120,-10 1440,65 L1440,0 L0,0 Z" fill="#cecb3f"/>
      </svg>
    </div>

    <div class="section-content row items-center q-col-gutter-xl q-pb-xl">
      <!-- Imagem à direita -->
      <div class="col-12 col-md-6 order-md-last d-flex flex-center relative-position">
        <div class="image-container">
          <q-img src="~assets/OfferedServices.png" class="img-services" />
          <div class="pills-wrapper">
            <q-chip
              v-for="(b, idx) in servicePills"
              :key="'servicePill'+idx"
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

      <!-- Texto à esquerda -->
      <div class="col-12 col-md-6 q-mt-md q-mb-lg">
        <h2 class="text-h4 text-primary q-mb-md text-weight-bold">Serviços Detalhados</h2>
        <ul class="service-list">
          <li>Traqueamento de jornada, GTM, pixels e eventos.</li>
          <li>Sites e landing pages otimizadas.</li>
          <li>Data Warehouse integrado (CRM, mídia, vendas).</li>
          <li>Dashboards e reports automatizados.</li>
          <li>Análises de IA para marketing e vendas.</li>
          <li>Planejamento de campanhas e CRM.</li>
          <li>Conteúdo visual por parceiros especializados.</li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script setup>
const servicePills = [
  { icon: 'north_east', text: 'Aumente Suas Vendas' },
  { icon: 'handshake', text: 'Fidelize Seus Clientes' },
  { icon: 'grid_view', text: 'Otimize Seus Processos' },
];
</script>

<style scoped>
.section-services {
  position: relative;
  background: #fff;
  overflow: hidden;
  z-index: 1;
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
  margin: 0 auto;
  padding: 40px 20px;
}
.image-container {
  width: 100%;
  max-width: 420px;
  position: relative;
}
.img-services {
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
  border: 1.5px solid #2fca49;
  color: #097f99;
  box-shadow: 0 2px 8px rgba(4, 187, 111, 0.13);
  animation: floatY 7s ease-in-out infinite;
  pointer-events: none;
}
@keyframes floatY {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

.service-list {
  padding-left: 20px;
  list-style: none;
}
.service-list li {
  margin-bottom: 16px;
  font-size: 1.15rem;
  line-height: 1.6;
  letter-spacing: 0.3px;
  text-wrap: balance;
}
.service-list li::before {
  content: "•";
  color: #3e8097;
  font-weight: bold;
  margin-right: 8px;
  font-size: 1.3rem;
}

/* Responsivo */
@media (max-width: 1024px) {
  .section-content {
    flex-direction: column-reverse;
    padding: 20px 12px;
  }
}
</style>
