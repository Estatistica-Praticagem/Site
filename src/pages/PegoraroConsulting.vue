<template>
  <q-page class="q-pa-md">
    <!-- Navigation Buttons -->
    <div class="text-center q-mb-lg">
      <q-btn label="About the Company"
      color="primary" flat @click="scrollTo('about')" class="q-mx-sm" />
      <q-btn label="Data Driven"
      color="primary" flat @click="scrollTo('data')" class="q-mx-sm" />
      <q-btn label="Skills & Expertise"
      color="primary" flat @click="scrollTo('skills')" class="q-mx-sm" />
      <q-btn label="Clients" color="primary" flat @click="scrollTo('clients')" class="q-mx-sm" />
      <q-btn label="Services" color="primary" flat @click="scrollTo('services')" class="q-mx-sm" />
      <q-btn label="Team" color="primary" flat @click="scrollTo('team')" class="q-mx-sm" />
      <q-btn label="Contact" color="primary" flat @click="scrollTo('contact')" class="q-mx-sm" />
    </div>

    <q-slide-transition>
      <q-card flat bordered class="q-pa-md portfolio-container">
        <!-- Title -->
        <div class="text-center q-mb-xl">
          <h1 class="text-h4 text-weight-bold">Horizonte BI</h1>
          <p class="text-subtitle1">Global Solutions, Local Results</p>
        </div>

        <!-- About the Company -->
        <q-separator spaced />
        <div id="about" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6">
            <q-img src="~assets/About the Company.png"
            style="width:100%" spinner-color="primary" alt="About the Company" />
          </div>
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">About the Company</h2>
            <p>Horizonte BI is a full-service marketing
              consultancy with deep BI expertise. We
              support clients across the digital marketing cycle—from strategy to optimization.</p>
            <p>We deliver tailored data solutions,
               dashboards and analytics that power informed decisions.</p>
          </div>
        </div>

        <!-- Data Driven Business -->
        <q-separator spaced />
        <div id="data" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Data Driven Business</h2>
            <p>We leverage reliable data to evaluate current strategies and design growth paths.</p>
            <p>Our culture values data insights in
              every cycle, turning information into strategy.</p>
          </div>
          <div class="col-12 col-md-6"></div>
        </div>

        <!-- Skills & Expertise -->
        <q-separator spaced />
        <div id="skills" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6">
            <q-img src="~assets/Skills&Expertise.png"
            style="width:100%" spinner-color="primary" alt="Skills & Expertise" />
          </div>
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Skills & Expertise</h2>
            <p>We analyse data, generate insights and improve decisions across marketing stages.</p>
            <h3 class="text-h6 text-bold q-mt-sm">ETL Process</h3>
            <p>Integration and transformation from multiple sources ensure clean, trusted data.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Database</h3>
            <p>SQL pipelines standardise and store insights.</p>
            <h3 class="text-h6 text-bold q-mt-sm">Dashboard</h3>
            <p>Interactive dashboards drive optimisation.</p>
          </div>
        </div>

        <!-- Clients Carousel -->
        <q-separator spaced />
        <div id="clients" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">Big Clients</h2>
          <q-slide-group show-arrows class="q-mt-md" dense>
            <div v-for="(c, i) in clientCards" :key="'cl'+i"
            class="q-slide-item q-pa-sm" style="min-width: 250px">
              <q-card class="column items-center q-pa-md client-card">
                <q-img :src="c.img" :alt="c.name" style="height:120px;width:100%" />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ c.name }}</div>
                <div class="text-caption text-center">{{ c.desc }}</div>
              </q-card>
            </div>
          </q-slide-group>
        </div>

        <!-- Team Carousel -->
        <q-separator spaced />
        <div id="team" class="q-mt-xl">
          <h2 class="text-h5 text-primary text-center">The Team</h2>
          <q-slide-group show-arrows class="q-mt-md" dense>
            <div v-for="(m, i) in teamSlides" :key="'tm'+i"
            class="q-slide-item q-pa-sm" style="min-width: 220px">
              <q-card class="column items-center q-pa-md team-card">
                <q-img :src="m.img" :alt="m.name"
                style="width:100px;height:100px;border-radius:50%" />
                <div class="text-subtitle1 text-bold q-mt-sm">{{ m.name }}</div>
                <div class="text-caption text-center">{{ m.role }}</div>
              </q-card>
            </div>
          </q-slide-group>
        </div>

        <!-- Services Text -->
        <q-separator spaced />
        <div id="services" class="row q-col-gutter-md q-mt-lg items-center">
          <div class="col-12 col-md-6 text-justify">
            <h2 class="text-h5 text-primary">Offered Services</h2>
            <p>End-to-end performance: ETL, analysis, CRM, dashboards and media optimisation.</p>
          </div>
          <div class="col-12 col-md-6">
            <q-img src="~assets/OfferedServices.png"
             style="width:100%" spinner-color="primary" alt="Services" />
          </div>
        </div>

        <!-- Contact Us -->
        <q-separator spaced />
        <div id="contact" class="text-center q-mt-xl">
          <h2 class="text-h5 text-primary">Contact Us</h2>
          <p>Data Specialist</p>
          <p class="q-mt-md text-italic">Horizonte BI<br />Thank you!</p>
        </div>
      </q-card>
    </q-slide-transition>
  </q-page>
</template>

<script>
export default {
  name: 'PegoraroConsultingPortfolio',
  data() {
    return {
      clientCards: [
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
      ],
      teamSlides: [
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
      ],
    };
  },
  methods: {
    scrollTo(id) {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },
  },
};
</script>

<style scoped>
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
</style>
