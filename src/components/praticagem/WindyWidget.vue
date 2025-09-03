<template>
  <div class="flex flex-col items-center justify-center min-h-screen w-full bg-transparent py-8">
    <!-- Card do seletor -->
    <div class="w-full flex justify-center mb-8">
      <div
        class="bg-white rounded-xl shadow-md flex flex-wrap items-center px-8 py-5 gap-3 min-w-[320px] max-w-[800px] relative"
      >
        <span class="text-base font-semibold text-blue-700 mr-2">Selecione o tipo de visualização:</span>

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

        <!-- Botão global de configurações (abre a config do widget selecionado) -->
        <button
          class="ml-auto bg-white/90 hover:bg-white rounded-full px-3 py-1 shadow border"
          @click="toggleTopConfig"
          aria-label="Configuração do widget atual"
          title="Configurar"
        >
          <span class="text-lg font-bold">...</span>
        </button>
      </div>
    </div>

    <!-- Mapa Windy -->
    <div
      v-if="selected === 'map'"
      :key="widgetKey"
      class="relative bg-white isolate overflow-visible"
      :class="cardClass(card1)"
      :style="cardStyle(card1)"
    >
      <div class="text-sm text-gray-700 font-bold px-2 pt-2 pb-1">Mapa Windy</div>

      <transition name="fade">
        <div
          v-if="card1.openConfig"
          class="absolute rounded-xl shadow-lg border"
          :style="configPanelStyle(card1)"
        >
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações do Mapa</div>
          <ConfigInputs
            v-model:fullWidth="card1.fullWidth"
            v-model:maxWidth="card1.maxWidth"
            v-model:width="card1.width"
            v-model:height="card1.height"
            v-model:paddingSide="card1.paddingSide"
            v-model:paddingTop="card1.paddingTop"
            v-model:paddingBottom="card1.paddingBottom"
            v-model:marginSide="card1.marginSide"
            v-model:marginTop="card1.marginTop"
            v-model:marginBottom="card1.marginBottom"
            v-model:shape="card1.shape"
            v-model:shadow="card1.shadow"
            v-model:border="card1.border"
            v-model:configPadding="card1.configPadding"
            v-model:configOffsetX="card1.configOffsetX"
            v-model:configOffsetY="card1.configOffsetY"
          />
          <div class="flex justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-3 px-3 py-1" @click="card1.openConfig = false">
              OK
            </button>
          </div>
        </div>
      </transition>

      <div
        ref="windyMap"
        data-windywidget="map"
        data-lat="-32.116"
        data-lon="-52.085"
        data-zoom="12"
        data-spotid="635778"
        data-appid="2ab73c6405d90ca519ed4ab7673303d7"
        data-spots="true"
        class="w-full h-full"
        style="min-height:150px"
      ></div>
    </div>

    <!-- Previsão Windy -->
    <div
      v-if="selected === 'forecast'"
      :key="widgetKey"
      class="relative bg-white isolate overflow-visible"
      :class="cardClass(card2)"
      :style="cardStyle(card2)"
    >
      <div class="text-sm text-gray-700 font-bold px-2 pt-2 pb-1">Previsão Windy</div>

      <transition name="fade">
        <div
          v-if="card2.openConfig"
          class="absolute rounded-xl shadow-lg border"
          :style="configPanelStyle(card2)"
        >
          <div class="mb-2 font-bold text-sm text-gray-700">Configurações da Previsão</div>

          <div class="grid grid-cols-1 gap-2 mb-2">
            <label class="text-xs font-semibold">Tema</label>
            <select v-model="card2.theme" class="border p-1 rounded w-full">
              <option value="white">White</option>
              <option value="dark">Dark</option>
            </select>
          </div>

          <ConfigInputs
            v-model:fullWidth="card2.fullWidth"
            v-model:maxWidth="card2.maxWidth"
            v-model:width="card2.width"
            v-model:height="card2.height"
            v-model:paddingSide="card2.paddingSide"
            v-model:paddingTop="card2.paddingTop"
            v-model:paddingBottom="card2.paddingBottom"
            v-model:marginSide="card2.marginSide"
            v-model:marginTop="card2.marginTop"
            v-model:marginBottom="card2.marginBottom"
            v-model:shape="card2.shape"
            v-model:shadow="card2.shadow"
            v-model:border="card2.border"
            v-model:configPadding="card2.configPadding"
            v-model:configOffsetX="card2.configOffsetX"
            v-model:configOffsetY="card2.configOffsetY"
          />
          <div class="flex justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded mt-3 px-3 py-1" @click="card2.openConfig = false">
              OK
            </button>
          </div>
        </div>
      </transition>

      <div
        ref="windyForecast"
        data-windywidget="forecast"
        :data-thememode="card2.theme"
        data-spotid="635778"
        data-appid="2ab73c6405d90ca519ed4ab7673303d7"
        class="w-full h-full"
        style="min-height:150px"
      ></div>
    </div>

    <!-- Freamer (Pena): Botão flutuante webview -->
    <div v-if="selected === 'freamer'">
      <WindyWidget />
    </div>
        <!-- Freamer (Pena): Botão flutuante webview -->
    <div v-if="selected === 'Windguru'">
      <WindguruWidget />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue"
import WindyWidget from 'src/components/MuitosBonsVentosWidget.vue'
import WindguruWidget from 'components/WindguruWidget.vue'

const btns = [
  { value: "map", label: "Mapa Windy" },
  { value: "forecast", label: "Previsão Windy" },
  { value: "freamer", label: "Bons Ventos" },
  { value: "Windguru", label: "Windguru" },
]

const selected = ref("map")
const widgetKey = ref(Date.now())

// ======= Tailwind helpers =======
const SHAPES = { sm: "rounded-md", md: "rounded-xl", lg: "rounded-2xl", xl: "rounded-3xl" };
const SHADOWS = { none: "shadow-none", sm: "shadow", md: "shadow-md", lg: "shadow-lg", xl: "shadow-xl" };

// ======= Estado padrão dos cards =======
function makeCardDefaults() {
  return {
    fullWidth: true,
    maxWidth: 1400,
    width: 960,
    height: 420,
    paddingSide: 40,
    paddingTop: 40,
    paddingBottom: 40,
    marginSide: 0,
    marginTop: 0,
    marginBottom: 24,
    shape: "lg",
    shadow: "md",
    border: true,
    theme: "white",
    openConfig: false,
    configPadding: 16,
    configOffsetX: 8,
    configOffsetY: 40,
  };
}

const card1 = ref(makeCardDefaults());
const card2 = ref({ ...makeCardDefaults(), height: 700, theme: "white" });
const cardFreamer = ref({ ...makeCardDefaults(), height: 600 });
const freamerUrl = "https://app.freamer.com.br/EXEMPLO"; // Troque para o link correto!

// ======= Componente de Inputs =======
const ConfigInputs = {
  props: [
    "fullWidth", "maxWidth",
    "width", "height",
    "paddingSide", "paddingTop", "paddingBottom",
    "marginSide", "marginTop", "marginBottom",
    "shape", "shadow", "border",
    "configPadding", "configOffsetX", "configOffsetY",
  ],
  emits: [
    "update:fullWidth", "update:maxWidth",
    "update:width", "update:height",
    "update:paddingSide", "update:paddingTop", "update:paddingBottom",
    "update:marginSide", "update:marginTop", "update:marginBottom",
    "update:shape", "update:shadow", "update:border",
    "update:configPadding", "update:configOffsetX", "update:configOffsetY",
  ],
  template: `
    <div class="grid grid-cols-2 gap-3">
      <div class="col-span-2 text-xs font-semibold text-gray-600">Largura</div>
      <div class="col-span-2 flex items-center gap-2">
        <input id="fwSwitch" type="checkbox" :checked="fullWidth" @change="$emit('update:fullWidth', $event.target.checked)" />
        <label for="fwSwitch" class="text-xs">Usar largura total (100%)</label>
      </div>

      <div class="col-span-2" v-if="fullWidth">
        <label class="text-xs">Largura máxima (px) quando 100%</label>
        <input type="number" :value="maxWidth" @input="$emit('update:maxWidth', Number($event.target.value))" min="320" max="2400" class="border p-1 rounded w-full" />
      </div>

      <div v-else>
        <label class="text-xs">Largura (px)</label>
        <input type="number" :value="width" @input="$emit('update:width', Number($event.target.value))" min="240" max="2000" class="border p-1 rounded w-full" />
      </div>
      <div>
        <label class="text-xs">Altura (px)</label>
        <input type="number" :value="height" @input="$emit('update:height', Number($event.target.value))" min="180" max="1400" class="border p-1 rounded w-full" />
      </div>

      <div class="col-span-2 text-xs font-semibold text-gray-600 mt-1">Padding (interno)</div>
      <div>
        <label class="text-xs">Lateral (px)</label>
        <input type="number" :value="paddingSide" @input="$emit('update:paddingSide', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>
      <div>
        <label class="text-xs">Topo (px)</label>
        <input type="number" :value="paddingTop" @input="$emit('update:paddingTop', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>
      <div>
        <label class="text-xs">Base (px)</label>
        <input type="number" :value="paddingBottom" @input="$emit('update:paddingBottom', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>

      <div class="col-span-2 text-xs font-semibold text-gray-600 mt-1">Margin (externo)</div>
      <div>
        <label class="text-xs">Lateral (px)</label>
        <input type="number" :value="marginSide" @input="$emit('update:marginSide', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>
      <div>
        <label class="text-xs">Topo (px)</label>
        <input type="number" :value="marginTop" @input="$emit('update:marginTop', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>
      <div>
        <label class="text-xs">Base (px)</label>
        <input type="number" :value="marginBottom" @input="$emit('update:marginBottom', Number($event.target.value))" min="0" max="200" class="border p-1 rounded w-full" />
      </div>

      <div class="col-span-2 text-xs font-semibold text-gray-600 mt-1">Forma de visualização</div>
      <div>
        <label class="text-xs">Cantos</label>
        <select :value="shape" @change="$emit('update:shape', $event.target.value)" class="border p-1 rounded w-full">
          <option value="sm">Arredondado sm</option>
          <option value="md">Arredondado md</option>
          <option value="lg">Arredondado lg</option>
          <option value="xl">Arredondado xl</option>
        </select>
      </div>
      <div>
        <label class="text-xs">Sombra</label>
        <select :value="shadow" @change="$emit('update:shadow', $event.target.value)" class="border p-1 rounded w-full">
          <option value="none">Sem sombra</option>
          <option value="sm">Leve</option>
          <option value="md">Média</option>
          <option value="lg">Forte</option>
          <option value="xl">Extra</option>
        </select>
      </div>
      <div class="col-span-2 flex items-center gap-2">
        <input id="borderSwitch" type="checkbox" :checked="border" @change="$emit('update:border', $event.target.checked)" />
        <label for="borderSwitch" class="text-xs">Exibir borda</label>
      </div>
    </div>
  `,
};

// ======= Helpers de classe/estilo =======
function cardClass(card) {
  return [
    SHAPES[card.shape] ?? SHAPES.lg,
    SHADOWS[card.shadow] ?? SHADOWS.md,
    card.border ? "border" : "border-0",
  ];
}
function cardStyle(card) {
  const base = {
    width: card.fullWidth ? "100%" : `${card.width}px`,
    maxWidth: card.fullWidth ? `${card.maxWidth}px` : "none",
    height: `${card.height}px`,
    padding: `${card.paddingTop}px ${card.paddingSide}px ${card.paddingBottom}px ${card.paddingSide}px`,
    margin: `${card.marginTop}px ${card.marginSide}px ${card.marginBottom}px ${card.marginSide}px`,
    transition: "width .2s, height .2s, padding .2s, margin .2s",
  };
  if (card.fullWidth) { base.marginLeft = "auto"; base.marginRight = "auto"; }
  return base;
}
function configPanelStyle(card) {
  // Sobrepõe o iframe do Windy ou Freamer
  return {
    top: `${card.configOffsetY}px`,
    right: `${card.configOffsetX}px`,
    padding: `${card.configPadding}px`,
    minWidth: "260px",
    background: "rgba(255,255,255,0.62)",
    zIndex: 2147483647,
    pointerEvents: "auto",
    boxShadow: "0 10px 28px rgba(0,0,0,.16)",
  };
}

// ======= Script de reload dos widgets =======
const windyMap = ref(null);
const windyForecast = ref(null);

const scriptMap = {
  map: "https://windy.app/widget3/windy_map_async.js",
  forecast: "https://windy.app/widgets-code/forecast/windy_forecast_async.js?v168",
};

function forceScriptReload(type) {
  const src = scriptMap[type];
  if (!src) return;
  const current = Array.from(document.querySelectorAll("script")).find(s => s.src && s.src.startsWith(src));
  if (current) current.remove();

  const s = document.createElement("script");
  s.async = true;
  s.type = "text/javascript";
  s.src = `${src + (src.includes("?") ? "&" : "?")}t=${Date.now()}`;
  document.body.appendChild(s);
}

async function reloadWindyWidget() {
  await nextTick();
  // eslint-disable-next-line no-underscore-dangle
  if (window._windy_widget_reload) {
    // eslint-disable-next-line no-underscore-dangle
    window._windy_widget_reload();
  } else {
    forceScriptReload(selected.value);
  }
}

function toggleTopConfig() {
  // fecha todos e abre o certo
  card1.value.openConfig = false;
  card2.value.openConfig = false;
  cardFreamer.value.openConfig = false;

  if (selected.value === "map") card1.value.openConfig = !card1.value.openConfig;
  else if (selected.value === "forecast") card2.value.openConfig = !card2.value.openConfig;
  else if (selected.value === "freamer") cardFreamer.value.openConfig = !cardFreamer.value.openConfig;
}

function selectWidget(val) {
  card1.value.openConfig = false;
  card2.value.openConfig = false;
  cardFreamer.value.openConfig = false;

  selected.value = val;
  widgetKey.value = Date.now();
  nextTick(() => {
    if (val === "map" || val === "forecast") {
      forceScriptReload(val);
      setTimeout(reloadWindyWidget, 50);
    }
  });
}
watch(selected, (val) => {
  if (val !== 'Windguru' && showWidget.value) {
    showWidget.value = false;
  }
})

// Recarrega quando o tema da previsão muda
watch(() => card2.value.theme, () => {
  if (selected.value === "forecast") reloadWindyWidget();
});

onMounted(() => {
  forceScriptReload(selected.value);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
