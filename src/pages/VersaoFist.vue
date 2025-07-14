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
    <div class="q-pa-md">
      <div class="row justify-center q-gutter-sm">
        <!-- <q-btn flat color="primary" label="Orientado a Dados" @click="scrollTo('data')" /> -->
        <!-- <q-btn flat color="primary" label="Habilidades" @click="scrollTo('skills')" /> -->
        <q-btn flat color="primary" label="Clientes" @click="scrollTo('clients')" />
        <q-btn flat color="primary" label="Serviços" @click="scrollTo('services')" />
        <q-btn flat color="primary" label="Equipe" @click="scrollTo('team')" />
        <q-btn flat color="primary" label="Contato" @click="scrollTo('contact-info')" />
      </div>
    </div>

    <!-- DATA ----------------------------------------------------------------->
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

    <!-- SKILLS --------------------------------------------------------------->
    <section id="skills" class="section-skills flex flex-center">
      <div class="section-content row items-center q-col-gutter-md">
        <div class="col-12 col-md-6">
          <h2 class="text-h5 text-white">Habilidades e Expertise</h2>
          <ul class="text-body1 text-white">
            <li>Análise de dados e insights para decisões em todas as fases do marketing.</li>
            <li>Processo ETL: múltiplas fontes, dados limpos e confiáveis.</li>
            <li>Pipelines SQL, DWs e dashboards interativos.</li>
            <li>Automação com Inteligência Artificial.</li>
          </ul>
        </div>
        <div class="col-12 col-md-6 relative-position">
          <q-img src="~assets/Skills&Expertise.png" style="width: 100%; max-width: 400px; height: auto;" />
          <!-- BALÕES SUSPENSOS -->
          <q-chip
            v-for="(b, idx) in skillPills"
            :key="'skillPill'+idx"
            class="floating-pill text-body1"
            color="white"
            text-color="primary"
            :icon="b.icon"
            :style="{top: `${32 + idx * 62}px`}"
          >{{ b.text }}</q-chip>
        </div>
      </div>
    </section>

    <!-- SERVICES ------------------------------------------------------------->
    <section id="services" class="section-services flex flex-center">
      <div class="section-content row items-center q-col-gutter-md">
        <div class="col-12 col-md-6 order-md-last relative-position">
          <q-img src="~assets/OfferedServices.png" style="width: 100%; max-width: 400px; height: auto;" />
          <!-- BALÕES SUSPENSOS -->
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
            class="text-capitalize text-subtitle1"
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
                  style="width: 100%; max-width: 380px; height: auto;"
                />
              </div>
              <div class="col-12 col-md-7">
                <p class="text-primary text-bold text-subtitle1 q-mb-sm">{{ c.name }}</p>
                <p class="text-body1">"{{ c.quote }}"</p>
                <h5 class="q-mt-md text-subtitle1">
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
            :label="m.role"
            class="text-capitalize text-subtitle1"
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
                  style="width: 100%; max-width: 350px; height: auto;"
                />
              </div>
              <div class="col-12 col-md-8">
                <h4 class="text-subtitle1">{{ m.name }}</h4>
                <p class="text-primary text-bold text-subtitle1">{{ m.role }}</p>
                <p class="q-mt-sm text-body1">{{ m.bio }}</p>
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
            <div class="text-caption text-white q-mt-xs">horizontetbi.contato@gmail.com</div>
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
          <q-input filled v-model="form.nome" label="Nome" required />
          <q-input filled v-model="form.email" label="E-mail" type="email" required />
          <q-input filled v-model="form.telefone" label="Telefone" />
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

/* BALÕES SUSPENSOS */
const dataPills = [
  { icon: 'visibility', text: 'Aumente Sua Visibilidade' },
  { icon: 'diversity_1', text: 'Engaje Seu Público' },
  { icon: 'trending_up', text: 'Melhore Sua Estratégia' },
];
const skillPills = [
  { icon: 'show_chart', text: 'Power BI & Analytics' },
  { icon: 'integration_instructions', text: 'Integração Total' },
];
const servicePills = [
  { icon: 'north_east', text: 'Aumente Suas Vendas' },
  { icon: 'handshake', text: 'Fidelize Seus Clientes' },
  { icon: 'grid_view', text: 'Otimize Seus Processos' },
];

/* CLIENTES E EQUIPE (IGUAL O SEU) ... */
const clientTab = ref('rg');
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

/* FORMULÁRIO IGUAL AO SEU */
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
function mailto() { window.open('mailto:horizontetbi.contato@gmail.com', '_blank'); }
function linkedin() { window.open('https://linkedin.com/company/horizontebi', '_blank'); }
function scrollTo(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.main-portfolio-page{ background:#f7e5d8 }

/* HERO ------------------------------------------------------------------ */
.section-hero{
  min-height:60vh;
  background:linear-gradient(135deg,#0b9dd3 0%,#0b4f76 100%);
  width:100vw;
}
.hero-content{
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}
.nav-bar{
  position: sticky;
  top: 0;
  z-index: 5;
  background: transparent;
}
.section-data     { background: #f7e5d8; } /* tom pastel claro */
.section-skills   { background: linear-gradient(120deg, #3e8097 0%, #0b9dd3 100%); color: #fff; } /* azul e azul-claro */
.section-services { background: #f7e5d8; } /* igual data */
.section-clients  { background: linear-gradient(90deg, #f7e5d8 40%, #A3DDF0 100%); } /* pastel com azul clarinho */
.section-team     { background: linear-gradient(100deg, #A3DDF0 0%, #ffffff 90%); } /* azul claro pro branco */
.section-contact  { background: linear-gradient(135deg, #0b4f76 0%, #47e295 100%); } /* azul pro verde-claro */
.section-form     { background: linear-gradient(135deg, #f7e5d8 0%, #bdfadc 100%); } /* pastel pro verde suave */

.section-content  {
  width: 100%;
  max-width: 1200px;
  padding: 40px 20px;
  margin: 0 auto;
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

@media(max-width:800px){ .section-content{ padding:20px 8px } }
@media(max-width:600px){
  .section-hero{ min-height:40vh }
  .section-content{ padding:12px 4px }
}
.section-content p,
.text-body1 { font-size: 1.12rem; line-height: 1.65; }
.text-subtitle1 { font-size: 1.23rem; font-weight:500;}
</style>
