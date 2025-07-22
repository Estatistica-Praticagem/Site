<template>
  <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" class="gauge-svg">
    <defs>
      <radialGradient :id="bgGradId" cx="50%" cy="50%" r="70%">
        <stop offset="0%" stop-color="#fff" />
        <stop offset="100%" stop-color="#e3edf7" />
      </radialGradient>
    </defs>
    <!-- Fundo -->
    <circle :cx="mid" :cy="mid" :r="radius" :fill="`url(#${bgGradId})`" stroke="#cfd8dc" stroke-width="3"/>
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
    <!-- Ponteiro -->
    <g :style="pointerStyle">
      <rect :x="mid-2" :y="pointerY" width="4" :height="pointerH" rx="2" fill="#1976d2" />
      <polygon :points="arrowPoints" fill="#0d47a1"/>
    </g>
    <!-- Miolo + Texto -->
    <circle :cx="mid" :cy="mid" :r="innerR" fill="#fff" stroke="#e3e3e3" stroke-width="2"/>
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
</template>

<script setup>
import { computed } from 'vue';

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

// ARCO SVG
const mid = computed(() => props.size / 2);
const radius = computed(() => props.size / 2 - 6);
const innerR = computed(() => props.size / 2.9);
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

// Ponteiro
const pointerH = computed(() => props.size * 0.27);
const pointerY = computed(() => props.size * 0.22);
const pointerAngle = computed(() => (props.value % 360) - 135);
const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: `${mid.value}px ${mid.value}px`,
}));
const arrowPoints = computed(() => `${mid.value},${pointerY.value - 6} ${mid.value + 5},${pointerY.value + 10} ${mid.value},${pointerY.value + 4} ${mid.value - 5},${pointerY.value + 10}`);

// eslint-disable-next-line no-restricted-globals
const grau = computed(() => (isNaN(props.value) ? '--' : props.value.toFixed(1)));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(1)));
</script>

<style scoped>
.gauge-svg {
  width: 110px;
  height: 110px;
  display: block;
}
</style>
