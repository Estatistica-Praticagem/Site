<template>
  <div>
    <!-- Botão flutuante para abrir a webview -->
    <q-btn
      v-if="!showIframe"
      class="q-pa-sm"
      style="position:fixed;top:30px;right:30px;z-index:9999"
      color="primary"
      rounded
      label="Ver Previsão dos Ventos 🌬️"
      @click="showIframe = true"
    />

    <!-- Webview (iframe) em tela cheia -->
    <div v-if="showIframe" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:#fff;">
      <iframe
        :src="iframeUrl"
        style="width:100vw;height:100vh;border:none;"
      ></iframe>

      <!-- Botão flutuante para fechar a webview -->
      <q-btn
        class="q-pa-sm"
        style="position:absolute;top:30px;right:30px;z-index:10001"
        color="negative"
        rounded
        label="Fechar Webview"
        @click="showIframe = false"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const showIframe = ref(false)
const iframeUrl = 'https://muitobonsventos.com.br/'
</script>
