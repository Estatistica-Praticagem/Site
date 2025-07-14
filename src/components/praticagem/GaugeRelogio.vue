<template>
  <svg width="110" height="110" viewBox="0 0 110 110" class="gauge-svg">
    <defs>
      <linearGradient id="grad1" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#f8fafb"/>
        <stop offset="100%" stop-color="#e3edf7"/>
      </linearGradient>
    </defs>
    <circle cx="55" cy="55" r="49" fill="url(#grad1)" stroke="#cfd8dc" stroke-width="3"/>
    <g :transform="'rotate(-135 55 55)'">
      <path
        :d="arcPath"
        :stroke="corIntensidade(intensidade, max)"
        stroke-width="8"
        fill="none"
        stroke-linecap="round"
      />
    </g>
    <g :style="pointerStyle">
      <rect x="53" y="24" width="4" height="30" rx="2" fill="#1976d2" />
      <polygon points="55,18 60,31 55,28 50,31" fill="#0d47a1"/>
    </g>
    <circle cx="55" cy="55" r="20" fill="#fff" stroke="#e3e3e3" stroke-width="2"/>
    <text x="55" y="60" text-anchor="middle" font-size="17" fill="#333" font-weight="bold">
      {{ intVal }}
    </text>
    <text x="55" y="74" text-anchor="middle" font-size="10" fill="#789">kt</text>
    <text x="55" y="90" text-anchor="middle" font-size="11" fill="#888">{{ grau }}°</text>
    <text x="55" y="15" text-anchor="middle" font-size="9" fill="#1976d2">N</text>
    <text x="55" y="104" text-anchor="middle" font-size="9" fill="#1976d2">S</text>
    <text x="12" y="59" text-anchor="middle" font-size="9" fill="#1976d2">O</text>
    <text x="98" y="59" text-anchor="middle" font-size="9" fill="#1976d2">L</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 },
  intensidade: { type: Number, default: 0 },
  max: { type: Number, default: 3 },
});

function corIntensidade(val, max) {
  if (val < max * 0.33) return '#43a047';
  if (val < max * 0.66) return '#1976d2';
  if (val < max * 0.85) return '#f9a825';
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
const pointerAngle = computed(() => (props.value % 360) - 135);
// eslint-disable-next-line no-restricted-globals
const grau = computed(() => (isNaN(props.value) ? '--' : props.value.toFixed(1)));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(2)));
const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: '55px 55px',
}));
const arcPath = computed(() => describeArc(55, 55, 46, 0, arc.value));
</script>
