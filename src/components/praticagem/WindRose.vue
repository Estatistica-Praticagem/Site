<template>
  <svg
    width="160"
    height="160"
    viewBox="0 0 160 160"
    class="rose-svg"
    :style="{ background: 'transparent' }"
  >
    <!-- Gradiente circular -->
    <defs>
      <radialGradient id="grad2" cx="50%" cy="50%" r="60%">
        <stop offset="0%" stop-color="#fafdff" />
        <stop offset="98%" stop-color="#90caf9" />
      </radialGradient>
      <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
        <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#90caf9" flood-opacity="0.11" />
      </filter>
    </defs>

    <!-- Círculo externo -->
    <circle
      cx="80" cy="80" r="77"
      fill="url(#grad2)"
      stroke="#b3e5fc"
      stroke-width="3"
      filter="url(#shadow)"
    />

    <!-- Linhas Norte-Sul, Leste-Oeste -->
    <g>
      <line x1="80" y1="17" x2="80" y2="143" stroke="#bbccdd" stroke-width="2.5" />
      <line x1="17" y1="80" x2="143" y2="80" stroke="#bbccdd" stroke-width="2.5" />
      <!-- Diagonais para sofisticação -->
      <line x1="33" y1="33" x2="127" y2="127" stroke="#e1e8ee" stroke-width="1.3" />
      <line x1="33" y1="127" x2="127" y2="33" stroke="#e1e8ee" stroke-width="1.3" />
    </g>

    <!-- Letra das direções -->
    <text x="80" y="36" text-anchor="middle" font-size="15" fill="#1976d2" font-weight="bold">N</text>
    <text x="80" y="153" text-anchor="middle" font-size="15" fill="#1976d2" font-weight="bold">S</text>
    <text x="20" y="87" text-anchor="middle" font-size="15" fill="#1976d2" font-weight="bold">O</text>
    <text x="140" y="87" text-anchor="middle" font-size="15" fill="#1976d2" font-weight="bold">L</text>
    <text x="42" y="48" text-anchor="middle" font-size="11" fill="#64b5f6">NO</text>
    <text x="120" y="48" text-anchor="middle" font-size="11" fill="#64b5f6">NL</text>
    <text x="42" y="133" text-anchor="middle" font-size="11" fill="#64b5f6">SO</text>
    <text x="120" y="133" text-anchor="middle" font-size="11" fill="#64b5f6">SL</text>

    <!-- Ponteiro (grupo animado) -->
    <g :style="pointerStyle">
      <!-- Sombra do ponteiro -->
      <polygon
        :points="polygonPoints"
        fill="#90caf9"
        opacity="0.28"
        filter="url(#shadow)"
      />
      <!-- Ponteiro principal -->
      <polygon
        :points="polygonPoints"
        :fill="pointerColor"
        stroke="#1976d2"
        stroke-width="2"
        style="transition: fill .3s;"
      />
      <line
        x1="80"
        y1="80"
        :x2="80"
        :y2="80 - len"
        :stroke="pointerColor"
        stroke-width="6"
        stroke-linecap="round"
        opacity="0.6"
      />
    </g>

    <!-- Centro -->
    <circle cx="80" cy="80" r="27" fill="#fff" stroke="#b3e5fc" stroke-width="2" />
    <text
      x="80"
      y="92"
      text-anchor="middle"
      font-size="27"
      fill="#1565c0"
      font-weight="bold"
      style="font-variant-numeric: tabular-nums;"
    >{{ intVal }}</text>
    <text x="80" y="111" text-anchor="middle" font-size="12" fill="#666">kts</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

// Props
const props = defineProps({
  direction: { type: Number, default: 0 }, // Graus meteorológicos (ventonum)
  intensidade: { type: Number, default: 0 }, // Valor (kts)
  max: { type: Number, default: 20 }, // Para cor/escala do ponteiro
});

// Cor do ponteiro conforme intensidade
function color(val) {
  if (val <= 15) return '#43a047'; // Verde
  if (val <= 35) return '#fbc02d'; // Amarelo
  return '#e53935'; // Vermelho
}

// Correção do ângulo: meteorológico 0° = Norte (cima do SVG)
const pointerAngle = computed(() => (90 - (props.direction || 0)) % 360);

// Se inverter, use +90. Mas o padrão meteorológico SVG geralmente é -90!

const len = computed(() => 42 + 29 * Math.min((props.intensidade || 0) / props.max, 1));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(2)));
const pointerColor = computed(() => color(props.intensidade, props.max));

// Ponteiro animado (rotate)
const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: '80px 80px',
  transition: 'transform .28s cubic-bezier(.65,.05,.36,1), filter .25s',
}));

const polygonPoints = computed(() => {
  // Triângulo com base no centro
  const l = len.value;
  // Formato flecha: ponta + lados + base arredondada
  return `
    80,${80 - l}
    84,${80 - l + 19}
    80,${80 - l + 12}
    76,${80 - l + 19}
  `;
});
</script>
