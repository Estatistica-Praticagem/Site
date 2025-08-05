<template>
  <div>
    <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" class="gauge-svg">
      <defs>
        <radialGradient :id="bgGradId" cx="50%" cy="50%" r="70%">
          <stop offset="0%" stop-color="#fff" />
          <stop offset="100%" stop-color="#e3edf7" />
        </radialGradient>
      </defs>

      <!-- Fundo -->
      <circle :cx="mid" :cy="mid" :r="radius" :fill="`url(#${bgGradId})`" stroke="#cfd8dc" stroke-width="3" />

      <!-- Barrinha colorida -->
      <g :transform="`rotate(-135 ${mid} ${mid})`">
        <path
          :d="arcPath"
          :stroke="windColor(intensidade)"
          stroke-width="9"
          fill="none"
          stroke-linecap="round"
        />
      </g>

      <!-- Seta Direcional -->
      <text
        :x="mid"
        :y="mid - radius / 1.4"
        text-anchor="middle"
        :font-size="arrowSize"
        :fill="arrowColor"
        :stroke="showBorder ? borderColor : 'none'"
        :stroke-width="showBorder ? 1.5 : 0"
        :transform="`rotate(${props.value} ${mid} ${mid})`"
        style="cursor: pointer"
        @click="showConfig = !showConfig"
      >
        ↑
      </text>

      <!-- Miolo + Texto -->
      <circle :cx="mid" :cy="mid" :r="innerR" fill="#fff" stroke="#e3e3e3" stroke-width="2" />
      <text :x="mid" :y="mid+6" text-anchor="middle" font-size="17" fill="#333" font-weight="bold">
        {{ intVal }}
      </text>
      <text :x="mid" :y="mid+20" text-anchor="middle" font-size="10" fill="#789">kts</text>
      <text :x="mid" :y="mid+35" text-anchor="middle" font-size="12" fill="#888">{{ grau }}°</text>

      <!-- Rosa dos Ventos -->
      <text :x="mid" :y="18" text-anchor="middle" font-size="9" fill="#1976d2">N</text>
      <text :x="mid" :y="size-6" text-anchor="middle" font-size="9" fill="#1976d2">S</text>
      <text :x="18" :y="mid+3" text-anchor="middle" font-size="9" fill="#1976d2">O</text>
      <text :x="size-18" :y="mid+3" text-anchor="middle" font-size="9" fill="#1976d2">L</text>
    </svg>

    <!-- Painel de Configuração -->
    <q-card v-if="showConfig" class="q-mt-sm q-pa-md bg-grey-1 shadow-2">
      <div class="text-subtitle2 q-mb-sm">Configurações da Seta</div>

      <q-slider
        v-model="arrowSize"
        label
        label-always
        :min="10"
        :max="40"
        step="1"
        color="primary"
        class="q-mb-md"
      >
        <template #prepend> Tamanho </template>
      </q-slider>

      <q-toggle v-model="showBorder" label="Mostrar borda" class="q-mb-md" />

      <q-input v-model="arrowColor" label="Cor da seta" type="color" filled class="q-mb-md" />
      <q-input v-model="borderColor" label="Cor da borda" type="color" filled v-if="showBorder" />
      <q-btn label="Restaurar padrão" color="warning" flat @click="resetToDefault" />

      <q-btn label="Fechar" color="primary" @click="showConfig = false" flat />
    </q-card>
  </div>
</template>

<script setup>
import {
  ref, computed, watch, onMounted,
} from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 }, // direção vento
  intensidade: { type: Number, default: 0 }, // intensidade em kts
  max: { type: Number, default: 60 },
  size: { type: Number, default: 110 },
});

const bgGradId = `bgWindGrad${Math.floor(Math.random() * 9999)}`;

// CORES DE VENTO
function windColor(kts) {
  if (kts <= 15) return '#43a047';
  if (kts <= 35) return '#fbc02d';
  return '#e53935';
}

function resetToDefault() {
  // eslint-disable-next-line no-use-before-define
  arrowSize.value = 22;
  // eslint-disable-next-line no-use-before-define
  showBorder.value = true;
  // eslint-disable-next-line no-use-before-define
  arrowColor.value = '#0d47a1';
  // eslint-disable-next-line no-use-before-define
  borderColor.value = '#1976d2';
}

// MEDIDAS
const mid = computed(() => props.size / 2);
const radius = computed(() => props.size / 2 - 6);
const innerR = computed(() => props.size / 2.9);

// ARCO
function polarToCartesian(cx, cy, r, angleInDegrees) {
  // eslint-disable-next-line no-mixed-operators
  const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;
  return { x: cx + (r * Math.cos(angleInRadians)), y: cy + (r * Math.sin(angleInRadians)) };
}
function describeArc(cx, cy, r, startAngle, endAngle) {
  const start = polarToCartesian(cx, cy, r, endAngle);
  const end = polarToCartesian(cx, cy, r, startAngle);
  const arcSweep = endAngle - startAngle <= 180 ? '0' : '1';
  return ['M', start.x, start.y, 'A', r, r, 0, arcSweep, 0, end.x, end.y].join(' ');
}
const arc = computed(() => (Math.min(props.intensidade, props.max) / props.max) * 270);
const arcPath = computed(() => describeArc(mid.value, mid.value, radius.value, 0, arc.value));

// TEXTO
// eslint-disable-next-line no-restricted-globals
const grau = computed(() => (isNaN(props.value) ? '--' : props.value.toFixed(1)));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(1)));

// CONFIGURAÇÕES DE SETA COM LOCALSTORAGE
const configKey = 'windArrowConfig';
const showConfig = ref(false);
const arrowSize = ref(22);
const showBorder = ref(true);
const arrowColor = ref('#0d47a1');
const borderColor = ref('#1976d2');

// CARREGAR CONFIGURACAO SALVA
onMounted(() => {
  const saved = localStorage.getItem(configKey);
  if (saved) {
    try {
      const cfg = JSON.parse(saved);
      arrowSize.value = cfg.arrowSize ?? arrowSize.value;
      showBorder.value = cfg.showBorder ?? showBorder.value;
      arrowColor.value = cfg.arrowColor ?? arrowColor.value;
      borderColor.value = cfg.borderColor ?? borderColor.value;
    } catch (e) {
      console.warn('Erro ao carregar configuração da seta:', e);
    }
  }
});

// SALVAR SEMPRE QUE MUDAR
watch([arrowSize, showBorder, arrowColor, borderColor], () => {
  const config = {
    arrowSize: arrowSize.value,
    showBorder: showBorder.value,
    arrowColor: arrowColor.value,
    borderColor: borderColor.value,
  };
  localStorage.setItem(configKey, JSON.stringify(config));
});
</script>

<style scoped>
.gauge-svg {
  width: 110px;
  height: 110px;
  display: block;
}
</style>
