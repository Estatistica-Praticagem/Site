<template>
  <div>
    <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" class="gauge-svg" @click="showConfig = !showConfig">
      <!-- Círculo principal -->
      <circle
        :cx="center"
        :cy="center"
        :r="center-6"
        :fill="circleFill"
        :stroke="showClockBorder ? clockBorderColor : 'none'"
        :stroke-width="showClockBorder ? 1.5 : 0"
      />

      <!-- Seta (3 estilos), cor pela intensidade -->
      <text
        :x="center"
        :y="center - size * 0.32"
        text-anchor="middle"
        dominant-baseline="middle"
        :font-size="arrowPx"
        :fill="intensityColor"
        :transform="`rotate(${pointerAngle} ${center} ${center})`"
        style="cursor: pointer; user-select: none;"
      >
        {{ arrowGlyph }}
      </text>

      <!-- Miolo + Leitura -->
      <circle :cx="center" :cy="center" :r="size*0.18" fill="#fff" stroke="#e3e3e3" stroke-width="2"/>
      <text :x="center" :y="center+5" text-anchor="middle" :font-size="size*0.15" fill="#333" font-weight="bold">
        {{ intVal }}
      </text>
      <text :x="center" :y="center+size*0.17" text-anchor="middle" :font-size="size*0.09" fill="#789">
        kts
      </text>
      <text :x="center" :y="center+size*0.32" text-anchor="middle" :font-size="size*0.10" fill="#888">
        {{ grau }}°
      </text>

      <!-- Rosa dos ventos (mostra/menos destaque/oculta) -->
      <template v-if="lettersMode !== 'hide'">
        <text :x="center" :y="size*0.14" text-anchor="middle" :font-size="size*0.08" :fill="lettersFill" :opacity="lettersOpacity">{{ labels.N }}</text>
        <text :x="center" :y="size*0.95" text-anchor="middle" :font-size="size*0.08" :fill="lettersFill" :opacity="lettersOpacity">{{ labels.S }}</text>
        <text :x="size*0.11" :y="center+4" text-anchor="middle" :font-size="size*0.08" :fill="lettersFill" :opacity="lettersOpacity">{{ labels.W }}</text>
        <text :x="size*0.89" :y="center+4" text-anchor="middle" :font-size="size*0.08" :fill="lettersFill" :opacity="lettersOpacity">{{ labels.E }}</text>
      </template>
    </svg>

    <!-- Painel de Config do relógio -->
    <q-card v-if="showConfig" class="q-mt-sm q-pa-md bg-grey-1 shadow-2" style="max-width: 320px">
      <div class="text-subtitle2 q-mb-sm">Configurações do Relógio</div>

      <!-- Para reduzir cor: escolher alvo da cor -->
      <q-option-group
        v-model="colorTarget"
        type="radio"
        :options="[
          { label: 'Cor só na seta', value: 'arrow' },
          { label: 'Cor na seta e no relógio', value: 'both' }
        ]"
        color="primary"
        class="q-mb-md"
      />

      <!-- Borda do relógio (fina por padrão) -->
      <q-toggle
        v-model="showClockBorder"
        label="Mostrar borda do relógio"
        color="primary"
        class="q-mb-md"
      />

      <!-- Seta -->
      <q-option-group
        v-model="arrowStyle"
        type="radio"
        :options="[
          { label: 'Seta clássica ↑', value: 'classic' },
          { label: 'Seta sólida ▲', value: 'solid' },
          { label: 'Seta teclado ⇧', value: 'kbd' }
        ]"
        color="primary"
        class="q-mb-md"
      />

      <q-slider
        v-model="arrowScale"
        label
        label-always
        :min="0.7"
        :max="2.0"
        :step="0.05"
        color="primary"
        class="q-mb-md"
      >
        <template #prepend>Tamanho da seta</template>
      </q-slider>

      <!-- Letras -->
      <q-option-group
        v-model="lettersMode"
        type="radio"
        :options="[
          { label: 'Mostrar letras', value: 'show' },
          { label: 'Letras mais opacas', value: 'dim' },
          { label: 'Ocultar letras', value: 'hide' }
        ]"
        color="primary"
        class="q-mb-md"
      />

      <div class="row q-gutter-sm q-mt-md">
        <q-btn label="Fechar" color="primary" @click="showConfig = false" flat />
        <q-btn label="Restaurar padrão" color="warning" flat @click="resetToDefault" />
      </div>
    </q-card>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 },         // direção da corrente (graus)
  intensidade: { type: Number, default: 0 },   // kts
  max: { type: Number, default: 6 },
  size: { type: Number, default: 110 },
  lang: { type: String, default: 'pt' },       // 'pt' ou 'en' — vem do PAI
});

const center = computed(() => props.size / 2);

// ===== Cores por intensidade =====
function intensityHex(val) {
  if (val <= 2) return '#43a047';   // verde
  if (val <= 4) return '#fbc02d';   // amarelo
  return '#e53935';                 // vermelho
}
function hexToRgba(hex, alpha = 0.16) {
  const h = hex.replace('#', '');
  const n = parseInt(h, 16);
  // eslint-disable-next-line no-bitwise
  const r = (n >> 16) & 255;
  // eslint-disable-next-line no-bitwise
  const g = (n >> 8) & 255;
  // eslint-disable-next-line no-bitwise
  const b = n & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
const intensityColor = computed(() => intensityHex(props.intensidade || 0));

// Controle de onde aplicar a cor (reduz “muita cor” por padrão)
const colorTarget = ref('arrow'); // 'arrow' | 'both'
const circleFill = computed(() => (colorTarget.value === 'both' ? hexToRgba(intensityColor.value, 0.16) : '#ffffff'));

// Borda do relógio (fina por padrão)
const showClockBorder = ref(true);
const clockBorderColor = '#cfd8dc';

// ===== Direção / leitura =====
const pointerAngle = computed(() => {
  const v = Number(props.value) || 0;
  return ((v % 360) + 360) % 360;
});
const grau = computed(() => (Number.isFinite(props.value) ? Number(props.value).toFixed(1) : '--'));
const intVal = computed(() => (Number.isFinite(props.intensidade) ? Number(props.intensidade).toFixed(2) : '--'));

// ===== Seta (3 estilos + escala) =====
const storageKey = 'currentGaugePrefs_v2';
const showConfig = ref(false);
const arrowStyle = ref('kbd');    // 'classic' | 'solid' | 'kbd'
const arrowScale = ref(1.0);
const arrowGlyph = computed(() => (arrowStyle.value === 'solid' ? '▲' : arrowStyle.value === 'kbd' ? '⇧' : '↑'));
const arrowPx = computed(() => Math.round(props.size * 0.26 * arrowScale.value));

// ===== Letras da rosa dos ventos (idioma vindo do pai) =====
const lettersMode = ref('show'); // 'show' | 'dim' | 'hide'
const lettersFill = '#1976d2';
const lettersOpacity = computed(() => (lettersMode.value === 'dim' ? 0.55 : 1.0));
const labels = computed(() => {
  if ((props.lang || 'pt').toLowerCase() === 'en') {
    return { N: 'N', E: 'E', S: 'S', W: 'W' };
  }
  // pt
  return { N: 'N', E: 'L', S: 'S', W: 'O' };
});

// ===== Persistência =====
onMounted(() => {
  const saved = localStorage.getItem(storageKey);
  if (saved) {
    try {
      const cfg = JSON.parse(saved);
      colorTarget.value   = cfg.colorTarget   ?? colorTarget.value;
      showClockBorder.value = cfg.showClockBorder ?? showClockBorder.value;
      arrowStyle.value    = cfg.arrowStyle    ?? arrowStyle.value;
      arrowScale.value    = cfg.arrowScale    ?? arrowScale.value;
      lettersMode.value   = cfg.lettersMode   ?? lettersMode.value;
    } catch (e) {
      console.warn('Erro ao carregar prefs do relógio:', e);
    }
  }
});
watch([colorTarget, showClockBorder, arrowStyle, arrowScale, lettersMode], () => {
  const cfg = {
    colorTarget: colorTarget.value,
    showClockBorder: showClockBorder.value,
    arrowStyle: arrowStyle.value,
    arrowScale: arrowScale.value,
    lettersMode: lettersMode.value,
  };
  localStorage.setItem(storageKey, JSON.stringify(cfg));
}, { deep: true });

// Reset
function resetToDefault() {
  colorTarget.value = 'arrow';
  showClockBorder.value = true;     // borda fina ligada
  arrowStyle.value = 'kbd';
  arrowScale.value = 1.0;
  lettersMode.value = 'show';
}
</script>

<style scoped>
.gauge-svg { display: block; }
</style>
