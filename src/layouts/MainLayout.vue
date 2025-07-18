<template>
  <q-layout view="lhr Lpr lFf">
    <q-header elevated class="bg-white shadow-header">
      <q-toolbar class="q-py-xs q-px-lg flex flex-center" style="min-height: 58px;">
        <div class="row items-center justify-between full-width">
          <!-- Header especial RG Pilots -->
          <template v-if="isWeatherNow">
            <div class="row items-center no-wrap justify-center full-width">
              <q-img
                :src="logoSrc"
                alt="Logo RG Pilots"
                style="width:86px;height:76px;"
                spinner-color="primary"
                fit="contain"
                class="q-mr-md"
              />
              <div>
                <div class="text-h6 text-primary q-mb-xs" style="letter-spacing:1px;">RG Pilots</div>
              </div>
            </div>
          </template>
          <!-- Header padrão Horizonte BI -->
          <template v-else>
            <div class="row items-center no-wrap">
              <q-img
                :src="logoSrc"
                :alt="logoAlt"
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
            <!-- Menus e avatar só fora do weatherNow -->
            <div v-if="mostrarBotoes" class="row items-center no-wrap q-ml-xl">
              <q-btn
                v-for="section in sections"
                :key="section.id"
                :id="`btn-${section.id}`"
                :data-gtm="`btn-${section.id}`"
                flat dense
                color="primary"
                :label="section.label"
                @click="handleClick(section.id)"
                class="q-mx-xs text-body1"
              />
            </div>
            <div v-if="isLoggedIn" class="row items-center">
              <q-btn dense flat round class="q-ml-md" size="lg" style="padding:0;">
                <q-avatar size="38px">
                  <template v-if="userImageUrl">
                    <img :src="userImageUrl" :alt="userName" />
                  </template>
                  <template v-else>
                    <span class="avatar-initials">{{ initials }}</span>
                  </template>
                </q-avatar>
                <q-menu anchor="bottom right" self="top right">
                  <div class="column items-center q-pa-md">
                    <q-avatar size="68px" class="q-mb-xs">
                      <template v-if="userImageUrl">
                        <img :src="userImageUrl" :alt="userName" />
                      </template>
                      <template v-else>
                        <span class="avatar-initials-big">{{ initials }}</span>
                      </template>
                    </q-avatar>
                    <div class="text-bold">{{ userName }}</div>
                  </div>
                  <q-separator />
                  <q-list bordered separator>
                    <q-item clickable v-ripple @click="gotoEditProfile">
                      <q-item-section avatar><q-icon name="edit" /></q-item-section>
                      <q-item-section>{{ t('profile.edit') }}</q-item-section>
                    </q-item>
                    <q-item clickable v-ripple @click="logout">
                      <q-item-section avatar><q-icon name="logout" color="negative" /></q-item-section>
                      <q-item-section>{{ t('profile.logout') }}</q-item-section>
                    </q-item>
                  </q-list>
                </q-menu>
              </q-btn>
            </div>
          </template>
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
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import RGPilotsSrc from 'src/assets/clients/logo-rgpilots.png';

const route = useRoute();
const router = useRouter();
const { t, locale } = useI18n();

onMounted(() => {
  const lang = localStorage.getItem('lang') || 'en';
  if (locale.value !== lang) {
    locale.value = lang;
  }
  if (window.location.pathname === '/' || window.location.pathname === '/index.html') {
    router.replace(lang === 'pt' ? '/br' : '/en');
  }
});

const isWeatherNow = computed(() => route.path === '/weatherNow');
const logoSrc = computed(() => (isWeatherNow.value
  ? RGPilotsSrc
  // eslint-disable-next-line global-require
  : require('src/assets/icons/icon.png')));
const logoAlt = computed(() => (isWeatherNow.value ? 'Logo RG Pilots' : 'Logo Horizonte BI'));

const sections = computed(() => [
  { id: 'clients', label: t('navbar.clients') },
  { id: 'services', label: t('navbar.services') },
  { id: 'team', label: t('navbar.team') },
  { id: 'contact', label: t('navbar.contact') },
]);
const isLoggedIn = computed(() => !!localStorage.getItem('user_id'));
const mostrarBotoes = computed(
  () => !['/login', '/contacts', '/editUser', '/registerUser'].includes(route.path),
);

// Usuário
function getUserData() {
  const raw = localStorage.getItem('usuarioLogado');
  try {
    return raw ? JSON.parse(raw) : {};
  } catch {
    return {};
  }
}
const userData = computed(() => getUserData());
const userImageUrl = computed(() => userData.value.imageUrl || userData.value.image_url || '');
const userName = computed(() => userData.value.usuario || userData.value.name || 'Usuário');
const initials = computed(() => {
  const name = userName.value.trim();
  if (!name) return 'U';
  return name.split(' ').map((p) => p[0]?.toUpperCase()).join('').slice(0, 2);
});

function handleClick(id) {
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });

  if (window?.dataLayer) {
    window.dataLayer.push({
      event: 'gtm_menu_click',
      gtm_button_id: `btn-${id}`,
    });
  }
}
function gotoEditProfile() {
  router.push('/editUser');
}
function logout() {
  localStorage.removeItem('user_id');
  localStorage.removeItem('user_name');
  localStorage.removeItem('usuarioLogado');
  router.push('/login');
}
</script>

<style scoped>
.shadow-header {
  box-shadow: 0 2px 14px 0 rgba(64, 64, 64, 0.05);
}
.avatar-initials, .avatar-initials-big {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1976D2;
  color: #fff;
  border-radius: 50%;
  font-weight: bold;
}
.avatar-initials {
  width: 100%;
  height: 100%;
  font-size: 1.2em;
}
.avatar-initials-big {
  width: 68px;
  height: 68px;
  font-size: 2em;
}
</style>
