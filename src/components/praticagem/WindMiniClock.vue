<template>
  <svg width="36" height="36" viewBox="0 0 36 36">
    <circle cx="18" cy="18" r="16" fill="#e3f2fd" stroke="#1976d2" stroke-width="2"/>
    <g>
      <!-- Fundo/Background -->
      <circle cx="18" cy="18" r="13" fill="#fff" opacity="0.9"/>
      <!-- Ponteiro -->
      <line
        :x1="18" :y1="18"
        :x2="18 + 12 * Math.sin(rad)"
        :y2="18 - 12 * Math.cos(rad)"
        stroke="#1976d2" stroke-width="3" stroke-linecap="round"
      />
      <!-- Setinha na ponta -->
      <polygon
        :points="arrowPoints"
        :fill="pointerColor"
        opacity="0.9"
      />
    </g>
    <!-- Texto intensidade -->
    <text x="18" y="33" font-size="9" text-anchor="middle" fill="#1976d2" font-weight="bold">
      {{ speed ? speed + ' km/h' : '' }}
    </text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  deg: { type: Number, default: 0 }, // direção do vento
  speed: { type: Number, default: null }, // intensidade km/h
});

// Converte graus para radianos, ajusta para "Norte" em cima
// eslint-disable-next-line no-mixed-operators
const rad = computed(() => (props.deg - 0) * Math.PI / 180);

// Calcula os pontos da seta
const arrowPoints = computed(() => {
  const x0 = 18 + 12 * Math.sin(rad.value);
  const y0 = 18 - 12 * Math.cos(rad.value);
  const x1 = x0 + 4 * Math.sin(rad.value + Math.PI * 0.6);
  const y1 = y0 - 4 * Math.cos(rad.value + Math.PI * 0.6);
  const x2 = x0 + 4 * Math.sin(rad.value - Math.PI * 0.6);
  const y2 = y0 - 4 * Math.cos(rad.value - Math.PI * 0.6);
  return `${x0},${y0} ${x1},${y1} ${x2},${y2}`;
});

const pointerColor = computed(() => (props.speed >= 20 ? '#f44336' : '#1976d2'));
</script>
