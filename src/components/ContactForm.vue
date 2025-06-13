<template>
  <section id="contact" class="section-form flex flex-center">
    <div class="section-content text-center">
      <q-form @submit.prevent="onSubmit" class="q-gutter-md q-mt-md" style="max-width:400px;margin:auto">
        <q-input filled v-model="form.nome" label="Nome" required />
        <q-input filled v-model="form.email" label="E-mail" type="email" required />
        <q-input filled v-model="form.telefone" label="Telefone" />
        <q-select
          filled
          v-model="form.servico"
          :options="['Consultoria','Dashboards','Campanhas']"
          label="Serviço"
        />
        <q-input filled v-model="form.descricao" label="Descrição" type="textarea" />
        <q-btn type="submit" label="Enviar" color="primary" class="full-width" />
      </q-form>
      <p v-if="formEnviado" class="q-mt-md text-italic text-white">
        Horizonte BI<br />Obrigado!
      </p>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const form = ref({
  nome: '', email: '', telefone: '', servico: '', descricao: '',
});
const formEnviado = ref(false);

function onSubmit() {
  formEnviado.value = true;
}
</script>

<style scoped>
.section-form     { background: linear-gradient(135deg, #f7e5d8 0%, #bdfadc 100%); }
</style>
