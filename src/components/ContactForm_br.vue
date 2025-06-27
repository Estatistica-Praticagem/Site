<template>
  <section id="contact" class="section-form flex flex-center" data-gtm="section-contact">
    <div class="section-content">
      <h2 class="text-h4 text-primary text-center q-mb-md text-weight-bold">
        Entre em Contato
      </h2>
      <q-form
        ref="formRef"
        @submit.prevent="onSubmit"
        class="form-wrapper"
        id="form-contato"
        data-gtm="form-contato"
      >
        <q-input
          filled
          v-model="form.nome"
          label="Nome"
          maxlength="100"
          required
          id="input-nome"
          data-gtm="input-nome"
        />

        <q-input
          filled
          v-model="form.email"
          label="E-mail"
          type="email"
          maxlength="100"
          required
          id="input-email"
          data-gtm="input-email"
        />

        <div class="row q-col-gutter-sm">
          <div class="col-4">
            <q-input
              filled
              v-model="form.ddd"
              label="DDD / País"
              maxlength="6"
              id="input-ddd"
              data-gtm="input-ddd"
            />
          </div>
          <div class="col-8">
            <q-input
              filled
              v-model="form.telefone"
              label="Telefone (opcional)"
              maxlength="20"
              id="input-telefone"
              data-gtm="input-telefone"
            />
          </div>
        </div>

        <q-select
          filled
          v-model="form.servico"
          :options="[
            'Serviços de markting',
            'Estudos / consultoria',
            'Construção de base de dados e dashboards',
            'Soluções em ML: Preditivas / MMM'
          ]"
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
          maxlength="5000"
          id="input-descricao"
          data-gtm="input-descricao"
          class="large-textarea"
        />

        <q-btn
          :loading="loading"
          :disable="loading"
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
            <!-- reCAPTCHA invisível -->
        <div ref="recaptchaEl" class="g-recaptcha recaptcha-fixed" id="recaptcha-bloco" data-gtm="recaptcha-bloco"></div>

  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Notify } from 'quasar';
import { contemConteudoProibido } from 'src/utils/InputFilter';

const siteKey = '6Le2FWsrAAAAAB4hzU3lQ5GU1FCSTLBYTlvFaNa7';
const urlSalva = localStorage.getItem('utm_full_url');
const urlAtual = urlSalva || window.location.href;

const form = ref({
  nome: '',
  email: '',
  ddd: '',
  telefone: '',
  servico: 'Serviços de markting',
  descricao: '',
  origem_url: urlAtual, // adiciona a origem aqui
});

const formRef = ref(null);
const formEnviado = ref(false);
const loading = ref(false);
const recaptchaWidgetId = ref(null);

function validarCampos() {
  const camposObrigatorios = ['email', 'servico', 'descricao'];
  // eslint-disable-next-line no-restricted-syntax
  for (const campo of camposObrigatorios) {
    if (!form.value[campo] || form.value[campo].trim() === '') {
      Notify.create({ type: 'warning', message: `O campo "${campo}" é obrigatório.` });
      return false;
    }
  }

  if (contemConteudoProibido(form.value.nome) || contemConteudoProibido(form.value.descricao)) {
    Notify.create({
      type: 'negative',
      message: 'Seu texto contém termos, links ou palavras proibidas.',
    });
    return false;
  }
  return true;
}

async function submitForm(token) {
  loading.value = true;
  try {
    const payload = { ...form.value, 'g-recaptcha-response': token };
    const response = await fetch('https://www.meusimulador.com/kevi/backend/contacts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await response.json();

    if (data.success) {
      if (window.dataLayer) {
        window.dataLayer.push({
          event: 'lead_enviado',
          lead_nome: form.value.nome,
          lead_email: form.value.email,
          lead_ddd: form.value.ddd,
          lead_telefone: form.value.telefone,
          lead_servico: form.value.servico,
          lead_descricao: form.value.descricao,
          lead_origem_url: form.value.origem_url,
        });
      }

      formEnviado.value = true;
      form.value = {
        nome: '', email: '', ddd: '', telefone: '', servico: '', descricao: '', origem_url: urlAtual,
      };
      formRef.value.resetValidation();
      // eslint-disable-next-line no-return-assign
      setTimeout(() => (formEnviado.value = false), 20000);
    } else {
      Notify.create({ type: 'negative', message: data.message || 'Erro ao enviar o formulário.' });
    }
  } catch (error) {
    Notify.create({ type: 'negative', message: 'Erro de conexão com o servidor.' });
    console.error(error);
  } finally {
    loading.value = false;
    if (window.grecaptcha && recaptchaWidgetId.value !== null) {
      window.grecaptcha.reset(recaptchaWidgetId.value);
    }
  }
}

function onRecaptchaSuccess(token) {
  submitForm(token);
}

function onSubmit() {
  if (!validarCampos()) return;
  if (window.grecaptcha && recaptchaWidgetId.value !== null) {
    window.grecaptcha.execute(recaptchaWidgetId.value);
  } else {
    Notify.create({ type: 'negative', message: 'reCAPTCHA não carregado.' });
  }
}

onMounted(() => {
  window.onRecaptchaSuccess = onRecaptchaSuccess;
  if (window.grecaptcha) {
    window.grecaptcha.ready(() => {
      recaptchaWidgetId.value = window.grecaptcha.render(document.getElementById('recaptcha-bloco'), {
        sitekey: siteKey,
        size: 'invisible',
        callback: 'onRecaptchaSuccess',
      });
    });
  }
});
</script>

<style scoped>
.section-form {
  background: transparent;
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
.large-textarea ::v-deep textarea.q-field__native {
  min-height: 140px;
  padding: 12px;
  line-height: 1.6;
  resize: vertical;
}
textarea.q-field__native {
  resize: vertical !important;
}
</style>
<style>
.grecaptcha-badge {
  position: fixed;
  bottom: 20;
  right: 20;
  z-index: 99;
  /* transform: scale(0.7); */
  /* transform-origin: bottom right;
  margin: 4px !important;
  opacity: 0.9;
  pointer-events: auto; */
}
</style>
