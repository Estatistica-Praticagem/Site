<template>
  <div
    class="tide-scrubber-wrap"
    :style="{ height: chartHeight + 'px' }"
    @mousedown="startDrag"
    @touchstart="startDrag"
    ref="container"
  >
    <svg
      :width="'100%'"
      :height="chartHeight"
      class="scrubber-svg"
      pointer-events="none"
    >
      <!-- Linha tracejada -->
      <line
        :x1="pointX"
        y1="10"
        :x2="pointX"
        :y2="chartHeight - 8"
        stroke="#145A32"
        stroke-width="2"
        stroke-dasharray="7 7"
        opacity="0.33"
      />
      <!-- Linha sólida do ponto até o topo -->
      <line
        :x1="pointX"
        :y1="dotY + dotSize/2"
        :x2="pointX"
        y2="12"
        stroke="#2684ff"
        stroke-width="2.6"
        opacity="0.60"
      />
    </svg>
    <!-- Bola arrastável única -->
    <div
      class="scrub-point"
      :style="{ left: `calc(${pointPercent}% - ${dotSize/2}px)`, top: `${dotY}px` }"
      tabindex="0"
      @keydown.left.prevent="movePoint(-1)"
      @keydown.right.prevent="movePoint(1)"
      :class="{ dragging: isDragging }"
      :aria-label="horaLabel"
    >
      <div class="scrub-dot" />
      <div class="scrub-label">{{ horaLabel }}</div>
    </div>
  </div>
</template>

<script setup>
import {
  ref, computed, watch, onBeforeUnmount,
} from 'vue';

const props = defineProps({
  data: { type: Array, required: true },
  chartHeight: { type: Number, default: 48 },
  lines: { type: Array, default: () => [] },
  scrubIndex: { type: Number, default: null },
  labels: { type: Array, default: () => [] },
});
const emit = defineEmits(['scrub']);

const isDragging = ref(false);
const dotSize = 22;
const container = ref(null);

const internalIdx = ref(
  props.scrubIndex ?? Math.floor((props.data.length - 1) / 2),
);

watch(() => props.scrubIndex, (v) => {
  if (typeof v === 'number' && v !== internalIdx.value) {
    internalIdx.value = v;
  }
});
watch(() => props.data, () => {
  internalIdx.value = props.data.length > 0 ? props.data.length - 1 : 0;
  emit('scrub', internalIdx.value);
}, { immediate: true });

function setIndex(idx) {
  if (idx < 0) idx = 0;
  if (idx >= props.data.length) idx = props.data.length - 1;
  internalIdx.value = idx;
  emit('scrub', idx);
}
function movePoint(delta) {
  setIndex(internalIdx.value + delta);
}

let dragUnbind = null;
// eslint-disable-next-line no-unused-vars
function startDrag(e) {
  isDragging.value = true;
  function onMove(ev) {
    const moveX = (ev.touches ? ev.touches[0].clientX : ev.clientX);
    if (!container.value) return;
    const rect = container.value.getBoundingClientRect();
    const relX = Math.max(0, Math.min(moveX - rect.left, rect.width));
    const percent = relX / rect.width;
    const idx = Math.round(percent * (props.data.length - 1));
    setIndex(idx);
  }
  function onUp() {
    isDragging.value = false;
    window.removeEventListener('mousemove', onMove);
    window.removeEventListener('mouseup', onUp);
    window.removeEventListener('touchmove', onMove);
    window.removeEventListener('touchend', onUp);
  }
  window.addEventListener('mousemove', onMove);
  window.addEventListener('mouseup', onUp);
  window.addEventListener('touchmove', onMove);
  window.addEventListener('touchend', onUp);
  dragUnbind = onUp;
}
onBeforeUnmount(() => { if (dragUnbind) dragUnbind(); });

const pointPercent = computed(() => (props.data.length <= 1
  ? 0
  : ((internalIdx.value || 0) * 100) / (props.data.length - 1)));
const pointX = computed(() => {
  if (!container.value || props.data.length <= 1) return 0;
  const width = container.value.offsetWidth || 1;
  return ((internalIdx.value || 0) / (props.data.length - 1)) * width;
});
const dotY = computed(() => (props.chartHeight > dotSize ? props.chartHeight - dotSize - 4 : 6));
const horaLabel = computed(() => props.labels?.[internalIdx.value] || '--:--');
</script>

<style scoped>
.tide-scrubber-wrap {
  width: 100%;
  position: relative;
  pointer-events: auto;
  z-index: 4;
  height: 48px;
  background: transparent;
}
.scrubber-svg {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0; left: 0;
  pointer-events: none;
}
.scrub-point {
  position: absolute;
  pointer-events: auto;
  z-index: 10;
  outline: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  user-select: none;
  cursor: grab;
  transition: filter 0.15s;
}
.scrub-point:active,
.scrub-point.dragging {
  filter: brightness(1.15) drop-shadow(0 0 4px #26a4fb66);
  cursor: grabbing;
}
.scrub-dot {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: radial-gradient(ellipse at 65% 45%, #1e78db 90%, #0c3c85 100%);
  border: 2px solid #fff;
  box-shadow: 0 2px 7px #145a3266, 0 1px 0px #aaa;
  transition: box-shadow 0.17s, border 0.17s;
  pointer-events: none;
}
.scrub-label {
  margin-top: 1.5px;
  font-size: 0.97em;
  color: #1565c0;
  background: #fff;
  padding: 0 7px 1px 7px;
  border-radius: 7px;
  margin-bottom: 0;
  font-weight: 500;
  box-shadow: 0 2px 6px #e7eaf5c4;
  border: 1.5px solid #e9f1ff;
  pointer-events: none;
}
@media (max-width:700px) {
  .scrub-dot { width: 15px; height: 15px; }
  .scrub-label { font-size: .90em; padding: 0 5px; }
}
</style>
