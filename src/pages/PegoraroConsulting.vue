<template>
  <q-page class="q-pa-none main-portfolio-page">
    <!-- HERO -->
    <HeroVideoSection />

    <!-- DATA -->
    <DataSection />

    <!-- SKILLS -->
    <SkillsVideoSection />

    <!-- SERVICES -->
    <ServicesSection />

    <!-- CLIENTES -->
    <ClientsSection />

    <!-- TEAM -->
    <TeamSection />

    <!-- CONTATO -->
    <ContactSection />

    <!-- FORM -->
    <ContactForm/>
  </q-page>
</template>

<script setup>

import HeroVideoSection from 'src/components/HeroVideoSection.vue';
import SkillsVideoSection from 'src/components/SkillsVideoSection.vue';
import DataSection from 'src/components/DataSection.vue';
import ServicesSection from 'src/components/ServicesSection.vue';
import ClientsSection from 'src/components/ClientsSection.vue';
import TeamSection from 'src/components/TeamSection.vue';
import ContactSection from 'src/components/ContactSection.vue';
import ContactForm from 'src/components/ContactForm.vue';

</script>

<style scoped>
.main-portfolio-page {
  background: #f7e5d8;
}
</style>
