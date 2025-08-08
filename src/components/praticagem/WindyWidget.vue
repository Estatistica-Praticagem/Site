<template>
  <div class="flex flex-col items-center justify-center min-h-screen w-full bg-transparent py-8">
    <!-- Card do seletor -->
    <div class="w-full flex justify-center mb-8">
      <div class="bg-white rounded-xl shadow-md flex items-center px-8 py-5 gap-3 min-w-[320px] max-w-[500px]">
        <span class="text-base font-semibold text-blue-700 mr-4">Selecione o tipo de visualização:</span>
        <button
          v-for="b in btns"
          :key="b.value"
          @click="selectWidget(b.value)"
          :class="[
            'px-5 py-2 rounded-lg text-base font-bold shadow transition border',
            selected === b.value
              ? 'bg-blue-600 text-white border-blue-700'
              : 'bg-white text-blue-600 border-blue-300 hover:bg-blue-100'
          ]"
        >
          {{ b.label }}
        </button>
      </div>
    </div>

    <!-- Card do widget Windy -->
    <div v-if="selected === 'map'" :key="widgetKey" class="relative bg-white rounded-2xl shadow-md"
      :style="cardStyle(card1)">
      <div class="text-sm text-gray-700 font-bold px-2 pt-2 pb-1">Mapa Windy</div>
      <button class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full px-2 py-1 shadow z-10"
        @click="card1.openConfig = !card1.openConfig" aria-label="Configuração">
        <span class="text-lg font-bold">...</span>
      </button>
      <transition name="fade">
        <div v-if="card1.openConfig"
          class="absolute z-20 top-12 right-2 bg-white rounded-xl shadow-lg p-4 flex flex-col gap-3 min-w-[220px] border border-gray-100">
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações do Mapa</div>
          <ConfigInputs v-model:width="card1.width" v-model:height="card1.height"
            v-model:paddingSide="card1.paddingSide" v-model:paddingTop="card1.paddingTop"
            v-model:paddingBottom="card1.paddingBottom" />
          <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-2 px-3 py-1"
            @click="card1.openConfig = false">
            OK
          </button>
        </div>
      </transition>
      <div ref="windyMap" data-windywidget="map" data-lat="-32.116" data-lon="-52.085" data-zoom="12"
        data-spotid="635778" data-appid="2ab73c6405d90ca519ed4ab7673303d7" data-spots="true" class="w-full h-full"
        style="min-height:150px"></div>
    </div>

    <div v-if="selected === 'forecast'" :key="widgetKey" class="relative bg-white rounded-2xl shadow-md"
      :style="cardStyle(card2)">
      <div class="text-sm text-gray-700 font-bold px-2 pt-2 pb-1">Previsão Windy</div>
      <button class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full px-2 py-1 shadow z-10"
        @click="card2.openConfig = !card2.openConfig" aria-label="Configuração">
        <span class="text-lg font-bold">...</span>
      </button>
      <transition name="fade">
        <div v-if="card2.openConfig"
          class="absolute z-20 top-12 right-2 bg-white rounded-xl shadow-lg p-4 flex flex-col gap-3 min-w-[220px] border border-gray-100">
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações da Previsão</div>
          <ConfigInputs v-model:width="card2.width" v-model:height="card2.height"
            v-model:paddingSide="card2.paddingSide" v-model:paddingTop="card2.paddingTop"
            v-model:paddingBottom="card2.paddingBottom" />
          <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-2 px-3 py-1"
            @click="card2.openConfig = false">
            OK
          </button>
        </div>
      </transition>
      <div ref="windyForecast" data-windywidget="forecast" data-thememode="white" data-spotid="635778"
        data-appid="2ab73c6405d90ca519ed4ab7673303d7" class="w-full h-full" style="min-height:150px"></div>
    </div>

    <div v-if="selected === 'station'" :key="widgetKey" class="relative bg-white rounded-2xl shadow-md"
      :style="cardStyle(card3)">
      <div class="text-sm text-gray-700 font-bold px-2 pt-2 pb-1">Estação Meteorológica (SBPK)</div>
      <button class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full px-2 py-1 shadow z-10"
        @click="card3.openConfig = !card3.openConfig" aria-label="Configuração">
        <span class="text-lg font-bold">...</span>
      </button>
      <transition name="fade">
        <div v-if="card3.openConfig"
          class="absolute z-20 top-12 right-2 bg-white rounded-xl shadow-lg p-4 flex flex-col gap-3 min-w-[220px] border border-gray-100">
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações da Estação</div>
          <ConfigInputs v-model:width="card3.width" v-model:height="card3.height"
            v-model:paddingSide="card3.paddingSide" v-model:paddingTop="card3.paddingTop"
            v-model:paddingBottom="card3.paddingBottom" />
          <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-2 px-3 py-1"
            @click="card3.openConfig = false">
            OK
          </button>
        </div>
      </transition>
      <div ref="windyChart" data-windywidget="windychart" data-meteostationid="SBPK" class="w-full h-full"
        style="min-height:150px"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";

const btns = [
  { value: "map", label: "Mapa Windy" },
  { value: "forecast", label: "Previsão Windy" },
  { value: "station", label: "Estação SBPK" },
];

const selected = ref("map");
const widgetKey = ref(Date.now());

function selectWidget(val) {
  selected.value = val;
  widgetKey.value = Date.now(); // Força o remount do widget!
  nextTick(() => reloadWindyWidget());
}

// Configuração de cada card (width/height/padding/abertura config)
const card1 = ref({ width: 400, height: 340, paddingSide: 40, paddingTop: 40, paddingBottom: 40, openConfig: false });
const card2 = ref({ width: 400, height: 340, paddingSide: 40, paddingTop: 40, paddingBottom: 40, openConfig: false });
const card3 = ref({ width: 400, height: 340, paddingSide: 40, paddingTop: 40, paddingBottom: 40, openConfig: false });

// Componente de Inputs de configuração, SFC style
const ConfigInputs = {
  props: [
    'width', 'height', 'paddingSide', 'paddingTop', 'paddingBottom', 'modelValue', 'modelModifiers'
  ],
  emits: [
    'update:width', 'update:height',
    'update:paddingSide', 'update:paddingTop', 'update:paddingBottom'
  ],
  template: `
    <div>
      <label class="text-xs">Largura (px)</label>
      <input type="number" :value="width" @input="$emit('update:width', Number($event.target.value))" min="200" max="2000" class="border p-1 rounded w-full" />
      <label class="text-xs">Altura (px)</label>
      <input type="number" :value="height" @input="$emit('update:height', Number($event.target.value))" min="200" max="1200" class="border p-1 rounded w-full" />
      <label class="text-xs mt-2">Padding Lateral (px)</label>
      <input type="number" :value="paddingSide" @input="$emit('update:paddingSide', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      <label class="text-xs">Padding Topo (px)</label>
      <input type="number" :value="paddingTop" @input="$emit('update:paddingTop', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      <label class="text-xs">Padding Base (px)</label>
      <input type="number" :value="paddingBottom" @input="$emit('update:paddingBottom', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
    </div>
  `
};

function cardStyle(card) {
  return {
    // eslint-disable-next-line prefer-template
    width: card.width + 'px',
    // eslint-disable-next-line prefer-template
    height: card.height + 'px',
    padding: `${card.paddingTop}px ${card.paddingSide}px ${card.paddingBottom}px ${card.paddingSide}px`,
    transition: 'width .2s, height .2s, padding .2s'
  }
}

function reloadWindyWidget() {
  // Recarrega o script correto conforme selecionado
  const scriptMap = {
    map: "https://windy.app/widget3/windy_map_async.js",
    forecast: "https://windy.app/widgets-code/forecast/windy_forecast_async.js?v168",
    station: "https://windy.app/meteostation-widget/dist/windy-chart.js?v19",
  };
  const src = scriptMap[selected.value];
  if (src && !document.querySelector(`script[src="${src}"]`)) {
    const script = document.createElement("script");
    script.async = true;
    script.type = "text/javascript";
    script.src = src;
    document.body.appendChild(script);
  // eslint-disable-next-line no-underscore-dangle
  } else if (window._windy_widget_reload) {
    // eslint-disable-next-line no-underscore-dangle
    window._windy_widget_reload();
  }
}

onMounted(() => {
  reloadWindyWidget();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
