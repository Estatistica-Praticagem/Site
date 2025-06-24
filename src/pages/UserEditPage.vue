<template>
  <q-page class="q-pa-md" style="max-width: 600px; margin: auto">
    <h2 class="text-h5 text-center">Editar Perfil</h2>
    <q-form @submit.prevent="atualizarUsuario" class="q-gutter-md">
      <q-input v-model="usuario" label="Nome" outlined />
      <q-input v-model="email" label="E-mail" type="email" outlined />
      <q-input v-model="senha" label="Nova senha" type="password" outlined />
      <div>
        <label class="text-caption">Foto de Perfil</label>
        <input type="file" accept="image/*" @change="handleFile" />
        <div v-if="urlImagemAtual" class="q-mt-xs">
          <img :src="urlImagemAtual" style="height: 80px; border-radius: 50%" alt="foto atual" />
        </div>
      </div>
      <q-btn type="submit" label="Salvar Alterações" color="primary" class="full-width" unelevated rounded />
    </q-form>
    <q-banner v-if="mensagem" class="q-mt-md bg-positive text-white">{{ mensagem }}</q-banner>
    <q-banner v-if="erro" class="q-mt-md bg-negative text-white">{{ erro }}</q-banner>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';

const userStorage = JSON.parse(localStorage.getItem('usuarioLogado')) || {};
const usuario = ref(userStorage.usuario || '');
const email = ref(userStorage.email || '');
const senha = ref('');
const imagem = ref(null);
const urlImagemAtual = ref(userStorage.imageUrl || '');

const mensagem = ref('');
const erro = ref('');

function handleFile(e) {
  // eslint-disable-next-line prefer-destructuring
  imagem.value = e.target.files[0];
}

async function atualizarUsuario() {
  mensagem.value = '';
  erro.value = '';

  const formData = new FormData();
  formData.append('usuario_id', localStorage.getItem('user_id'));

  if (usuario.value && usuario.value !== userStorage.usuario) {
    formData.append('usuario', usuario.value);
  }

  if (email.value && email.value !== userStorage.email) {
    formData.append('email', email.value);
  }

  if (senha.value) {
    formData.append('senha', senha.value);
  }

  if (imagem.value) {
    formData.append('imagem', imagem.value);
  }

  try {
    const resp = await fetch('https://www.meusimulador.com/kevi/backend/user-edit.php', {
      method: 'POST',
      body: formData,
    });
    const data = await resp.json();

    if (data.status === 'sucesso') {
      mensagem.value = data.mensagem;
      erro.value = '';

      // Atualiza localStorage apenas se tiverem sido enviados
      if (formData.has('usuario')) userStorage.usuario = usuario.value;
      if (formData.has('email')) userStorage.email = email.value;
      if (data.imageUrl) {
        userStorage.imageUrl = data.imageUrl;
        urlImagemAtual.value = data.imageUrl;
      }

      localStorage.setItem('usuarioLogado', JSON.stringify(userStorage));
    } else {
      erro.value = data.mensagem || 'Erro ao atualizar';
    }
  } catch (e) {
    erro.value = 'Erro de rede.';
  }
}
</script>
