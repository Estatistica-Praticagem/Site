<!-- eslint-disable global-require -->
<template>
  <section
    id="team"
    data-gtm="team-section"
    class="section-team flex flex-center"
  >
    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md">The Team</h2>

      <q-tabs
        v-model="teamTab"
        class="team-tabs q-mt-md text-primary bg-transparent"
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

      <!-- Panels with fully transparent background -->
      <q-tab-panels v-model="teamTab" animated class="transparent-panel q-mt-xl">
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
              <h4 class="text-subtitle1 q-mb-xs team-name">{{ m.name }}</h4>
              <p class="text-body1 team-bio">{{ m.bio }}</p>
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
    bio: 'Applied mathematician with 6 years of experience in Business Intelligence. Graduated from Calhoun Community College, Alabama, USA.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/kevi.png'),
  },
  {
    id: 'carol',
    name: 'Carolina Schaefer',
    role: 'Planning Specialist – Brazil',
    bio: 'Specialist in strategic planning, CRM, and campaign orchestration.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/carol.png'),
  },
  {
    id: 'cristian',
    name: 'Cristian P.',
    role: 'Full-stack Developer – Brazil',
    bio: 'Full-stack developer focused on automations, integrations, and custom system architecture and dashboard development.',
    // eslint-disable-next-line global-require
    img: require('src/assets/image/cristian.png'),
  },
];

</script>

<style scoped>
.section-team {
  background:transparent;
  /* background: linear-gradient(100deg, #985ee4 0%, #b5abf1 90%); */
  padding: 80px 0 40px;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
}

.section-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Tabs & Panels without background */
.team-tabs,
.transparent-panel,
.q-tab-panel {
  background: none !important;
}

/* Avatar */
.team-media {
  width: 140px;
  height: 140px;
  object-fit: cover;
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Typography tweaks */
.team-name {
  font-weight: 700;
}

.team-bio {
  line-height: 1.5;
}
</style>
