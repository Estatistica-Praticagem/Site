<template>
  <svg :width="size" :height="size" :viewBox="`0 0 ${size} ${size}`" class="gauge-svg">
    <defs>
      <radialGradient :id="bgGradId" cx="50%" cy="50%" r="70%">
        <stop offset="0%" stop-color="#fff" />
        <stop offset="100%" stop-color="#e3edf7" />
      </radialGradient>
    </defs>
    <circle :cx="mid" :cy="mid" :r="radius" :fill="`url(#${bgGradId})`" stroke="#cfd8dc" stroke-width="3"/>
    <g :transform="`rotate(-135 ${mid} ${mid})`">
      <path
        :d="arcPath"
        :stroke="gaugeColor(value)"
        stroke-width="9"
        fill="none"
        stroke-linecap="round"
      />
    </g>
    <circle :cx="mid" :cy="mid" :r="innerR" fill="#fff" stroke="#e3e3e3" stroke-width="2"/>
    <text :x="mid" :y="mid+6" text-anchor="middle" font-size="17" fill="#333" font-weight="bold">
      {{ valueText }}
    </text>
    <text :x="mid" :y="mid+20" text-anchor="middle" font-size="10" fill="#789">{{ unidade }}</text>
    <text v-if="label" :x="mid" :y="mid+35" text-anchor="middle" font-size="12" fill="#888">{{ label }}</text>
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: { type: Number, default: 0 },
  max: { type: Number, default: 10 },
  unidade: { type: String, default: '' },
  label: { type: String, default: '' },
  // eslint-disable-next-line no-unused-vars
  colorFn: { type: Function, default: (v) => '#43a047' }, // cor dinâmica
  size: { type: Number, default: 110 },
});
const bgGradId = `bgBaseGrad${Math.floor(Math.random() * 9999)}`;
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
const arc = computed(() => (Math.min(props.value, props.max) / props.max) * 270);
const arcPath = computed(() => describeArc(mid.value, mid.value, radius.value, 0, arc.value));
// eslint-disable-next-line no-restricted-globals
const valueText = computed(() => (isNaN(props.value) ? '--' : props.value.toFixed(2)));
const gaugeColor = (v) => props.colorFn(v);
</script>

<style scoped>
.gauge-svg {
  width: 110px;
  height: 110px;
  display: block;
}
</style>
