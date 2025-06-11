<template>
  <q-page class="q-pa-md">
    <!-- Navegação principal -->
    <div class="text-center q-mb-lg">
      <q-btn label="Orientado a Dados"   color="primary" flat class="q-mx-sm" @click="scrollTo('data')" />
      <q-btn label="Habilidades e Expertise" color="primary" flat class="q-mx-sm" @click="scrollTo('skills')" />
      <q-btn label="Clientes"            color="primary" flat class="q-mx-sm" @click="scrollTo('clients')" />
      <q-btn label="Serviços"            color="primary" flat class="q-mx-sm" @click="scrollTo('services')" />
      <q-btn label="Equipe"              color="primary" flat class="q-mx-sm" @click="scrollTo('team')" />
      <q-btn label="Contato"             color="primary" flat class="q-mx-sm" @click="scrollTo('contact-info')" />
    </div>

    <q-slide-transition>
      <q-card flat bordered class="q-pa-md portfolio-container">
        <!-- =================== SOBRE =================== -->
        <q-separator spaced />
        <div id="about" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6">
            <q-img src="~assets/About the Company.png" style="width:100%" spinner-color="primary" alt="Sobre a Empresa" />
          </div>
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Sobre a Empresa</h2>
            <p>
              A Horizonte BI é uma consultoria de marketing com serviço completo e know‑how em BI. Atuamos em todo o ciclo do marketing digital — da estratégia à otimização.
            </p>
            <p>
              Entregamos soluções de dados personalizadas, dashboards interativos e análises que sustentam decisões informadas.
            </p>
          </div>
        </div>

        <!-- ============== DATA‑DRIVEN BUSINESS ============== -->
        <q-separator spaced />
        <div id="data" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Negócios Orientados a Dados</h2>
            <p>Utilizamos dados confiáveis para avaliar estratégias atuais e desenhar caminhos de crescimento.</p>
            <p>Nossa cultura valoriza insights em cada ciclo, transformando informação em estratégia.</p>
          </div>
        </div>

        <!-- ============== SKILLS ============== -->
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
            <p>Automatizamos análises e identificamos padrões de comportamento em campanhas.</p>
          </div>
        </div>

        <!-- ============== SERVIÇOS ============== -->
        <q-separator spaced />
        <div id="services" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">Serviços Detalhados</h2>
          <div class="row q-col-gutter-md q-mt-md">
            <div class="col-12 col-md-6 text-justify">
              <ul>
                <li>Traqueamento completo da jornada do cliente (GTM, pixels, eventos personalizados).</li>
                <li>Criação de sites, landing pages e páginas de conversão otimizadas.</li>
                <li>Data Warehouse integrado (CRM, mídia, vendas).</li>
                <li>Dashboards e reports automatizados (Data‑Driven Visualization).</li>
                <li>Análises com IA para marketing e vendas.</li>
                <li>Planejamento estratégico de campanhas e clientes.</li>
                <li>CRM: configuração e gestão de relacionamento com leads.</li>
                <li>Conteúdo e peças visuais através de parceiros especializados.</li>
              </ul>
            </div>
            <div class="col-12 col-md-6">
              <q-img src="~assets/OfferedServices.png" style="width:100%" spinner-color="primary" alt="Serviços" />
            </div>
          </div>
        </div>

        <!-- ============== CLIENTES ============== -->
        <q-separator spaced />
        <div id="clients" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">Grandes Clientes</h2>
          <div class="row no-wrap scroll-x q-mt-md q-px-sm">
            <div
              v-for="(c, i) in clientCards"
              :key="'cl' + i"
              class="q-pr-sm client-card-custom"
            >
              <q-card class="column items-center q-pa-md client-card">
                <q-img
                  :src="c.img"
                  :alt="c.name"
                  style="height:90px;width:120px;object-fit:contain;"
                />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ c.name }}</div>
                <div class="text-caption text-center">{{ c.desc }}</div>
              </q-card>
            </div>
          </div>
        </div>

        <!-- ============== EQUIPE ============== -->
        <q-separator spaced />
        <div id="team" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">A Equipe</h2>
          <div class="row no-wrap scroll-x q-mt-md q-px-sm" style="overflow-x:auto;">
            <div v-for="(m, i) in teamSlides" :key="'tm' + i" class="q-pr-sm" style="min-width:320px">
              <q-card class="column items-center q-pa-md team-card">
                <q-img :src="m.img" :alt="m.name" style="width:100px;height:100px;border-radius:50%" />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ m.name }}</div>
                <div class="text-caption text-center">{{ m.role }}</div>
              </q-card>
            </div>
          </div>
        </div>

        <!-- ============== CONTATO / CTA ============== -->
        <q-separator spaced />
        <div id="contact-info" class="text-center q-mt-xl q-mb-lg">
          <h2 class="text-h5 text-primary q-mb-sm">Entre em Contato</h2>
          <div class="row justify-center items-center q-gutter-lg">
            <div>
              <q-btn outline color="primary" icon="email" label="E‑mail" @click="mailto" />
              <div class="text-caption q-mt-xs">horizontetbi@gmail.com</div>
            </div>
            <div>
              <q-btn outline color="primary" icon="linkedin" label="LinkedIn" @click="linkedin" />
              <div class="text-caption q-mt-xs">linkedin.com/company/horizontebi</div>
            </div>
          </div>
          <q-btn color="primary" label="Fale Conosco" class="q-mt-lg" size="lg" @click="abrirFormulario" />
        </div>

        <!-- Formulário -->
        <div id="contact" class="text-center q-mt-xl" v-if="showForm">
          <q-form @submit.prevent="onSubmit" class="q-gutter-md q-mt-md" style="max-width:400px;margin:auto">
            <q-input filled v-model="form.nome"      label="Nome"        required />
            <q-input filled v-model="form.email"     label="E‑mail" type="email" required />
            <q-input filled v-model="form.telefone"  label="Telefone" />
            <q-select filled v-model="form.servico"  :options="['Consultoria','Dashboards','Campanhas']" label="Serviço" />
            <q-input filled v-model="form.descricao" label="Descrição" type="textarea" />
            <q-btn type="submit" label="Enviar" color="primary" class="full-width" />
          </q-form>
          <p v-if="formEnviado" class="q-mt-md text-italic">Horizonte BI<br>Obrigado!</p>
        </div>
      </q-card>
    </q-slide-transition>
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

function onSubmit() {
  console.log('Formulário enviado:', form.value);
  formEnviado.value = true;
}

function abrirFormulario() {
  showForm.value = true;
  // eslint-disable-next-line no-use-before-define
  setTimeout(() => scrollTo('contact'), 50);
}

const mailto = () => window.open('mailto:horizontetbi@gmail.com', '_blank');
const linkedin = () => window.open('https://linkedin.com/company/horizontebi', '_blank');

function scrollTo(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.scroll-x { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.row.no-wrap { flex-wrap:nowrap; }
.portfolio-container { max-width:1100px; margin:0 auto; }

h2{ margin:16px 0 12px; }
p, ul{ font-size:16px; line-height:1.6; }
.text-justify{ text-align:justify; }

.client-card, .team-card{ width:100%; min-height:260px; box-shadow:0 2px 8px rgba(0,0,0,.08); border-radius:10px; }
.client-card-custom{ min-width:340px; max-width:380px; flex:0 0 auto; }

@media (max-width:600px){
  .portfolio-container{ padding:0!important; }
  .row.no-wrap{ flex-wrap:wrap; }
  .client-card-custom{ min-width:280px; }
}
</style>
