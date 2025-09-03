<template>
  <div class="flex flex-col items-center justify-center py-12">
    <div class="text-lg font-bold mb-4"></div>
    <q-btn
      color="primary"
      rounded
      class="text-white text-lg q-px-xl q-py-md shadow-lg"
      label="Abrir previsão Windguru 🌬️"
      @click="abrirWindguru"
    />
  </div>
</template>

<script setup>
function abrirWindguru() {
  window.open('https://www.windguru.cz/2169', '_blank')
}
</script>
