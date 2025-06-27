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

import HeroSection from 'src/components/HeroSection_en.vue';
// import SkillsVideoSection from 'src/components/SkillsVideoSection.vue';
// import DataSection from 'src/components/DataSection.vue';
import ServicesSection from 'src/components/ServicesSection_en.vue';
import ClientsSection from 'src/components/ClientsSection_en.vue';
import TeamSection from 'src/components/TeamSection_en.vue';
import ContactSection from 'src/components/ContactSection_en.vue';
import ContactForm from 'src/components/ContactForm_en.vue';
import ClientsLogo from 'src/components/ClientsLogo_en.vue';

</script>

<style scoped>
.main-portfolio-page {
  background: linear-gradient(100deg, #e8f6fa 0%, #ffffff 100%);
}
</style>
