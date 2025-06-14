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
          <div class="text-caption text-white q-mt-xs">https://www.linkedin.com/company/orizzonttebi</div>
        </div>
      </div>
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

</script>

<style scoped>
.section-contact  { background: linear-gradient(135deg, #0b4f76 0%, #47e295 100%); }
</style>
