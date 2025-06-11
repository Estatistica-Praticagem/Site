<template>
  <q-page class="q-pa-none main-portfolio-page">
    <!-- Hero Section -->
    <section class="section-hero flex flex-center">
      <div class="hero-content">
        <h1 class="text-h3 text-weight-bold text-white">Horizonte BI</h1>
        <p class="text-subtitle1 text-white q-mt-sm">Soluções Globais, Resultados Locais</p>
      </div>
    </section>

    <!-- Navegação Fixa -->
    <div class="q-pa-md nav-bar">
      <div class="row justify-center q-gutter-sm">
        <q-btn label="Orientado a Dados" flat color="primary" @click="scrollTo('data')" />
        <q-btn label="Habilidades" flat color="primary" @click="scrollTo('skills')" />
        <q-btn label="Clientes" flat color="primary" @click="scrollTo('clients')" />
        <q-btn label="Serviços" flat color="primary" @click="scrollTo('services')" />
        <q-btn label="Equipe" flat color="primary" @click="scrollTo('team')" />
        <q-btn label="Contato" flat color="primary" @click="scrollTo('contact-info')" />
      </div>
    </div>

    <!-- Sessão Orientado a Dados -->
    <section id="data" class="section-data flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary">Negócios Orientados a Dados</h2>
        <p>Utilizamos dados confiáveis para avaliar estratégias atuais e desenhar caminhos de crescimento.</p>
        <p>Nossa cultura valoriza insights em cada ciclo, transformando informação em estratégia.</p>
      </div>
    </section>

    <!-- Skills & Expertise -->
    <section id="skills" class="section-skills flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary">Habilidades e Expertise</h2>
        <ul>
          <li>Análise de dados e insights para decisões em todas as fases do marketing.</li>
          <li>Processo ETL: integração de múltiplas fontes, dados limpos e confiáveis.</li>
          <li>Pipelines SQL, Data Warehouses e dashboards interativos.</li>
          <li>Automação com Inteligência Artificial em campanhas.</li>
        </ul>
      </div>
    </section>

    <!-- Serviços -->
    <section id="services" class="section-services flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary">Serviços Detalhados</h2>
        <ul>
          <li>Traqueamento de jornada, GTM, pixels e eventos.</li>
          <li>Sites, landing pages e páginas de conversão otimizadas.</li>
          <li>Data Warehouse com fontes integradas (CRM, mídia, vendas).</li>
          <li>Dashboards interativos, reports automatizados.</li>
          <li>Análises com IA para marketing e vendas.</li>
          <li>Planejamento de campanhas e clientes.</li>
          <li>CRM: configuração e gestão de leads.</li>
          <li>Conteúdo visual por parceiros especializados.</li>
        </ul>
      </div>
    </section>

    <!-- Clientes -->
    <section id="clients" class="section-clients flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary text-center">Grandes Clientes</h2>
        <div class="row no-wrap scroll-x q-mt-md q-px-sm">
          <div
            v-for="(c, i) in clientCards"
            :key="'cl'+i"
            class="q-pr-sm client-card-custom"
          >
            <q-card class="column items-center q-pa-md client-card">
              <q-img
                :src="c.img"
                :alt="c.name"
                style="height: 90px; width: 80px; object-fit: contain;"
              />
              <div class="text-subtitle1 text-bold q-mt-sm">{{ c.name }}</div>
              <div class="text-caption text-center">{{ c.desc }}</div>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- Equipe -->
    <section id="team" class="section-team flex flex-center">
      <div class="section-content">
        <h2 class="text-h5 text-primary text-center">A Equipe</h2>
        <div class="row no-wrap scroll-x q-mt-md q-px-sm" style="overflow-x: auto;">
          <div v-for="(m, i) in teamSlides" :key="'tm'+i" class="q-pr-sm" style="min-width: 320px">
            <q-card class="column items-center q-pa-md team-card">
              <q-img :src="m.img" :alt="m.name" style="width:100px;height:100px;border-radius:50%" />
              <div class="text-subtitle1 text-bold q-mt-sm">{{ m.name }}</div>
              <div class="text-caption text-center">{{ m.role }}</div>
            </q-card>
          </div>
        </div>
      </div>
    </section>

    <!-- Contato -->
    <section id="contact-info" class="section-contact flex flex-center">
      <div class="section-content text-center">
        <h2 class="text-h5 text-white q-mb-sm">Entre em Contato</h2>
        <div class="row justify-center items-center q-gutter-lg">
          <div>
            <q-btn outline color="white" icon="email" label="E-mail" @click="mailto" />
            <div class="text-caption q-mt-xs text-white">horizontetbi@gmail.com</div>
          </div>
          <div>
            <q-btn outline color="white" icon="linkedin" label="LinkedIn" @click="linkedin" />
            <div class="text-caption q-mt-xs text-white">linkedin.com/company/orizzonttebi</div>
          </div>
        </div>
        <q-btn color="white" text-color="primary" label="Fale Conosco" class="q-mt-lg" size="lg" @click="abrirFormulario" />
      </div>
    </section>

    <!-- Formulário -->
    <section id="contact" v-if="showForm" class="section-form flex flex-center">
      <div class="section-content text-center">
        <q-form @submit.prevent="onSubmit" class="q-gutter-md q-mt-md" style="max-width: 400px; margin: auto">
          <q-input filled v-model="form.nome" label="Nome" required />
          <q-input filled v-model="form.email" label="E-mail" type="email" required />
          <q-input filled v-model="form.telefone" label="Telefone" />
          <q-select filled v-model="form.servico" :options="['Consultoria', 'Dashboards', 'Campanhas']" label="Serviço" />
          <q-input filled v-model="form.descricao" label="Descrição" type="textarea" />
          <q-btn type="submit" label="Enviar" color="primary" class="full-width" />
        </q-form>
        <p v-if="formEnviado" class="q-mt-md text-italic text-white">Horizonte BI<br />Obrigado!</p>
      </div>
    </section>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';

const clientCards = [
  // eslint-disable-next-line global-require
  { name: 'Port of Rio Grande', desc: 'Real‑time tide/current ML system', img: require('src/assets/clients/RGPilots.png') },
  // eslint-disable-next-line global-require
  { name: 'Via Marte', desc: 'E‑commerce media automation', img: require('src/assets/RGPilots.png') },
  // eslint-disable-next-line global-require
  { name: 'Agency Rfill', desc: 'Luxury property lead dashboards', img: require('src/assets/RGPilots.png') },
];

const teamSlides = [
  // eslint-disable-next-line global-require
  { name: 'Kévi Pegoraro', role: 'Data Specialist – USA', img: require('src/assets/image/kevi.png') },
  // eslint-disable-next-line global-require
  { name: 'Carolina Schaefer', role: 'Planning Specialist – BR', img: require('src/assets/image/carol.png') },
  // eslint-disable-next-line global-require
  { name: 'Cristian P.', role: 'Full‑stack Developer', img: require('src/assets/image/cristian.png') },
];

const form = ref({
  nome: '', email: '', telefone: '', servico: '', descricao: '',
});
const showForm = ref(false);
const formEnviado = ref(false);
function onSubmit() { formEnviado.value = true; }
// eslint-disable-next-line no-use-before-define
function abrirFormulario() { showForm.value = true; setTimeout(() => scrollTo('contact'), 10); }
function mailto() { window.open('mailto:horizontetbi@gmail.com', '_blank'); }
function linkedin() { window.open('https://linkedin.com/company/horizontebi', '_blank'); }
function scrollTo(id) { const el = document.getElementById(id); if (el) el.scrollIntoView({ behavior: 'smooth' }); }
</script>

<style scoped>
.main-portfolio-page {
  background: #f7e5d8;
}

/* HERO */
.section-hero {
  min-height: 60vh;
  background: linear-gradient(135deg, #097f99 0%, #0b4f76 100%);
  width: 100vw;
}
.hero-content {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

/* Barra de navegação fixa */
.nav-bar {
  background: transparent;
  position: sticky;
  top: 0;
  z-index: 5;
}

/* SECTIONS */
.section-data {
  min-height: 340px;
  background: #f7e5d8;
}
.section-skills {
  min-height: 340px;
  background: linear-gradient(120deg, #3e8097 0%, #097f99 100%);
  color: #fff;
}
.section-services {
  min-height: 340px;
  background: #f7e5d8;
}
.section-clients {
  min-height: 340px;
  background: linear-gradient(90deg, #f7e5d8 40%, #769ca8 100%);
}
.section-team {
  min-height: 340px;
  background: linear-gradient(100deg, #769ca8 0%, #fff 80%);
}
.section-contact {
  min-height: 340px;
  background: linear-gradient(135deg, #0b4f76 0%, #097f99 100%);
}
.section-form {
  min-height: 340px;
  background: #097f99;
}
.section-content {
  width: 100%;
  max-width: 900px;
  padding: 40px 20px;
  background: transparent;
  margin: 0 auto;
}

@media (max-width: 800px) {
  .section-content { padding: 20px 8px; }
}
@media (max-width: 600px) {
  .section-hero { min-height: 40vh; }
  .section-content { padding: 12px 4px; }
}
</style>
