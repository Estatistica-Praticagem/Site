<!-- eslint-disable vue/no-unused-vars -->
<template>
  <section
    id="clients"
    data-gtm="clients-section"
    :class="['section-clients', `${clientTab}-bg`]"
  >
    <div class="section-inner">
      <h2 class="section-title text-center text-h4 q-mb-md">Grandes Clientes</h2>

      <q-tabs
        v-model="clientTab"
        class="clients-tabs q-mt-md bg-transparent"
        active-color="primary"
        indicator-color="primary"
        align="justify"
        narrow-indicator
      >
        <q-tab
          v-for="(c, idx) in clientCards"
          :key="c.id"
          :id="`tab-${c.id}`"
          :data-gtm="`tab-${c.id}`"
          :name="c.id"
          :label="c.tabLabel"
          class="text-subtitle2 text-weight-medium"
        />
      </q-tabs>

      <q-tab-panels v-model="clientTab" animated class="transparent-panel q-mt-xl">
        <q-tab-panel
          v-for="(c, idx) in clientCards"
          :key="c.id"
          :name="c.id"
          :id="`panel-${c.id}`"
          :data-gtm="`panel-${c.id}`"
          class="q-pa-none"
        >
          <div class="row items-center q-col-gutter-xl">
            <div class="col-12 col-md-7">
              <div class="client-info">
                <div class="client-header flex items-center q-mb-sm">
                  <q-img
                    :src="c.icon || c.img"
                    class="client-icon"
                    :alt="`${c.name} icon`"
                    width="40"
                    height="40"
                    no-spinner
                  />
                  <h3 class="client-name">{{ c.name }}</h3>
                </div>

                <p class="client-quote">"{{ c.quote }}"</p>
                <div class="client-description">
  <p v-for="(paragraph, i) in c.description" :key="i">{{ paragraph }}</p>
</div>

                <p class="client-person q-mt-md">
                  {{ c.person }}
                  <span v-if="c.personRole">
                    | <span class="client-role">{{ c.personRole }}</span>
                  </span>
                </p>
              </div>
            </div>

            <div class="col-12 col-md-5 text-center">
              <component
                :is="c.isVideo ? 'video' : 'q-img'"
                :src="c.img"
                :alt="c.name"
                v-bind="c.isVideo
                  ? { controls: true, autoplay: true, muted: true, loop: true }
                  : {}"
                class="client-media"
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
import rgIcon from 'src/assets/clients/logo-rgpilots.png';
import viaMarteIcon from 'src/assets/clients/viamarteicon.png';
import rfillIcon from 'src/assets/clients/rfill.png';
import unisinosIcon from 'src/assets/clients/unisinos.jpeg';
import rgImg from 'src/assets/image/pilots.png';
import viaMarteImg from 'src/assets/image/viamarte.png';
import rfillImg from 'src/assets/image/Refillfundotransparente.png';
import unisinosImg from 'src/assets/image/Unisinosgpt.png';

const clientTab = ref('rg');

const clientCards = [
  {
    id: 'rg',
    tabLabel: 'Rio Grande',
    name: 'Rio Grande International Port',
    quote: 'Real-time tide and current system with AI.',
    description: [
      'Rio Grande International Port.',
      'We developed a complete system to collect climate data from different sources and sensors, training and applying machine learning models to predict tides and currents.',
      'Our system delivers real-time predictions 24/7 to enhance port safety, achieving a high degree of accuracy and reliability.',
      'Since 2020.',
    ],
    person: 'Port Pilotage',
    personRole: 'Port Operations',
    img: rgImg,
    icon: rgIcon,
    isVideo: false,
  },
  {
    id: 'unisinos',
    tabLabel: 'Unisinos',
    name: 'University of Vale do Rio dos Sinos (Unisinos)',
    quote: 'Real-time dashboard.',
    description: [
      'Horizonte BI established a partnership with Unisinos to set up customer journey tracking from scratch — from clearing old settings, mapping needs, and configuring tools like Google Tag Manager, pixels, tags, and buttons to track user behavior at each step of navigation.',
      'After organizing and configuring Unisinos’ site tracking ecosystem, we created databases via BigQuery and Google Sheets to process media ads.',
      'The information was made available through an interactive dashboard using Unisinos’ custom taxonomy.',
      'The result was a complete ecosystem for tracking, storage, and data analysis to support real-time decision-making.',
    ],
    person: 'Unisinos Team',
    personRole: 'Marketing & Technology',
    img: unisinosImg,
    icon: unisinosIcon,
    isVideo: false,
  },
  {
    id: 'vm',
    tabLabel: 'RBA + Via Marte',
    name: 'Via Marte',
    quote: 'Media automation boosting sales by 40%.',
    description: [
      'Every e-commerce business needs constant visibility into its data to support decision-making.',
      'In partnership with RBA+, we developed a Data Warehouse and Dashboard for Via Marte — one of the largest footwear e-commerce platforms in southern Brazil.',
      'We automated data collection from media platforms, organic traffic, and the site, with visualizations of goals, KPIs, and campaign performance.',
      'The data is updated daily, enabling continuous analysis and quick decisions to maximize sales.',
    ],
    person: '',
    personRole: 'Digital Marketing',
    img: viaMarteImg,
    icon: viaMarteIcon,
    isVideo: false,
  },
  {
    id: 'ar',
    tabLabel: 'Agency Rfill',
    name: 'Agency Rfill',
    quote: 'Real-time leads for luxury properties.',
    description: [
      'Rfill aimed to optimize its digital marketing focused on luxury real estate.',
      'We automated data collection from paid media platforms and forms, ensuring greater reliability in analyses.',
      'We created centralized databases with taxonomy for campaigns, channels, creatives, and property types.',
      'Interactive dashboards focused on lead acquisition helped drive faster and more effective decision-making.',
    ],
    person: 'Sales Team',
    personRole: 'High-End Real Estate Market',
    img: rfillImg,
    icon: rfillIcon,
    isVideo: false,
  },
];

</script>

<style scoped>
.section-clients {
  padding: 80px 0 60px;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
  transition: background 0.5s ease;
}
.section-inner {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 24px;
}
.section-title {
  margin-bottom: 36px;
  font-weight: 700;
  color: var(--title-color, #fff);
}
.clients-tabs .q-tab__label {
  color: var(--tab-color, #fff) !important;
}
.rg-bg {
  background: linear-gradient(135deg, #f52f49 0%, #f7f8f8 70%);
  --title-color: #f1e9e9;
  --tab-color: #003b73;
}
.vm-bg {
  background: linear-gradient(135deg, #904d92 50%, #0c0c0c 100%);
  --title-color: #ffd4da;
  --tab-color: #ffd4da;
}
.ar-bg {
  background: linear-gradient(135deg, #1d1d1d -20%, #ffffff 140%);
  --title-color: #ffffff;
  --tab-color: #ffffff;
}
.unisinos-bg {
  background: linear-gradient(135deg, #7536a1 0%, #3d3d83 100% );
  --title-color: #e4f0fc;
  --tab-color: #e4f0fc;
}
/* ---------- Textos específicos ------------------------------*/
.rg-bg .client-name        {color:#022546;}
.rg-bg .client-quote       {color:#01080e;font-style:italic;}
.rg-bg .client-description {color:#010911;}
.rg-bg .client-person,
.rg-bg .client-role        {color:#001E40;}

.vm-bg .client-name        {color:#FFD4DA;}
.vm-bg .client-quote       {color:#FFABB4;font-style:italic;}
.vm-bg .client-description {color:#FFCDD3;}
.vm-bg .client-person,
.vm-bg .client-role        {color:#FFE6EA;}

.ar-bg .client-name        {color:#FFFFFF;}
.ar-bg .client-quote       {color:#DDDDDD;font-style:italic;}
.ar-bg .client-description {color:#F2F2F2;}
.ar-bg .client-person,
.ar-bg .client-role        {color:#CCCCCC;}

.unisinos-bg .client-name        {color:#e4f0fc;}
.unisinos-bg .client-quote       {color:#bed5ec;font-style:italic;}
.unisinos-bg .client-description {color:#eef0f3;}
.unisinos-bg .client-person,
.unisinos-bg .client-role        {color:#180429;}

/* ---------- Estrutura dos cartões ---------------------------*/
.clients-tabs,
.transparent-panel,
.q-tab-panel{background:none !important;}

.client-icon{width:40px;height:40px;object-fit:contain;margin-right:12px;}
.client-name{font-size:1.8rem;font-weight:700;}
.client-quote{font-size:1.125rem;margin-bottom:8px;}
.client-description{font-size:1rem;line-height:1.5;margin-bottom:12px;}
.client-person{font-weight:600;}

.client-media{
  width:100%;
  max-width:340px;
  height:auto;
  border-radius:16px;
  box-shadow:0 6px 24px rgba(0,0,0,.1);
  object-fit:contain;
}
.client-icon {
  width: 40px;
  height: 40px;
  object-fit: contain;
  margin-right: 12px;
}
.client-name {
  font-size: 1.8rem;
  font-weight: 700;
}
.client-quote {
  font-size: 1.125rem;
  margin-bottom: 8px;
  font-style: italic;
}
.client-description {
  font-size: 1rem;
  line-height: 1.5;
  margin-bottom: 12px;
}
.client-person {
  font-weight: 600;
}
.client-media {
  width: 100%;
  max-width: 340px;
  height: auto;
  border-radius: 16px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
  object-fit: contain;
}
@media (max-width: 1023px) {
  .client-name {
    font-size: 1.4rem;
  }
}
@media(max-width:1023px){.client-name{font-size:1.4rem;}}
</style>
