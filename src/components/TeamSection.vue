<template>
  <section id="team" class="section-team flex flex-center">
    <div class="section-wave-top">
      <svg width="100%" height="90" viewBox="0 0 1440 90" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,72 C320,110 1120,-10 1440,65 L1440,0 L0,0 Z" fill="#A3DDF0" />
      </svg>
    </div>

    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md">A Equipe</h2>

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
          class="text-subtitle2 text-weight-medium"
        />
      </q-tabs>

      <q-tab-panels v-model="teamTab" animated class="q-mt-xl">
        <q-tab-panel
          v-for="m in teamMembers"
          :key="m.id"
          :name="m.id"
          class="q-pa-none"
        >
          <div class="row q-col-gutter-xl items-center">
            <div class="col-12 col-md-4">
              <q-img
                :src="m.img"
                :alt="m.name"
                class="team-media"
              />
            </div>
            <div class="col-12 col-md-8">
              <p class="text-pink-7 text-bold text-caption q-mb-xs">{{ m.role.toUpperCase() }}</p>
              <h4 class="text-subtitle1 q-mb-xs">{{ m.name }}</h4>
              <p class="text-body1">{{ m.bio }}</p>
            </div>
          </div>
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

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
</script>

<style scoped>
.section-team {
  position: relative;
  background: #fff;
  overflow: hidden;
  z-index: 1;
  padding-top: 90px;
}

.section-wave-top {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 80px;
  z-index: 2;
  pointer-events: none;
}

.section-content {
  position: relative;
  z-index: 3;
  width: 100%;
  max-width: 1200px;
  padding: 40px 20px;
  margin: 0 auto;
}

.team-media {
  width: 100%;
  max-width: 400px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
  display: block;
}
</style>
