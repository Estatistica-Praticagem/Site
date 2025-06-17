<template>
  <section
    id="clients"
    data-gtm="clients-section"
    class="section-clients flex flex-center"
  >
    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md">Grandes Clientes</h2>

      <q-tabs
        v-model="clientTab"
        class="q-mt-md text-primary bg-transparent"
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

      <q-tab-panels v-model="clientTab" animated class="q-mt-xl">
        <q-tab-panel
          v-for="c in clientCards"
          :key="c.id"
          :name="c.id"
          class="q-pa-none"
        >
          <div class="row q-col-gutter-xl items-center justify-center">
            <div class="col-12 col-md-5 text-center">
              <component
                :is="c.isVideo ? 'video' : 'q-img'"
                :src="c.img"
                :alt="c.name"
                v-bind="c.isVideo ? { controls: true, autoplay: true, muted: true, loop: true } : {}"
                class="client-media"
              />
            </div>
            <div class="col-12 col-md-7 text-left">
              <p class="text-primary text-bold text-subtitle1 q-mb-sm">{{ c.name }}</p>
              <p class="text-body1 q-mb-md">"{{ c.quote }}"</p>
              <p class="text-body2 q-mb-md">{{ c.description }}</p>
              <h5 class="text-subtitle1 q-mb-xs">
                {{ c.person }} |
                <span class="text-primary">{{ c.name }}</span>
              </h5>
              <p class="text-caption text-grey-8">{{ c.personRole }}</p>
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
    isVideo: false,
  },
  {
    id: 'unisinos',
    tabLabel: 'Unisinos',
    name: 'Universidade do Vale do Rio dos Sinos (Unisinos)',
    quote: 'Do zero ao dashboard em tempo real.',
    description: 'A Horizonte BI firmou uma parceria com a Unisinos para configurar do zero o traqueamento da jornada do cliente, incluindo a limpeza de configurações antigas e o mapeamento das necessidades. Utilizamos Google Tag Manager, pixels, tags e botões para rastrear o comportamento em cada etapa da navegação. Os dados foram estruturados no BigQuery e Google Sheets e integrados a dashboards interativos para decisões com base na taxonomia personalizada da Unisinos.',
    person: 'Equipe Unisinos',
    personRole: 'Marketing & Tecnologia',
    img: unisinos,
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
    isVideo: false,
  },
];
</script>

<style scoped>
.section-clients {
  background-color: #ffffff;
  padding-top: 80px;
  padding-bottom: 60px;
}

.section-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.client-media {
  width: 100%;
  max-width: 240px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
  background-color: #f0f0f0;
  object-fit: contain;
}
</style>
