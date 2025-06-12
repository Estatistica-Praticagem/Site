<template>
  <q-page class="q-pa-none main-portfolio-page">
    <!-- HERO ---------------------------------------------------------------->
    <section class="section-hero flex flex-center">
      <div class="hero-content">
        <h1 class="text-h3 text-weight-bold text-white">Horizonte BI</h1>
        <p class="text-subtitle1 text-white q-mt-sm">Soluções Globais, Resultados Locais</p>
      </div>
    </section>

    <!-- NAVBAR -------------------------------------------------------------- -->
    <div class="q-pa-md nav-bar">
      <div class="row justify-center q-gutter-sm">
        <q-btn flat color="primary" label="Orientado a Dados" @click="scrollTo('data')" />
        <q-btn flat color="primary" label="Habilidades"        @click="scrollTo('skills')" />
        <q-btn flat color="primary" label="Clientes"           @click="scrollTo('clients')" />
        <q-btn flat color="primary" label="Serviços"           @click="scrollTo('services')" />
        <q-btn flat color="primary" label="Equipe"             @click="scrollTo('team')" />
        <q-btn flat color="primary" label="Contato"            @click="scrollTo('contact-info')" />
      </div>
    </div>

    <!-- DATA ----------------------------------------------------------------->
    <section id="data" class="section-data flex flex-center">
      <div class="section-content row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <q-img src="~assets/About the Company.png" :ratio="16/9" />
        </div>
        <div class="col-12 col-md-6">
          <h2 class="text-h5 text-primary">Negócios Orientados a Dados</h2>
          <p>Utilizamos dados confiáveis para avaliar estratégias atuais e desenhar caminhos de crescimento.</p>
          <p>Nossa cultura valoriza insights em cada ciclo, transformando informação em estratégia.</p>
        </div>
      </div>
    </section>

    <!-- SKILLS --------------------------------------------------------------->
    <section id="skills" class="section-skills flex flex-center">
      <div class="section-content row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <h2 class="text-h5 text-white">Habilidades e Expertise</h2>
          <ul class="text-white">
            <li>Análise de dados e insights para decisões em todas as fases do marketing.</li>
            <li>Processo ETL: múltiplas fontes, dados limpos e confiáveis.</li>
            <li>Pipelines SQL, DWs e dashboards interativos.</li>
            <li>Automação com Inteligência Artificial.</li>
          </ul>
        </div>
        <div class="col-12 col-md-6">
          <q-img src="~assets/Skills&Expertise.png" :ratio="1" />
        </div>
      </div>
    </section>

    <!-- SERVICES ------------------------------------------------------------->
    <section id="services" class="section-services flex flex-center">
      <div class="section-content row items-center q-col-gutter-md">
        <div class="col-12 col-md-6 order-md-last">
          <q-img src="~assets/OfferedServices.png" :ratio="1" />
        </div>
        <div class="col-12 col-md-6">
          <h2 class="text-h5 text-primary">Serviços Detalhados</h2>
          <ul>
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

    <!-- CLIENTES (TABS) ------------------------------------------------------>
    <section id="clients" class="section-clients flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary text-center">Grandes Clientes</h2>

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
            class="text-capitalize"
          />
        </q-tabs>

        <q-tab-panels v-model="clientTab" animated class="q-mt-lg">
          <q-tab-panel
            v-for="c in clientCards"
            :key="c.id"
            :name="c.id"
            class="q-pa-none"
          >
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-5">
                <q-img
                  :src="c.img"
                  :alt="c.name"
                  style="max-width:100%;object-fit:contain"
                />
              </div>
              <div class="col-12 col-md-7">
                <p class="text-primary text-bold q-mb-sm">{{ c.name }}</p>
                <p>"{{ c.quote }}"</p>
                <h5 class="q-mt-md">
                  {{ c.person }} |
                  <span class="text-primary">{{ c.name }}</span>
                </h5>
                <p class="text-caption">{{ c.personRole }}</p>
                <q-btn
                  v-if="c.link"
                  color="primary"
                  label="Ler sobre o Case"
                  :href="c.link"
                  target="_blank"
                  class="q-mt-sm"
                />
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </section>

    <!-- TEAM (TABS) ---------------------------------------------------------->
    <section id="team" class="section-team flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary text-center">A Equipe</h2>

        <q-tabs
          v-model="teamTab"
          class="q-mt-md text-primary bg-transparent"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab
            v-for="m in teamMembers"
            :key="m.id"
            :name="m.id"
            :label="m.tabLabel"
            class="text-capitalize"
          />
        </q-tabs>

        <q-tab-panels v-model="teamTab" animated class="q-mt-lg">
          <q-tab-panel
            v-for="m in teamMembers"
            :key="m.id"
            :name="m.id"
            class="q-pa-none"
          >
            <div class="row q-col-gutter-md items-center">
              <div class="col-12 col-md-4">
                <q-img
                  :src="m.img"
                  :alt="m.name"
                  style="max-width:100%;object-fit:contain"
                />
              </div>
              <div class="col-12 col-md-8">
                <h4>{{ m.name }}</h4>
                <p class="text-primary text-bold">{{ m.role }}</p>
                <p class="q-mt-sm">{{ m.bio }}</p>
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </section>

    <!-- CONTATO ------------------------------------------------------------->
    <section id="contact-info" class="section-contact flex flex-center">
      <div class="section-content text-center">
        <h2 class="text-h5 text-white q-mb-sm">Entre em Contato</h2>
        <div class="row justify-center items-center q-gutter-lg">
          <div>
            <q-btn outline color="white" icon="email" label="E-mail" @click="mailto" />
            <div class="text-caption text-white q-mt-xs">horizontetbi@gmail.com</div>
          </div>
          <div>
            <q-btn outline color="white" icon="linkedin" label="LinkedIn" @click="linkedin" />
            <div class="text-caption text-white q-mt-xs">linkedin.com/company/orizzonttebi</div>
          </div>
        </div>
        <q-btn
          color="white"
          text-color="primary"
          label="Fale Conosco"
          class="q-mt-lg"
          size="lg"
          @click="abrirFormulario"
        />
      </div>
    </section>

    <!-- FORM ---------------------------------------------------------------->
    <section id="contact" v-if="showForm" class="section-form flex flex-center">
      <div class="section-content text-center">
        <q-form @submit.prevent="onSubmit" class="q-gutter-md q-mt-md" style="max-width:400px;margin:auto">
          <q-input filled v-model="form.nome"      label="Nome"      required />
          <q-input filled v-model="form.email"     label="E-mail"    type="email" required />
          <q-input filled v-model="form.telefone"  label="Telefone" />
          <q-select
            filled
            v-model="form.servico"
            :options="['Consultoria','Dashboards','Campanhas']"
            label="Serviço"
          />
          <q-input filled v-model="form.descricao" label="Descrição" type="textarea" />
          <q-btn type="submit" label="Enviar" color="primary" class="full-width" />
        </q-form>
        <p v-if="formEnviado" class="q-mt-md text-italic text-white">
          Horizonte BI<br />Obrigado!
        </p>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';

/* ---------------------------- CLIENTES ----------------------------------- */
const clientTab = ref('rg'); // id inicial
const clientCards = [
  {
    id: 'rg',
    tabLabel: 'Rio Grande',
    name: 'Port of Rio Grande',
    quote: 'Sistema de marés e correntes em tempo real com IA.',
    person: 'Capitão Pilot Board',
    personRole: 'Harbor Operations',
    // eslint-disable-next-line global-require
    img: require('src/assets/clients/RGPilots.png'),
    link: '#',
  },
  {
    id: 'vm',
    tabLabel: 'Via Marte',
    name: 'Via Marte',
    quote: 'Automação de mídia para e-commerce com crescimento de 40 %.',
    person: 'Equipe Growth',
    personRole: 'Marketing Digital',
    // eslint-disable-next-line global-require
    img: require('src/assets/RGPilots.png'),
    link: '#',
  },
  {
    id: 'ar',
    tabLabel: 'Agency Rfill',
    name: 'Agency Rfill',
    quote: 'Dashboards de leads de imóveis de luxo em tempo real.',
    person: 'Diretoria de Vendas',
    personRole: 'Imobiliário Premium',
    // eslint-disable-next-line global-require
    img: require('src/assets/RGPilots.png'),
    link: '#',
  },
];

/* ----------------------------- EQUIPE ------------------------------------ */
const teamTab = ref('kevi');
const teamMembers = [
  {
    id: 'kevi',
    tabLabel: 'Kévi',
    name: 'Kévi Pegoraro',
    role: 'Data Specialist – USA',
    bio: 'Especialista em ETL, modelagem de dados e análise preditiva.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/kevi.png'),
  },
  {
    id: 'carol',
    tabLabel: 'Carolina',
    name: 'Carolina Schaefer',
    role: 'Planning Specialist – BR',
    bio: 'Planejamento estratégico, CRM e orquestração de campanhas.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/carol.png'),
  },
  {
    id: 'cristian',
    tabLabel: 'Cristian',
    name: 'Cristian P.',
    role: 'Full-stack Developer',
    bio: 'Dashboards, integrações e automações full-stack.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/cristian.png'),
  },
];

/* ---------------------------- FORMULÁRIO --------------------------------- */
const form = ref({
  nome: '', email: '', telefone: '', servico: '', descricao: '',
});
const showForm = ref(false);
const formEnviado = ref(false);
function onSubmit() { formEnviado.value = true; }
function abrirFormulario() {
  showForm.value = true;
  // eslint-disable-next-line no-use-before-define
  setTimeout(() => scrollTo('contact'), 10);
}

/* --------------------------- LINKS / SCROLL ------------------------------ */
function mailto() { window.open('mailto:horizontetbi@gmail.com', '_blank'); }
function linkedin() { window.open('https://linkedin.com/company/horizontebi', '_blank'); }
function scrollTo(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
/* --- Cores / Gradientes Gerais ----------------------------------------- */
.main-portfolio-page{ background:#f7e5d8 }

/* HERO ------------------------------------------------------------------ */
.section-hero{
  min-height:60vh;
  background:linear-gradient(135deg,#0b9dd3 0%,#0b4f76 100%);
  width:100vw;
}
.hero-content{ text-align:center;max-width:800px;margin:0 auto }

/* NAVBAR ---------------------------------------------------------------- */
.nav-bar{ position:sticky;top:0;z-index:5;background:transparent }

/* SEÇÕES ---------------------------------------------------------------- */
.section-data     { background:#f7e5d8 }
.section-skills   { background:linear-gradient(120deg,#3e8097 0%,#097f99 100%);color:#fff }
.section-services { background:#f7e5d8 }
.section-clients  { background:linear-gradient(90deg,#f7e5d8 40%,#769ca8 100%) }
.section-team     { background:linear-gradient(100deg,#769ca8 0%,#ffffff 80%) }
.section-contact  { background:linear-gradient(135deg,#0b4f76 0%,#097f99 100%) }
.section-form     { background:#097f99 }

.section-content  { width:100%;max-width:900px;padding:40px 20px;margin:0 auto }

/* RESPONSIVO ------------------------------------------------------------ */
@media(max-width:800px){ .section-content{ padding:20px 8px } }
@media(max-width:600px){
  .section-hero{ min-height:40vh }
  .section-content{ padding:12px 4px }
}
</style>
