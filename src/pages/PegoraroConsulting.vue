<template>
  <q-page class="q-pa-md">
    <!-- Botões de Navegação -->
    <div class="text-center q-mb-lg">
      <q-btn label="Sobre a Empresa" color="primary" flat @click="scrollTo('about')" class="q-mx-sm" />
      <q-btn label="Orientado a Dados" color="primary" flat @click="scrollTo('data')" class="q-mx-sm" />
      <q-btn label="Habilidades e Expertise" color="primary" flat @click="scrollTo('skills')" class="q-mx-sm" />
      <q-btn label="Clientes" color="primary" flat @click="scrollTo('clients')" class="q-mx-sm" />
      <q-btn label="Serviços" color="primary" flat @click="scrollTo('services')" class="q-mx-sm" />
      <q-btn label="Equipe" color="primary" flat @click="scrollTo('team')" class="q-mx-sm" />
      <q-btn label="Contato" color="primary" flat @click="scrollTo('contact-info')" class="q-mx-sm" />
    </div>

    <q-slide-transition>
      <q-card flat bordered class="q-pa-md portfolio-container">

        <div class="text-center q-mb-xl">
          <h1 class="text-h4 text-weight-bold">Horizonte BI</h1>
          <p class="text-subtitle1">Soluções Globais, Resultados Locais</p>
        </div>

        <!-- Sobre a Empresa -->
        <q-separator spaced />
        <div id="about" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6">
            <q-img src="~assets/About the Company.png" style="width:100%" spinner-color="primary" alt="Sobre a Empresa" />
          </div>
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Sobre a Empresa</h2>
            <p>A Horizonte BI é uma consultoria de marketing com serviço completo e expertise profunda em BI. Atendemos clientes em todo o ciclo do marketing digital—da estratégia à otimização.</p>
            <p>Entregamos soluções de dados personalizadas, dashboards e análises que sustentam decisões informadas.</p>
          </div>
        </div>

        <!-- Negócios Orientados a Dados -->
        <q-separator spaced />
        <div id="data" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Negócios Orientados a Dados</h2>
            <p>Utilizamos dados confiáveis para avaliar estratégias atuais e desenhar caminhos de crescimento.</p>
            <p>Nossa cultura valoriza insights em cada ciclo, transformando informação em estratégia.</p>
          </div>
        </div>

        <!-- Habilidades e Expertise -->
        <q-separator spaced />
        <div id="skills" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6">
            <q-img src="~assets/Skills&Expertise.png" style="width:100%" spinner-color="primary" alt="Habilidades e Expertise" />
          </div>
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Habilidades e Expertise</h2>
            <p>Analisamos dados, geramos insights e melhoramos decisões em todas as fases do marketing.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Processo ETL</h3>
            <p>Integração e transformação de múltiplas fontes garantem dados limpos e confiáveis.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Banco de Dados</h3>
            <p>Pipelines SQL padronizam e armazenam insights.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Dashboard</h3>
            <p>Dashboards interativos impulsionam a otimização.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Inteligência Artificial</h3>
            <p>Utilizamos IA para automatizar análises e identificar padrões de comportamento em campanhas.</p>
          </div>
        </div>

        <!-- Serviços Detalhados -->
        <q-separator spaced />
        <div id="services" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">Serviços Detalhados</h2>
          <div class="row q-col-gutter-md q-mt-md">
            <div class="col-12 col-md-6 text-justify">
              <ul>
                <li>Traqueamento completo da jornada do cliente com GTM, pixels e eventos personalizados.</li>
                <li>Criação de sites, landing pages e páginas de conversão otimizadas.</li>
                <li>Construção de Data Warehouse com fontes integradas (CRM, mídia, vendas).</li>
                <li>Dashboards interativos e reports automatizados (Data Driven Visualization).</li>
                <li>Análises automatizadas com IA para marketing e vendas.</li>
                <li>Planejamento estratégico de campanhas e clientes.</li>
                <li>CRM: configuração e gestão do relacionamento com leads.</li>
                <li>Criação de peças visuais e conteúdo por parceiros especializados.</li>
              </ul>
            </div>
            <div class="col-12 col-md-6">
              <q-img src="~assets/OfferedServices.png" style="width:100%" spinner-color="primary" alt="Serviços" />
            </div>
          </div>
        </div>

        <!-- Clientes -->
        <q-separator spaced />
        <div id="clients" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">Grandes Clientes</h2>
          <div class="row no-wrap scroll-x q-mt-md q-px-sm">
            <div v-for="(c, i) in clientCards" :key="'cl'+i" class="q-pr-sm" style="min-width: 250px">
              <q-card class="column items-center q-pa-md client-card">
                <q-img :src="c.img" :alt="c.name" style="height:120px;width:100%" />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ c.name }}</div>
                <div class="text-caption text-center">{{ c.desc }}</div>
              </q-card>
            </div>
          </div>
        </div>

        <!-- Equipe -->
        <q-separator spaced />
        <div id="team" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">A Equipe</h2>
          <div class="row no-wrap scroll-x q-mt-md q-px-sm">
            <div v-for="(m, i) in teamSlides" :key="'tm'+i" class="q-pr-sm" style="min-width: 220px">
              <q-card class="column items-center q-pa-md team-card">
                <q-img :src="m.img" :alt="m.name" style="width:100px;height:100px;border-radius:50%" />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ m.name }}</div>
                <div class="text-caption text-center">{{ m.role }}</div>
              </q-card>
            </div>
          </div>
        </div>

        <!-- Área de Contato Fixa (antes do formulário) -->
        <q-separator spaced />
        <div id="contact-info" class="text-center q-mt-xl q-mb-lg">
          <h2 class="text-h5 text-primary q-mb-sm">Entre em Contato</h2>
          <div class="row justify-center items-center q-gutter-lg">
            <div>
              <q-btn
                outline
                color="primary"
                icon="email"
                label="E-mail"
                @click="mailto"
              />
              <div class="text-caption q-mt-xs">horizontetbi@gmail.com</div>
            </div>
            <div>
              <q-btn
                outline
                color="primary"
                icon="whatsapp"
                label="WhatsApp"
                @click="whatsapp"
              />
              <div class="text-caption q-mt-xs">+55 51 99888-0000</div>
            </div>
            <div>
              <q-btn
                outline
                color="primary"
                icon="linkedin"
                label="LinkedIn"
                @click="linkedin"
              />
              <div class="text-caption q-mt-xs">
                linkedin.com/company/horizontebi
              </div>
            </div>
          </div>
            <q-btn
              color="primary"
              label="Fale Conosco"
              class="q-mt-lg"
              size="lg"
              @click="abrirFormulario"
            />
        </div>

        <!-- Formulário de Contato -->
      <div id="contact" class="text-center q-mt-xl" v-if="showForm">
        <q-form @submit.prevent="onSubmit" class="q-gutter-md q-mt-md" style="max-width: 400px; margin: auto">
          <q-input filled v-model="form.nome" label="Nome" required />
          <q-input filled v-model="form.email" label="E-mail" type="email" required />
          <q-input filled v-model="form.telefone" label="Telefone" />
          <q-select filled v-model="form.servico" :options="['Consultoria', 'Dashboards', 'Campanhas']" label="Serviço" />
          <q-input filled v-model="form.descricao" label="Descrição" type="textarea" />
          <q-btn type="submit" label="Enviar" color="primary" class="full-width" />
        </q-form>
        <p class="q-mt-md text-italic">Horizonte BI<br />Obrigado!</p>
      </div>

      </q-card>
    </q-slide-transition>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';

const clientCards = [
  {
    name: 'Port of Rio Grande',
    desc: 'Real-time tide/current ML system',
    // eslint-disable-next-line global-require
    img: require('src/assets/RGPilots.png'),
  },
  {
    name: 'Via Marte',
    desc: 'E-commerce media automation',
    // eslint-disable-next-line global-require
    img: require('src/assets/RGPilots.png'),
  },
  {
    name: 'Agency Rfill',
    desc: 'Luxury property lead dashboards',
    // eslint-disable-next-line global-require
    img: require('src/assets/RGPilots.png'),
  },
];

const teamSlides = [
  {
    name: 'Kévi Pegoraro',
    role: 'Data Specialist – USA',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/kevi.png'),
  },
  {
    name: 'Lorenzo de Oliveira',
    role: 'Media Specialist – BR',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/lorenzo.png'),
  },
  {
    name: 'Carolina Schaefer',
    role: 'Planning Specialist – BR',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/carol.png'),
  },
  {
    name: 'Cristian P.',
    role: 'Full-stack Developer',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/cristian.png'),
  },
];

const form = ref({
  nome: '',
  email: '',
  telefone: '',
  servico: '',
  descricao: '',
});

// Controle da exibição do formulário de contato
const showForm = ref(false);

function abrirFormulario() {
  showForm.value = true;
  // Scroll suave para o formulário quando abrir
  // eslint-disable-next-line no-use-before-define
  setTimeout(() => scrollTo('contact'), 10);
}

// Abre e-mail
function mailto() {
  window.open('mailto:horizontetbi@gmail.com', '_blank');
}

// Abre WhatsApp
function whatsapp() {
  window.open('https://wa.me/5551998880000', '_blank');
}

// Abre LinkedIn
function linkedin() {
  window.open('https://linkedin.com/company/horizontebi', '_blank');
}

// Scroll até seção
function scrollTo(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

// Submissão do formulário
function onSubmit() {
  console.log('Formulário enviado:', form.value);
  // Aqui você pode integrar com API, Google Sheets, ou e-mail
}
</script>

<style scoped>
.scroll-x {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.row.no-wrap {
  flex-wrap: nowrap;
}
.portfolio-container {
  max-width: 1100px;
  margin: 0 auto;
}
h2 {
  margin: 16px 0 12px;
}
p, ul {
  font-size: 16px;
  line-height: 1.6;
}
.text-justify {
  text-align: justify;
}
.client-card, .team-card {
  width: 100%;
  min-height: 260px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}
@media (max-width: 600px) {
  .portfolio-container {
    padding: 0 !important;
  }
  .row.no-wrap {
    flex-wrap: wrap;
  }
}
</style>
