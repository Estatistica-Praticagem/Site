<template>
  <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" class="gauge-svg">
    ...
    <circle :cx="center" :cy="center" :r="center-6" fill="url(#grad1)" stroke="#cfd8dc" stroke-width="3"/>
    ...
    <g :transform="'rotate(-135 ' + center + ' ' + center + ')'">
      <path
        :d="arcPath"
        :stroke="corIntensidade(intensidade)"
        stroke-width="8"
        fill="none"
        stroke-linecap="round"
      />
    </g>
    <g :style="pointerStyle">
      <rect :x="center-2" :y="center-size*0.28" width="4" :height="size*0.27" rx="2" fill="#1976d2" />
      <polygon :points="pointerPolygon" fill="#0d47a1"/>
    </g>
    <circle :cx="center" :cy="center" :r="size*0.18" fill="#fff" stroke="#e3e3e3" stroke-width="2"/>
    <text :x="center" :y="center+5" text-anchor="middle" :font-size="size*0.15" fill="#333" font-weight="bold">
      {{ intVal }}
    </text>
    <text :x="center" :y="center+size*0.17" text-anchor="middle" :font-size="size*0.09" fill="#789">kts</text>
    <text :x="center" :y="center+size*0.32" text-anchor="middle" :font-size="size*0.1" fill="#888">{{ grau }}°</text>
    <text :x="center" :y="size*0.14" text-anchor="middle" :font-size="size*0.08" fill="#1976d2">N</text>
    <text :x="center" :y="size*0.95" text-anchor="middle" :font-size="size*0.08" fill="#1976d2">S</text>
    <text :x="size*0.11" :y="center+4" text-anchor="middle" :font-size="size*0.08" fill="#1976d2">O</text>
    <text :x="size*0.89" :y="center+4" text-anchor="middle" :font-size="size*0.08" fill="#1976d2">L</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 },
  intensidade: { type: Number, default: 0 },
  max: { type: Number, default: 6 },
  size: { type: Number, default: 110 }, // <-- adicionado!
});

const center = computed(() => props.size / 2);

function corIntensidade(val) {
  if (val <= 2) return '#43a047';
  if (val <= 4) return '#fbc02d';
  return '#e53935';
}
function polarToCartesian(cx, cy, r, angleInDegrees) {
  // eslint-disable-next-line no-mixed-operators
  const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;
  return {
    x: cx + (r * Math.cos(angleInRadians)),
    y: cy + (r * Math.sin(angleInRadians)),
  };
}
function describeArc(cx, cy, r, startAngle, endAngle) {
  const start = polarToCartesian(cx, cy, r, endAngle);
  const end = polarToCartesian(cx, cy, r, startAngle);
  const arcSweep = endAngle - startAngle <= 180 ? '0' : '1';
  return [
    'M', start.x, start.y,
    'A', r, r, 0, arcSweep, 0, end.x, end.y,
  ].join(' ');
}
const arc = computed(() => (Math.min(props.intensidade, props.max) / props.max) * 270);
const pointerAngle = computed(() => props.value % 360);

// eslint-disable-next-line no-restricted-globals
const grau = computed(() => (isNaN(props.value) ? '--' : props.value.toFixed(1)));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(2)));
const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: `${center.value}px ${center.value}px`,
}));
const arcPath = computed(() => describeArc(center.value, center.value, center.value - 9, 0, arc.value));
const pointerPolygon = computed(() => {
  // Triângulo para o ponteiro (ajustado para size)
  // ponta, lado dir, base, lado esq
  const c = center.value;
  const tip = [c, c - props.size * 0.34];
  const right = [c + props.size * 0.045, c - props.size * 0.21];
  const base = [c, c - props.size * 0.24];
  const left = [c - props.size * 0.045, c - props.size * 0.21];
  return `${tip} ${right} ${base} ${left}`;
});
</script>
