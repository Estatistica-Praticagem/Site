<template>
  <nav id="navbar-sections" class="navbar-sections q-pa-md">
    <div class="row justify-center q-gutter-sm">
      <q-btn
        v-for="(section, idx) in sections"
        :key="section.anchor + idx"
        :id="`btn-${section.anchor.toLowerCase().replace(/\s+/g, '-')}`"
        :data-gtm="`btn-${section.anchor.toLowerCase().replace(/\s+/g, '-')}`"
        flat
        color="primary"
        :label="section.label"
        @click="scrollTo(section.anchor)"
        class="text-body1"
      />
    </div>
  </nav>
</template>

<script setup>
// eslint-disable-next-line no-unused-vars
const props = defineProps({
  sections: {
    type: Array,
    default: () => ([
      { label: 'Clientes', anchor: 'clients' },
      { label: 'Serviços', anchor: 'services' },
      { label: 'Equipe', anchor: 'team' },
      { label: 'Contato', anchor: 'contact-info' },
    ]),
  },
});

function scrollTo(anchor) {
  const el = document.getElementById(anchor);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.navbar-sections {
  position: sticky;
  top: 0;
  z-index: 5;
  background: transparent;
  width: 100%;
}
</style>
