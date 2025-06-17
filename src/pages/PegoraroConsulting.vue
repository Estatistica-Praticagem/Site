<template>
  <q-page class="q-pa-none main-portfolio-page">
    <!-- HERO -->
     <!-- retoma a verssão anterior de back ground Sobre a empresa. botão datadrive -> que abre a seção de dados com o conteuso do miro datadrive-->
    <HeroSection />

    <!-- DATA -->
     <!-- Abre na seção do hero e add todo o texto do métodologia/datadriver -->
    <!-- <DataSection /> -->

      <!-- SERVICES -->
       <!--Remove imagem e tags e ajusta os titulos para esquerda e ao passar o mause encima abre aderscrição há direita  -->
    <ServicesSection />

    <!-- SKILLS -->
    <!-- <SkillsVideoSection /> -->

    <!-- CLIENTES -->
     <!--  -->
    <ClientsSection />

    <!-- TEAM -->
     <!--  Foto circular e reduzir o tamanho-->
    <TeamSection />

     <!-- Logos -->
    <ClientsLogo/>

    <!-- FORM -->
    <ContactForm/>
     <!-- Criar section logos parceiros contem todos os logos dos clientes -->
    <!-- CONTATO Roda Pé-->
     <!-- logo do linkdin -->
    <ContactSection />

    <!-- Adicionar os IDs no botoés para GTM -->
  </q-page>
</template>

<script setup>

import HeroSection from 'src/components/HeroSection.vue';
// import SkillsVideoSection from 'src/components/SkillsVideoSection.vue';
// import DataSection from 'src/components/DataSection.vue';
import ServicesSection from 'src/components/ServicesSection.vue';
import ClientsSection from 'src/components/ClientsSection.vue';
import TeamSection from 'src/components/TeamSection.vue';
import ContactSection from 'src/components/ContactSection.vue';
import ContactForm from 'src/components/ContactForm.vue';
import ClientsLogo from 'src/components/ClientsLogo.vue';

</script>

<style scoped>
.main-portfolio-page {
  background: #f7e5d8;
}
</style>
