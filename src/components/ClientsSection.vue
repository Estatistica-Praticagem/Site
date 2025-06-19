<template>
  <section
    id="clients"
    data-gtm="clients-section"
    class="section-clients"
  >
    <div class="section-inner">
      <h2 class="text-h4 text-primary text-center q-mb-md">Grandes Clientes</h2>

      <q-tabs
        v-model="clientTab"
        class="clients-tabs q-mt-md text-primary bg-transparent"
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

      <q-tab-panels v-model="clientTab" animated class="transparent-panel q-mt-xl">
        <q-tab-panel
          v-for="c in clientCards"
          :key="c.id"
          :name="c.id"
          class="q-pa-none"
        >
          <div class="row items-center q-col-gutter-xl">
            <!-- Text / Info -->
            <div class="col-12 col-md-7">
              <div class="client-info">
                <!-- Icon + Name -->
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
                  <span v-if="c.personRole"> | <span class="text-primary">{{ c.personRole }}</span></span>
                </p>
              </div>
            </div>

            <!-- Media -->
            <div class="col-12 col-md-5 text-center">
              <component
                :is="c.isVideo ? 'video' : 'q-img'"
                :src="c.img"
                :alt="c.name"
                v-bind="c.isVideo ? { controls: true, autoplay: true, muted: true, loop: true } : {}"
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
import rgImg from 'src/assets/clients/logo-rgpilots.png';
import viaMarteImg from 'src/assets/clients/viamarte2.png';
import rfillVideo from 'src/assets/clients/rfill.png';
import unisinos from 'src/assets/clients/unisinos.png';

const clientTab = ref('rg');

const clientCards = [
  {
    id: 'rg',
    tabLabel: 'Rio Grande',
    name: 'Porto Internacional do Rio Grande',
    quote: 'Sistema de marés e correntes em tempo real com IA.',
    description: 'Desenvolvemos um sistema completo para coletar dados climáticos de diferentes fontes e sensores, treinando e implantando modelos de machine learning para prever marés e correntes. Nosso sistema fornece previsões em tempo real 24/7 para aumentar a segurança do porto, alcançando alto grau de assertividade e confiabilidade. Desde 2020.',
    person: 'Capitão Pilot Board',
    personRole: 'Operações Portuárias',
    img: rgImg,
    icon: rgImg,
    isVideo: false,
  },
  {
    id: 'unisinos',
    tabLabel: 'Unisinos',
    name: 'Universidade do Vale do Rio dos Sinos (Unisinos)',
    quote: 'Do zero ao dashboard em tempo real.',
    description: 'Horizonte BI firmou uma parceria com a Unisinos para configurar do zero o traqueamento da jornada do cliente, desde a limpeza de configurações antigas, mapeamento das necessidades e configuração de ferramentas como Google Tag Manager, pixels, tags e botões para rastrear o comportamento dos usuários em cada etapa da navegação. Após a organização e configuração interna do ecossistema de mapeamento das informações do site da Unisinos, realizamos bases de dados via BigQuery e Google Sheets para extração e manipulação dos anúncios de mídia — informações que foram disponibilizadas via dashboard interativo para tomada de decisão após manipulação dos dados em conjunto com a taxonomia de mídia personalizada e exclusiva da Unisinos. O resultado do trabalho passou por diferentes etapas, desde organização do traqueamento da jornada do cliente, configuração do Data Warehouse da Unisinos e dashboards exclusivos para análise e tomadas de decisões em realtime.',
    person: 'Equipe Unisinos',
    personRole: 'Marketing & Tecnologia',
    img: unisinos,
    icon: unisinos,
    isVideo: false,
  },
  {
    id: 'vm',
    tabLabel: 'RBA + Via Marte',
    name: 'Via Marte',
    quote: 'Automação de mídia para e-commerce com crescimento de 40%.',
    description: 'Um dos maiores e-commerces de calçados do sul do Brasil. Automatizamos a coleta de dados de todas as plataformas de mídia, criamos visões consolidadas e dashboards, e comparamos os dados de vendas. As informações são atualizadas diariamente, permitindo decisões rápidas para maximizar retorno e impulsionar as vendas. Desde 2024.',
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
    quote: 'Dashboards de leads de imóveis de luxo em tempo real.',
    description: 'A agência Rfill buscava otimizar o fluxo de marketing digital para imóveis de luxo. Automatizamos a coleta de dados, desenvolvemos dashboards e implementamos fluxos para geração de leads. Esses esforços entregaram insights acionáveis, permitindo decisões mais precisas e campanhas mais eficazes.',
    person: 'Diretoria de Vendas',
    personRole: 'Imobiliário Premium',
    img: rfillVideo,
    icon: rfillVideo,
    isVideo: false,
  },
];
</script>

<style scoped>
.section-clients {
  /* background:transparent; */
  background: linear-gradient(90deg, #f07f29 10%, #eba43b 100%);
  padding: 80px 0 60px;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
}

.section-inner {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 24px;
}

h2 {
  margin: 0 0 36px;
}

/* Tabs / Panels */
.clients-tabs,
.transparent-panel,
.q-tab-panel {
  background: none !important;
}

/* Header */
.client-header .client-icon {
  width: 40px;
  height: 40px;
  object-fit: contain;
  margin-right: 12px;
}

.client-name {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1C1C1C;
}

.client-quote {
  font-style: italic;
  font-size: 1.125rem;
  margin-bottom: 8px;
}

.client-description {
  font-size: 1rem;
  line-height: 1.5;
  margin-bottom: 12px;
}

.client-person {
  font-weight: 600;
  color: #000;
}

/* Media */
.client-media {
  width: 100%;
  max-width: 340px;
  height: auto;
  border-radius: 16px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.1);
  background-color: transparent !important;
  object-fit: contain;
}

@media (max-width: 1023px) {
  .client-name {
    font-size: 1.4rem;
  }
}
</style>
