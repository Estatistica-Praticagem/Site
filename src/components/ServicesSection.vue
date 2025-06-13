<template>
  <section id="services" class="section-services flex flex-center">
    <div class="section-wave-top">
      <svg width="100%" height="90" viewBox="0 0 1440 90" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,72 C320,110 1120,-10 1440,65 L1440,0 L0,0 Z" fill="#bdfadc"/>
      </svg>
    </div>
    <div class="section-content row items-center q-col-gutter-md">
      <div class="col-12 col-md-6 order-md-last relative-position">
        <q-img src="~assets/OfferedServices.png" style="width: 100%; max-width: 400px; height: auto;" />
        <q-chip
          v-for="(b, idx) in servicePills"
          :key="'servicePill'+idx"
          class="floating-pill text-body1"
          color="white"
          text-color="primary"
          :icon="b.icon"
          :style="{top: `${32 + idx * 62}px`}"
        >{{ b.text }}</q-chip>
      </div>
      <div class="col-12 col-md-6">
        <h2 class="text-h5 text-primary">Serviços Detalhados</h2>
        <ul class="text-body1">
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
  width: 100%;
  height: 80px;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 2;
  pointer-events: none;
}
.section-services .section-content {
  position: relative;
  z-index: 3;
}
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
