<template>
  <div class="gauge-wrap">
    <!-- RELÓGIO (clique para abrir configurações) -->
    <svg
      :width="size"
      :height="size"
      :viewBox="`0 0 ${size} ${size}`"
      class="gauge-svg"
      @click="showConfig = true"
      style="cursor: pointer"
    >
      <defs>
        <radialGradient :id="gradId" cx="50%" cy="50%" r="70%">
          <stop offset="0%" stop-color="#ffffff" />
          <stop offset="100%" stop-color="#f2f6fb" />
        </radialGradient>
      </defs>

      <!-- Círculo externo do relógio -->
      <circle
        :cx="center"
        :cy="center"
        :r="radius"
        :fill="clockFill"
        :stroke="clockStroke"
        :stroke-width="cfg.showBorder ? 1 : 0"
      />

      <!-- Seta -->
      <text
        :x="center"
        :y="center - radius * 0.70"
        text-anchor="middle"
        dominant-baseline="middle"
        :font-size="cfg.arrowSizePx"
        :fill="arrowColor"
        :transform="`rotate(${pointerAngle} ${center} ${center})`"
        style="user-select:none"
      >
        {{ arrowGlyph }}
      </text>

      <!-- Miolo (círculo interno) -->
      <circle
        :cx="center"
        :cy="center"
        :r="size * 0.18"
        fill="#fff"
        stroke="#e3e3e3"
        stroke-width="2"
      />
      <!-- Valor de intensidade no miolo (mantido) -->
      <text
        :x="center"
        :y="center + size * 0.04"
        text-anchor="middle"
        :font-size="size * 0.15"
        fill="#333"
        font-weight="bold"
        style="font-variant-numeric: tabular-nums;"
      >
        {{ intVal }}
      </text>
      <text
        :x="center"
        :y="center + size * 0.15"
        text-anchor="middle"
        :font-size="size * 0.09"
        fill="#789"
      >
        {{ unidadeLabel }}
      </text>

      <!-- (REMOVIDO) Label de graus -->

      <!-- Rosa dos ventos -->
      <g v-if="cfg.lettersMode !== 'hide'">
        <text
          :x="center"
          :y="size * 0.14"
          text-anchor="middle"
          :font-size="size * 0.08"
          :fill="lettersFill"
          :opacity="lettersOpacity"
        >{{ labels.N }}</text>

        <text
          :x="center"
          :y="size * 0.95"
          text-anchor="middle"
          :font-size="size * 0.08"
          :fill="lettersFill"
          :opacity="lettersOpacity"
        >{{ labels.S }}</text>

        <text
          :x="size * 0.11"
          :y="center + 4"
          text-anchor="middle"
          :font-size="size * 0.08"
          :fill="lettersFill"
          :opacity="lettersOpacity"
        >{{ labels.W }}</text>

        <text
          :x="size * 0.89"
          :y="center + 4"
          text-anchor="middle"
          :font-size="size * 0.08"
          :fill="lettersFill"
          :opacity="lettersOpacity"
        >{{ labels.E }}</text>
      </g>
    </svg>

    <!-- DIALOG DE CONFIGURAÇÃO (overlay largo e responsivo) -->
    <q-dialog v-model="showConfig" persistent transition-show="scale" transition-hide="scale">
      <q-card class="cfg-card">
        <q-card-section class="row items-center">
          <div class="text-h6 text-primary">Configurações do Relógio</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showConfig = false" />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Cor de status aplicada em</div>
              <q-option-group
                v-model="cfg.colorScope"
                type="radio"
                color="primary"
                :options="[
                  { label: 'Só na seta', value: 'arrow' },
                  { label: 'Seta e relógio', value: 'both' }
                ]"
              />
            </div>

            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Borda do relógio</div>
              <q-toggle v-model="cfg.showBorder" label="Mostrar borda fina" color="primary" />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Tipo de seta</div>
              <q-option-group
                v-model="cfg.arrowType"
                type="radio"
                color="primary"
                :options="[
                  { label: 'Teclado (⇧)', value: 'keyboard' },
                  { label: 'Clássica (↑)', value: 'classic' },
                  { label: 'Sólida (▲)', value: 'solid' }
                ]"
              />
            </div>

            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Tamanho da seta</div>
              <q-slider
                v-model="cfg.arrowSizePx"
                :min="12" :max="40" :step="1"
                color="primary"
                label
                label-always
              />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Idioma das letras</div>
              <q-option-group
                v-model="cfg.lang"
                type="radio"
                color="primary"
                :options="[
                  { label: 'Português (N L S O)', value: 'pt' },
                  { label: 'Inglês (N E S W)', value: 'en' }
                ]"
              />
            </div>

            <div class="col-12 col-sm-6">
              <div class="text-bold q-mb-xs">Letras da rosa dos ventos</div>
              <q-option-group
                v-model="cfg.lettersMode"
                type="radio"
                color="primary"
                :options="[
                  { label: 'Visíveis', value: 'show' },
                  { label: 'Mais opacas', value: 'dim' },
                  { label: 'Ocultas', value: 'hide' }
                ]"
              />
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <q-btn flat label="Fechar" color="primary" v-close-popup />
          <q-btn flat label="Restaurar padrão" color="warning" @click="resetToDefault" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 },          // direção (para onde vai)
  intensidade: { type: Number, default: 0 },    // kts
  max: { type: Number, default: 6 },
  size: { type: Number, default: 110 },

  // props de entrada (usadas como default ao abrir o componente)
  lang: { type: String, default: 'pt' },        // 'pt' | 'en'
  unidade: { type: String, default: 'kts' },
});

const center = computed(() => props.size / 2);
const radius = computed(() => props.size / 2 - 6);
const gradId = `gaugeGrad_${Math.floor(Math.random() * 1e8)}`;

const showConfig = ref(false);

// ---- CONFIG LOCAL (com persistência) ----
const cfg = ref({
  showBorder: false,
  colorScope: 'arrow',      // 'arrow' | 'both'
  arrowType: 'keyboard',    // 'keyboard' | 'classic' | 'solid'
  arrowSizePx: 20,
  lang: props.lang,         // 'pt' | 'en'
  lettersMode: 'dim',       // 'show' | 'dim' | 'hide'
});
const LS_KEY = 'currentGauge_config_v1';

onMounted(() => {
  const saved = localStorage.getItem(LS_KEY);
  if (saved) {
    try {
      Object.assign(cfg.value, JSON.parse(saved));
    } catch (e) { /* noop */ }
  }
});
watch(cfg, () => {
  localStorage.setItem(LS_KEY, JSON.stringify(cfg.value));
}, { deep: true });

function resetToDefault() {
  cfg.value = {
    showBorder: false,
    colorScope: 'arrow',
    arrowType: 'keyboard',
    arrowSizePx: 20,
    lang: 'pt',
    lettersMode: 'dim',
  };
}

// ---- Cores por intensidade (kts) ----
function colorByIntensity(kts) {
  if (!(typeof kts === 'number') || Number.isNaN(kts)) return '#1976d2';
  if (kts >= 4) return '#e53935';   // vermelho
  if (kts >= 2) return '#fbc02d';   // amarelo
  return '#43a047';                 // verde
}
const statusColor = computed(() => colorByIntensity(props.intensidade));

function hexToRgba(hex, alpha = 1) {
  const h = hex.replace('#', '');
  const bigint = parseInt(h, 16);
  // eslint-disable-next-line no-bitwise
  const r = (bigint >> 16) & 255;
  // eslint-disable-next-line no-bitwise
  const g = (bigint >> 8) & 255;
  // eslint-disable-next-line no-bitwise
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Relógio: preenchimento/borda reagem à opção "both"
const clockFill = computed(() => (
  cfg.value.colorScope === 'both'
    ? hexToRgba(statusColor.value, 0.14)
    : `url(#${gradId})`
));
const clockStroke = computed(() => (cfg.value.colorScope === 'both' ? statusColor.value : '#cfd8dc'));

// Seta
const arrowColor = computed(() => statusColor.value);
const pointerAngle = computed(() => {
  const v = Number.isFinite(props.value) ? props.value : 0;
  return ((v % 360) + 360) % 360;
});
const arrowGlyph = computed(() => {
  if (cfg.value.arrowType === 'solid') return '▲';
  if (cfg.value.arrowType === 'classic') return '↑';
  return '⇧'; // keyboard (padrão)
});

// Letras/idioma
const labels = computed(() => (
  cfg.value.lang === 'en'
    ? { N: 'N', S: 'S', E: 'E', W: 'W' }
    : { N: 'N', S: 'S', E: 'L', W: 'O' }
));
const lettersFill = computed(() => '#1976d2');
const lettersOpacity = computed(() => (cfg.value.lettersMode === 'dim' ? 0.45 : 1));

// Valor e unidade no miolo
const intVal = computed(() => (Number.isFinite(props.intensidade) ? props.intensidade.toFixed(2) : '--'));
const unidadeLabel = computed(() => (props.unidade || '').toString());
</script>

<style scoped>
.gauge-wrap {
  display: inline-block;
}
.gauge-svg {
  display: block;
}

/* Dialog largo e confortável */
.cfg-card {
  width: min(720px, 96vw);
  max-height: 90vh;
  overflow: auto;
  border-radius: 16px;
}
</style>
