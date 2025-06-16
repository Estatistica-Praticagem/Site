<template>
  <q-layout view="lhr Lpr lFf">
    <q-header elevated class="bg-white shadow-header">
      <q-toolbar class="q-py-xs q-px-lg flex flex-center" style="min-height: 58px;">
        <div class="row items-center justify-center full-width">
          <!-- Logo + Nome -->
          <div class="row items-center no-wrap">
            <q-img
              src="~assets/icons/icon.png"
              alt="Logo Horizonte BI"
              class="q-mr-md"
              style="width:86px;height:76px;"
              spinner-color="primary"
              fit="contain"
            />
            <div>
              <div class="text-h6 text-primary q-mb-xs" style="letter-spacing:1px;">Horizonte BI</div>
              <div class="text-caption text-grey-8" style="margin-top:-4px;">Global Solutions, Local Results</div>
            </div>
          </div>
          <!-- Barra de Navegação -->
          <div class="row items-center no-wrap q-ml-xl">
            <q-btn flat dense color="primary" label="Clientes"     @click="scrollTo('clients')"    class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Serviços"     @click="scrollTo('services')"   class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Equipe"       @click="scrollTo('team')"       class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Contato"      @click="scrollTo('contact')" class="q-mx-xs text-body1" />
          </div>
        </div>
      </q-toolbar>

    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
function scrollTo(id) {
  // Caso as âncoras estejam em páginas diferentes, trocar para uso do vue-router!
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.shadow-header {
  box-shadow: 0 2px 14px 0 rgba(64,64,64,0.05);
}
</style>
