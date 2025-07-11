<template>
  <svg width="160" height="160" viewBox="0 0 160 160" class="rose-svg">
    <defs>
      <radialGradient id="grad2" cx="50%" cy="50%" r="60%">
        <stop offset="0%" stop-color="#eaf2fd" />
        <stop offset="100%" stop-color="#90caf9" />
      </radialGradient>
    </defs>
    <circle cx="80" cy="80" r="78" fill="url(#grad2)" stroke="#b3e5fc" stroke-width="3" />
    <g>
      <line x1="80" y1="20" x2="80" y2="140" stroke="#bbb" stroke-width="3" />
      <line x1="20" y1="80" x2="140" y2="80" stroke="#bbb" stroke-width="3" />
    </g>
    <!-- Ponteiro direção -->
    <g :style="pointerStyle">
      <polygon
        :points="polygonPoints"
        :fill="pointerColor"
      />
      <line
        x1="80"
        y1="80"
        :x2="80"
        :y2="80 - len"
        :stroke="pointerColor"
        stroke-width="5"
        stroke-linecap="round"
      />
    </g>
    <!-- Centro -->
    <circle cx="80" cy="80" r="27" fill="#fff" stroke="#90caf9" stroke-width="2" />
    <text x="80" y="92" text-anchor="middle" font-size="24" fill="#1565c0" font-weight="bold">{{ intVal }}</text>
    <text x="80" y="110" text-anchor="middle" font-size="11" fill="#666">m/s</text>
    <text x="80" y="42" text-anchor="middle" font-size="12" fill="#1976d2">N</text>
    <text x="80" y="156" text-anchor="middle" font-size="12" fill="#1976d2">S</text>
    <text x="24" y="86" text-anchor="middle" font-size="12" fill="#1976d2">O</text>
    <text x="146" y="86" text-anchor="middle" font-size="12" fill="#1976d2">L</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  direction: { type: Number, default: 0 },
  intensidade: { type: Number, default: 0 },
  max: { type: Number, default: 10 },
});

function color(val, max) {
  if (val < max * 0.33) return '#43a047';
  if (val < max * 0.66) return '#1976d2';
  if (val < max * 0.85) return '#f9a825';
  return '#e53935';
}

const pointerAngle = computed(() => (props.direction % 360) - 90);
const len = computed(() => 40 + 30 * Math.min(props.intensidade / props.max, 1));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(2)));
const pointerColor = computed(() => color(props.intensidade, props.max));

const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: '80px 80px',
}));

const polygonPoints = computed(() => {
  const l = len.value;
  return `80,80 80,${80 - l} 86,${80 - l + 16} 74,${80 - l + 16}`;
});
</script>
