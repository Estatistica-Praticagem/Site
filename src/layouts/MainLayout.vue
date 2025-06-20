<template>
  <q-layout view="lhr Lpr lFf">
    <q-header elevated class="bg-white shadow-header">
      <q-toolbar class="q-py-xs q-px-lg flex flex-center" style="min-height: 58px;">
        <div class="row items-center justify-between full-width">

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
          <div v-if="mostrarBotoes" class="row items-center no-wrap q-ml-xl">
            <q-btn flat dense color="primary" label="Clientes" @click="scrollTo('clients')" class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Serviços" @click="scrollTo('services')" class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Equipe"   @click="scrollTo('team')"     class="q-mx-xs text-body1" />
            <q-btn flat dense color="primary" label="Contato"  @click="scrollTo('contact')"  class="q-mx-xs text-body1" />
          </div>

          <!-- Logout (somente se logado e fora do login/contacts) -->
          <div v-if="mostrarLogout && isLoggedIn" class="row items-center">
            <q-btn dense flat icon="logout" color="negative" @click="logout" class="q-ml-md">
              <q-tooltip>Logout</q-tooltip>
            </q-btn>
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
import { useRoute, useRouter } from 'vue-router';
import { computed } from 'vue';

const route = useRoute();
const router = useRouter();
const mostrarLogout = computed(() => ['/login', '/contacts'].includes(route.path));
const mostrarBotoes = computed(() => !['/login', '/contacts'].includes(route.path));
const isLoggedIn = computed(() => !!localStorage.getItem('user_id'));

function scrollTo(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function logout() {
  localStorage.removeItem('user_id');
  localStorage.removeItem('user_name');
  router.push('/login');
}
</script>

<style scoped>
.shadow-header {
  box-shadow: 0 2px 14px 0 rgba(64, 64, 64, 0.05);
}
</style>
