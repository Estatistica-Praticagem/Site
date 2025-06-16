<template>
  <section id="team" class="section-team flex flex-center">
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
          <div class="row q-col-gutter-xl items-center justify-center">
            <div class="col-12 col-md-4 text-center">
              <q-img
                :src="m.img"
                :alt="m.name"
                class="team-media"
              />
            </div>
            <div class="col-12 col-md-8 text-left">
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
    name: 'Kévi Pegoraro',
    role: 'Data Specialist – USA',
    bio: 'Especialista em ETL, modelagem de dados e análise preditiva.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/kevi.png'),
  },
  {
    id: 'carol',
    name: 'Carolina Schaefer',
    role: 'Planning Specialist – BR',
    bio: 'Planejamento estratégico, CRM e orquestração de campanhas.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/carol.png'),
  },
  {
    id: 'cristian',
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
  background: #ffffff;
  /* background: linear-gradient(100deg, #A3DDF0 0%, #ffffff 90%); */
  padding-top: 80px;
  padding-bottom: 40px;
}

.section-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.team-media {
  width: 140px;
  height: 140px;
  object-fit: cover;
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  margin: 0 auto;
}
</style>
