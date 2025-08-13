<template>
  <div>
    <svg width="36" height="36" viewBox="0 0 36 36">
      <!-- Círculo de fundo (opcional) — DESATIVADO
      <circle cx="18" cy="18" r="16" fill="#e3f2fd" stroke="#1976d2" stroke-width="2"/>
      <circle cx="18" cy="18" r="13" fill="#fff" opacity="0.9"/>
      -->

      <!-- Seta -->
      <text
        x="18"
        y="18"
        text-anchor="middle"
        dominant-baseline="middle"
        :font-size="arrowSize"
        :fill="arrowFill"
        :stroke="showBorder ? borderColor : 'none'"
        :stroke-width="showBorder ? 1 : 0"
        :transform="`rotate(${degOut} 18 18)`"
        style="cursor: pointer"
        @click="showConfig = !showConfig"
      >
        {{ selectedArrow }}
      </text>
    </svg>

    <!-- Card de Configuração -->
    <q-card v-if="showConfig" class="q-mt-sm q-pa-md bg-grey-1 shadow-2" style="max-width: 320px">
      <div class="text-subtitle2 q-mb-sm">Configuração da Seta</div>

      <!-- Modo de Cor -->
      <q-option-group
        v-model="colorMode"
        type="radio"
        :options="[
          { label: 'Automática (15/25 kts)', value: 'auto' },
          { label: 'Personalizada por faixa', value: 'custom' },
          { label: 'Manual (cor fixa)', value: 'manual' }
        ]"
        color="primary"
        class="q-mb-md"
      />

      <!-- Paleta custom por faixa -->
      <div v-if="colorMode === 'custom'" class="q-gutter-sm q-mb-md">
        <div class="text-caption text-weight-bold">Cores por faixa (knots)</div>
        <div class="row items-center q-gutter-sm">
          <q-input v-model="customLow"  type="color" label="0–14.9"  filled dense style="max-width: 140px;" />
          <q-input v-model="customMid"  type="color" label="15–24.9" filled dense style="max-width: 140px;" />
          <q-input v-model="customHigh" type="color" label="≥ 25"    filled dense style="max-width: 140px;" />
        </div>
      </div>

      <!-- Manual: color picker clássico -->
      <q-input
        v-if="colorMode === 'manual'"
        v-model="arrowColor"
        label="Cor fixa da seta"
        type="color"
        filled
        class="q-mb-md"
      />

      <q-slider
        v-model="arrowSize"
        label
        label-always
        :min="10"
        :max="30"
        step="1"
        color="primary"
        class="q-mb-md"
      >
        <template #prepend>Tamanho</template>
      </q-slider>

      <q-toggle v-model="showBorder" label="Mostrar borda" class="q-mb-md" />
      <q-input v-model="borderColor" label="Cor da borda" type="color" filled v-if="showBorder" />

      <q-option-group
        v-model="arrowStyle"
        type="radio"
        :options="arrowOptions"
        label="Tipo de seta"
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
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
  deg:   { type: Number, default: 0 },      // direção base (de onde vem)
  speed: { type: Number, default: null },   // velocidade (m/s por padrão)
  speedIsKnots: { type: Boolean, default: false }, // defina true se já vier em kts
});

// Normaliza o ângulo de entrada e inverte (para onde vai = +180°)
const degIn = computed(() => {
  const v = Number(props.deg);
  return Number.isFinite(v) ? ((v % 360) + 360) % 360 : 0;
});
const degOut = computed(() => (degIn.value + 180) % 360);

// ===== Conversão p/ knots =====
const msToKts = (v) => (typeof v === 'number' ? v * 1.94384 : null);
const speedKts = computed(() => (props.speedIsKnots ? props.speed : msToKts(props.speed)));

// ===== Config persistida =====
const configKey = 'globalWindArrowConfig_v3';

const showConfig   = ref(false);

// Visual (defaults: SEM BORDA e seta teclado)
const arrowSize   = ref(18);
const showBorder  = ref(false);
const borderColor = ref('#0d47a1');

// Modo de cor
const colorMode   = ref('auto'); // auto | custom | manual
const arrowColor  = ref('#1976d2'); // usado no modo 'manual'

// Paleta custom (faixas)
const customLow   = ref('#43a047'); // < 15 kts (verde)
const customMid   = ref('#fbc02d'); // 15–24.9 kts (amarelo)
const customHigh  = ref('#e53935'); // ≥ 25 kts (vermelho)

// Estilo da seta (default: teclado)
const arrowStyle = ref('arrow3');
const arrowOptions = [
  { label: 'Seta clássica ↑', value: 'arrow1' },
  { label: 'Seta sólida ▲',  value: 'arrow2' },
  { label: 'Seta teclado ⇧', value: 'arrow3' },
];
const selectedArrow = computed(() => {
  switch (arrowStyle.value) {
    case 'arrow2': return '▲';
    case 'arrow3': return '⇧';
    default: return '↑';
  }
});

// ===== Cor por força do vento =====
function colorByKts(kts) {
  const low  = customLow.value;
  const mid  = customMid.value;
  const high = customHigh.value;

  // fallback para manual se não houver velocidade
  // eslint-disable-next-line no-restricted-globals
  if (kts == null || isNaN(kts)) return arrowColor.value;
  if (kts >= 25) return high;
  if (kts >= 15) return mid;
  return low;
}

const arrowFill = computed(() => {
  if (colorMode.value === 'manual') return arrowColor.value;
  // auto e custom usam thresholds; em 'custom' você altera as cores
  return colorByKts(speedKts.value);
});

// ===== Persistência =====
onMounted(() => {
  const saved = localStorage.getItem(configKey);
  if (saved) {
    try {
      const cfg = JSON.parse(saved);
      arrowSize.value   = cfg.arrowSize   ?? arrowSize.value;
      showBorder.value  = cfg.showBorder  ?? showBorder.value;
      borderColor.value = cfg.borderColor ?? borderColor.value;

      colorMode.value   = cfg.colorMode   ?? colorMode.value;
      arrowColor.value  = cfg.arrowColor  ?? arrowColor.value;

      customLow.value   = cfg.customLow   ?? customLow.value;
      customMid.value   = cfg.customMid   ?? customMid.value;
      customHigh.value  = cfg.customHigh  ?? customHigh.value;

      arrowStyle.value  = cfg.arrowStyle  ?? arrowStyle.value;
    } catch (e) {
      console.warn('Erro ao carregar config global:', e);
    }
  }
});

watch(
  [arrowSize, showBorder, borderColor, colorMode, arrowColor, customLow, customMid, customHigh, arrowStyle],
  () => {
    const cfg = {
      arrowSize: arrowSize.value,
      showBorder: showBorder.value,
      borderColor: borderColor.value,
      colorMode: colorMode.value,
      arrowColor: arrowColor.value,
      customLow: customLow.value,
      customMid: customMid.value,
      customHigh: customHigh.value,
      arrowStyle: arrowStyle.value,
    };
    localStorage.setItem(configKey, JSON.stringify(cfg));
  },
  { deep: true }
);

// Reset
function resetToDefault() {
  arrowSize.value = 18;
  showBorder.value = false;
  borderColor.value = '#0d47a1';

  colorMode.value = 'auto';
  arrowColor.value = '#1976d2';

  customLow.value  = '#43a047';
  customMid.value  = '#fbc02d';
  customHigh.value = '#e53935';

  arrowStyle.value = 'arrow3'; // seta teclado
}
</script>
