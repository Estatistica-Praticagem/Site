<template>
  <q-page class="q-pa-md" style="max-width: 600px; margin: auto">
    <h2 class="text-h5 text-center">Cadastro de Usuário</h2>
    <q-form @submit.prevent="cadastrarUsuario" class="q-gutter-md">
      <q-input v-model="usuario" label="Nome" outlined :rules="[(val) => !!val || 'Campo obrigatório']" />
      <q-input v-model="email" label="E-mail" type="email" outlined :rules="[(val) => !!val || 'Campo obrigatório']" />
      <q-input v-model="senha" label="Senha" type="password" outlined :rules="[(val) => !!val || 'Campo obrigatório']" />
      <q-btn type="submit" label="Cadastrar" color="primary" class="full-width" unelevated rounded />
    </q-form>
    <q-banner v-if="mensagem" class="q-mt-md bg-positive text-white">{{ mensagem }}</q-banner>
    <q-banner v-if="erro" class="q-mt-md bg-negative text-white">{{ erro }}</q-banner>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';

const usuario = ref('');
const email = ref('');
const senha = ref('');
const mensagem = ref('');
const erro = ref('');

async function cadastrarUsuario() {
  mensagem.value = '';
  erro.value = '';

  const formData = new FormData();
  formData.append('usuario', usuario.value);
  formData.append('email', email.value);
  formData.append('senha', senha.value);
  const userIdLogado = localStorage.getItem('user_id');
  formData.append('usuario_id', userIdLogado);

  try {
    const resp = await fetch('https://www.meusimulador.com/kevi/backend/user_register.php', {
      method: 'POST',
      body: formData,
    });
    const data = await resp.json();

    if (data.status === 'sucesso') {
      mensagem.value = data.mensagem;
      erro.value = '';
      usuario.value = '';
      email.value = '';
      senha.value = '';
    } else {
      erro.value = data.mensagem || 'Erro ao cadastrar';
    }
  } catch (e) {
    erro.value = 'Erro de rede.';
  }
}
</script>
