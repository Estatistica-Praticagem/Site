<template>
  <q-layout view="lhr Lpr lFf">
    <q-header elevated>
      <q-toolbar class="justify-center q-py-sm">
        <q-toolbar-title class="full-width text-center">
          <div>
            <h2 class="q-mt-sm q-mb-xs">Horizonte BI</h2>
            <h6 class="q-mt-none q-mb-sm">Global Solutions, Local Results</h6>
          </div>
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue';
// import EssentialLink from 'components/EssentialLink.vue';

const linksList = [
  {
    title: 'Horizonte BI',
    caption: '',
    icon: 'mdi-deskphone',
    link: 'https://quasar.dev',
  },
];

export default defineComponent({
  name: 'MainLayout',

  components: {
    // EssentialLink,
  },

  data() {
    return {
      linksList,
      leftDrawerOpen: false,
    };
  },

  methods: {
    toggleLeftDrawer() {
      this.leftDrawerOpen = !this.leftDrawerOpen;
    },
  },
});
</script>
