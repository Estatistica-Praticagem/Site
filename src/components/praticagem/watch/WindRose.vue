<template>
  <div style="display:flex;align-items:center;">
    <svg
      :width="size"
      :height="size"
      :viewBox="`0 0 ${size} ${size}`"
      class="rose-svg"
      :style="{ background: 'transparent', display:'block' }"
    >
      <defs>
        <radialGradient id="grad2" cx="50%" cy="50%" r="60%">
          <stop offset="0%" stop-color="#fafdff" />
          <stop offset="98%" stop-color="#90caf9" />
        </radialGradient>
        <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#90caf9" flood-opacity="0.11" />
        </filter>
      </defs>

      <!-- ARCO DE INTENSIDADE (fora do círculo principal) -->
      <g :transform="`rotate(-135 ${center} ${center})`">
        <path
          :d="arcPath"
          :stroke="pointerColor"
          :stroke-width="size * 0.08"
          fill="none"
          stroke-linecap="round"
          style="filter: drop-shadow(0 1px 4px #eee8);"
        />
      </g>

      <!-- Círculo externo -->
      <circle
        :cx="center"
        :cy="center"
        :r="center-3"
        fill="url(#grad2)"
        stroke="#b3e5fc"
        stroke-width="3"
        filter="url(#shadow)"
      />

      <!-- Linhas e diagonais -->
      <g>
        <line :x1="center" :y1="center-((center-3)*0.82)" :x2="center" :y2="center+((center-3)*0.82)" stroke="#bbccdd" stroke-width="2.5"/>
        <line :x1="center-((center-3)*0.82)" :y1="center" :x2="center+((center-3)*0.82)" :y2="center" stroke="#bbccdd" stroke-width="2.5"/>
        <line :x1="center-(center*0.59)" :y1="center-(center*0.59)" :x2="center+(center*0.59)" :y2="center+(center*0.59)" stroke="#e1e8ee" stroke-width="1.3"/>
        <line :x1="center-(center*0.59)" :y1="center+(center*0.59)" :x2="center+(center*0.59)" :y2="center-(center*0.59)" stroke="#e1e8ee" stroke-width="1.3"/>
      </g>

      <!-- Direções -->
      <text :x="center" :y="center-(center*0.73)" text-anchor="middle" :font-size="size*0.094" fill="#1976d2" font-weight="bold">{{ dirs.N }}</text>
      <text :x="center" :y="center+(center*0.87)" text-anchor="middle" :font-size="size*0.094" fill="#1976d2" font-weight="bold">{{ dirs.S }}</text>
      <text :x="center-(center*0.75)" :y="center+7" text-anchor="middle" :font-size="size*0.094" fill="#1976d2" font-weight="bold">{{ dirs.W }}</text>
      <text :x="center+(center*0.75)" :y="center+7" text-anchor="middle" :font-size="size*0.094" fill="#1976d2" font-weight="bold">{{ dirs.E }}</text>
      <text :x="center-(center*0.52)" :y="center-(center*0.55)" text-anchor="middle" :font-size="size*0.07" fill="#64b5f6">{{ dirs.NW }}</text>
      <text :x="center+(center*0.52)" :y="center-(center*0.55)" text-anchor="middle" :font-size="size*0.07" fill="#64b5f6">{{ dirs.NE }}</text>
      <text :x="center-(center*0.52)" :y="center+(center*0.63)" text-anchor="middle" :font-size="size*0.07" fill="#64b5f6">{{ dirs.SW }}</text>
      <text :x="center+(center*0.52)" :y="center+(center*0.63)" text-anchor="middle" :font-size="size*0.07" fill="#64b5f6">{{ dirs.SE }}</text>

      <!-- Ponteiro -->
      <g :style="pointerStyle">
        <polygon
          :points="polygonPoints"
          fill="#90caf9"
          opacity="0.28"
          filter="url(#shadow)"
        />
        <polygon
          :points="polygonPoints"
          :fill="pointerColor"
          stroke="#1976d2"
          stroke-width="2"
          style="transition: fill .3s;"
        />
        <line
          :x1="center"
          :y1="center"
          :x2="center"
          :y2="center-len"
          :stroke="pointerColor"
          stroke-width="6"
          stroke-linecap="round"
          opacity="0.6"
        />
      </g>

      <!-- Centro / Valor -->
      <circle :cx="center" :cy="center" :r="size*0.17" fill="#fff" stroke="#b3e5fc" stroke-width="2" />
      <text
        :x="center"
        :y="center+(size*0.075)"
        text-anchor="middle"
        :font-size="size*0.17"
        fill="#1565c0"
        font-weight="bold"
        style="font-variant-numeric: tabular-nums;"
      >{{ intVal }}</text>
      <text :x="center" :y="center+(size*0.22)" text-anchor="middle" :font-size="size*0.075" fill="#666">kts</text>
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  direction: { type: Number, default: 0 },
  intensidade: { type: Number, default: 0 },
  max: { type: Number, default: 20 },
  lang: { type: String, default: 'pt' },
  size: { type: Number, default: 110 },
});

const dirMap = {
  pt: { N: 'N', S: 'S', E: 'L', W: 'O', NE: 'NL', NW: 'NO', SE: 'SL', SW: 'SO' },
  en: { N: 'N', S: 'S', E: 'E', W: 'W', NE: 'NE', NW: 'NW', SE: 'SE', SW: 'SW' },
};
const dirs = computed(() => dirMap[props.lang] || dirMap.pt);

const center = computed(() => props.size / 2);

// ---- ARCO DE INTENSIDADE (fora do círculo) ----
function corIntensidade(val) {
  if (val <= 15) return '#43a047';
  if (val <= 35) return '#fbc02d';
  return '#e53935';
}
function polarToCartesian(cx, cy, r, angleInDegrees) {
  const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;
  return { x: cx + (r * Math.cos(angleInRadians)), y: cy + (r * Math.sin(angleInRadians)) };
}
function describeArc(cx, cy, r, startAngle, endAngle) {
  const start = polarToCartesian(cx, cy, r, endAngle);
  const end = polarToCartesian(cx, cy, r, startAngle);
  const arcSweep = endAngle - startAngle <= 180 ? '0' : '1';
  return ['M', start.x, start.y, 'A', r, r, 0, arcSweep, 0, end.x, end.y].join(' ');
}
const arco = computed(() => (Math.min(props.intensidade, props.max) / props.max) * 270);
const arcPath = computed(() => describeArc(center.value, center.value, center.value - 1, 0, arco.value));
const pointerColor = computed(() => corIntensidade(props.intensidade));
// ----- FIM ARCO INTENSIDADE -----

// ✅ INVERTE A DIREÇÃO: adiciona 180° (para onde vai)
const pointerAngle = computed(() => {
  const dir = Number.isFinite(props.direction) ? props.direction : 0;
  // mapeamento original era (90 - dir); agora somamos +180 e normalizamos
  const angle = 90 - ((dir + 180) % 360);
  return ((angle % 360) + 360) % 360; // garante 0–359
});

const len = computed(() => center.value * 0.65 + center.value * 0.35 * Math.min((props.intensidade || 0) / props.max, 1));
// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : props.intensidade.toFixed(2)));
const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: `${center.value}px ${center.value}px`,
  transition: 'transform .28s cubic-bezier(.65,.05,.36,1), filter .25s',
}));
const polygonPoints = computed(() => {
  const l = len.value;
  const c = center.value;
  return `
    ${c},${c - l}
    ${c + props.size * 0.025},${c - l + props.size * 0.12}
    ${c},${c - l + props.size * 0.07}
    ${c - props.size * 0.025},${c - l + props.size * 0.12}
  `;
});
</script>
