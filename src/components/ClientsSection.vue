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
                <p class="client-description">{{ c.description }}</p>

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
import rgImg from 'src/assets/clients/pilots.png';
import viaMarteImg from 'src/assets/clients/viamarte.png';
import rfillVideo from 'src/assets/clients/rfill.png';
import unisinosImg from 'src/assets/clients/unisinos.jpeg';

const clientTab = ref('rg');

const clientCards = [
  {
    id: 'rg',
    tabLabel: 'Rio Grande',
    name: 'Porto Internacional do Rio Grande',
    quote: 'Sistema de marés e correntes em tempo real com IA.',
    description:
      'Desenvolvemos um sistema completo para coletar dados climáticos de diferentes fontes e sensores, treinando e implantando modelos de machine learning para prever marés e correntes.',
    person: 'Capitão Pilot Board',
    personRole: 'Operações Portuárias',
    img: rgImg,
    icon: rgIcon,
    isVideo: false,
  },
  {
    id: 'unisinos',
    tabLabel: 'Unisinos',
    name: 'Universidade do Vale do Rio dos Sinos (Unisinos)',
    quote: 'Dashboard em tempo real.',
    description:
      'Parceria para configurar traqueamento da jornada do cliente, mapeando necessidades e configurando GTM, pixels e dashboards.',
    person: 'Equipe Unisinos',
    personRole: 'Marketing & Tecnologia',
    img: unisinosImg,
    icon: unisinosImg,
    isVideo: false,
  },
  {
    id: 'vm',
    tabLabel: 'RBA + Via Marte',
    name: 'Via Marte',
    quote: 'Automação de mídia elevando as vendas em 40%.',
    description:
      'Automatizamos a coleta de dados de mídia e cruzamos com vendas, permitindo decisões ágeis para maximizar retorno.',
    person: '',
    personRole: 'Marketing Digital',
    img: viaMarteImg,
    icon: viaMarteImg,
    isVideo: false,
  },
  {
    id: 'ar',
    tabLabel: 'Agency Rfill',
    name: 'Agency Rfill',
    quote: 'Leads de imóveis de luxo em tempo real.',
    description:
      'Automatizamos coleta de dados, dashboards e geração de leads para campanhas mais eficazes.',
    person: 'Diretoria de Vendas',
    personRole: 'Imobiliário Premium',
    img: rfillVideo,
    icon: rfillVideo,
    isVideo: false,
  },
];
</script>

<style scoped>
/* ---------- Estrutura base ----------------------------------*/
.section-clients{
  padding:80px 0 60px;
  width:100vw;
  margin-left:calc(-50vw + 50%);
  margin-right:calc(-50vw + 50%);
  transition:background .5s ease;
}
.section-inner{max-width:1240px;margin:0 auto;padding:0 24px;}
.section-title{margin:0 0 36px;font-weight:700;color:var(--title-color, #fff);}
.clients-tabs .q-tab__label{color:var(--tab-color, #fff) !important;}
/* ---------- Gradientes + vars --------------------------------*/
.rg-bg{
  background: linear-gradient(135deg, #e65a6c 10%, #f7f8f8 70%);

  --title-color:#f1e9e9;
  --tab-color:#003B73;
}
.vm-bg{
  background:linear-gradient(135deg,#904d92 10%,#000000 100%);
  --title-color:#ffd4da;
  --tab-color:#ffd4da;
}
.ar-bg{
  background:linear-gradient(135deg,#000000 -20%,#ffffff 100%);
  --title-color:#ffffff;
  --tab-color:#ffffff;
}
.unisinos-bg{
  background:linear-gradient(135deg,#0066CC -10%,#6A0DAD 100%);
  --title-color:#e4f0fc;
  --tab-color:#e4f0fc;
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

@media(max-width:1023px){.client-name{font-size:1.4rem;}}
</style>
