<template>
  <section id="contact" class="section-form flex flex-center" data-gtm="section-contact">
    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md text-weight-bold">Entre em Contato</h2>
      <q-form @submit.prevent="onSubmit" class="form-wrapper" id="form-contato" data-gtm="form-contato">
        <q-input
          filled
          v-model="form.nome"
          label="Nome"
          required
          id="input-nome"
          data-gtm="input-nome"
        />
        <q-input
          filled
          v-model="form.email"
          label="E-mail"
          type="email"
          required
          id="input-email"
          data-gtm="input-email"
        />
        <q-input
          filled
          v-model="form.telefone"
          label="Telefone"
          mask="(##) #####-####"
          id="input-telefone"
          data-gtm="input-telefone"
        />
        <q-select
          filled
          v-model="form.servico"
          :options="['Consultoria', 'Dashboards', 'Campanhas']"
          label="Serviço de Interesse"
          emit-value
          map-options
          id="select-servico"
          data-gtm="select-servico"
        />
        <q-input
          filled
          v-model="form.descricao"
          label="Como podemos ajudar?"
          type="textarea"
          autogrow
          id="input-descricao"
          data-gtm="input-descricao"
        />
        <q-btn
          type="submit"
          label="Enviar"
          color="primary"
          class="full-width q-mt-md"
          id="btn-enviar-contato"
          data-gtm="btn-enviar-contato"
        />
      </q-form>

      <q-banner
        v-if="formEnviado"
        class="q-mt-lg bg-primary text-white text-center rounded-borders"
        dense
      >
        <q-icon name="check_circle" class="q-mr-sm" />
        Mensagem enviada com sucesso! Obrigado por entrar em contato.
      </q-banner>
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

  // Aqui você pode adicionar integração com API ou envio por e-mail futuramente
  setTimeout(() => {
    form.value = {
      nome: '', email: '', telefone: '', servico: '', descricao: '',
    };
    // eslint-disable-next-line no-return-assign
    setTimeout(() => (formEnviado.value = false), 4000);
  }, 100);
}
</script>

<style scoped>
.section-form {
  /* background: #ffffff; */
  background: linear-gradient(135deg, #f7e5d8 0%, #bdfadc 100%);
  /* background:linear-gradient(135deg,#f6fbff 0%,#dff9ff 100%); */
  padding: 80px 20px;
  min-height: 100vh;
  display: flex;
  align-items: center;
}

.section-content {
  max-width: 600px;
  width: 100%;
  background: #ffffffdd;
  backdrop-filter: blur(4px);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  margin: auto;
}

.form-wrapper {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.full-width {
  width: 100%;
}
</style>
