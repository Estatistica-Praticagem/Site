<!-- WindyWidget.vue -->
<template>
  <div
    class="flex items-center justify-center min-h-[100vh] min-w-full bg-transparent"
    :style="{
      padding: `${paddingTop}px ${paddingSide}px ${paddingBottom}px ${paddingSide}px`
    }"
  >
    <div
      class="relative bg-white rounded-2xl shadow-md transition-all"
      :style="{
        width: width + 'px',
        height: height + 'px',
        transition: 'width .2s, height .2s, padding .2s'
      }"
    >
      <!-- Botão de configuração -->
      <button
        class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full px-2 py-1 shadow z-10"
        @click="openConfig = !openConfig"
        aria-label="Configuração"
      >
        <span class="text-lg font-bold">...</span>
      </button>

      <!-- Card de configuração -->
      <transition name="fade">
        <div
          v-if="openConfig"
          class="absolute z-20 top-12 right-2 bg-white rounded-xl shadow-lg p-4 flex flex-col gap-3 min-w-[220px] border border-gray-100"
        >
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações do Widget</div>
          <div class="flex flex-col gap-2">
            <label class="text-xs">Largura (px)</label>
            <input type="number" v-model.number="width" min="200" max="2000" class="border p-1 rounded w-full" />
            <label class="text-xs">Altura (px)</label>
            <input type="number" v-model.number="height" min="200" max="1200" class="border p-1 rounded w-full" />

            <label class="text-xs mt-2">Padding Lateral (px)</label>
            <input type="number" v-model.number="paddingSide" min="0" max="200" class="border p-1 rounded w-full" />
            <label class="text-xs">Padding Topo (px)</label>
            <input type="number" v-model.number="paddingTop" min="0" max="200" class="border p-1 rounded w-full" />
            <label class="text-xs">Padding Base (px)</label>
            <input type="number" v-model.number="paddingBottom" min="0" max="200" class="border p-1 rounded w-full" />
          </div>
          <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-2 px-3 py-1" @click="openConfig = false">
            OK
          </button>
        </div>
      </transition>

      <!-- Widget Windy -->
      <div
        ref="windyDiv"
        :data-windywidget="'map'"
        :data-spotid="spotId"
        :data-appid="appId"
        :data-spots="true"
        class="w-full h-full"
        style="min-height:150px"
      ></div>
    </div>
  </div>
</template>

<script setup>
// eslint-disable-next-line import/newline-after-import
import { ref, computed, onMounted, nextTick, watch } from "vue";
const spotId = "635778";
const appId = "2ab73c6405d90ca519ed4ab7673303d7";
const openConfig = ref(false);

const width = ref(400);
const height = ref(340);
const paddingSide = ref(40);
const paddingTop = ref(40);
const paddingBottom = ref(40);

function reloadWindyWidget() {
  nextTick(() => {
    // eslint-disable-next-line no-underscore-dangle
    if (window._windy_widget_reload) window._windy_widget_reload();
  });
}

onMounted(() => {
  if (!document.querySelector('script[src="https://windy.app/widget3/windy_map_async.js"]')) {
    const script = document.createElement("script");
    script.async = true;
    script.type = "text/javascript";
    script.src = "https://windy.app/widget3/windy_map_async.js";
    script.onload = reloadWindyWidget;
    document.body.appendChild(script);
  } else {
    reloadWindyWidget();
  }
});

watch([width, height, paddingSide, paddingTop, paddingBottom], () => {
  reloadWindyWidget();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
