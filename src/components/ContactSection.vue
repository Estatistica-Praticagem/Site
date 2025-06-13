<template>
  <section id="contact-info" class="section-contact flex flex-center">
    <div class="section-content text-center">
      <h2 class="text-h5 text-white q-mb-sm">Entre em Contato</h2>
      <div class="row justify-center items-center q-gutter-lg">
        <div>
          <q-btn outline color="white" icon="email" label="E-mail" @click="mailto" />
          <div class="text-caption text-white q-mt-xs">horizontetbi@gmail.com</div>
        </div>
        <div>
          <q-btn outline color="white" icon="linkedin" label="LinkedIn" @click="linkedin" />
          <div class="text-caption text-white q-mt-xs">linkedin.com/company/orizzonttebi</div>
        </div>
      </div>
      <q-btn
        color="white"
        text-color="primary"
        label="Fale Conosco"
        class="q-mt-lg"
        size="lg"
        @click="abrirFormulario"
      />
    </div>
  </section>
</template>

<script setup>
function mailto() {
  window.open('mailto:horizontetbi@gmail.com', '_blank');
}
function linkedin() {
  window.open('https://linkedin.com/company/horizontebi', '_blank');
}
function abrirFormulario() {
  // Dispare um evento ou use um prop para abrir o formulário na página principal
  // Exemplo: emit('openForm')
}
</script>

<style scoped>
.section-contact  { background: linear-gradient(135deg, #0b4f76 0%, #47e295 100%); }
</style>
