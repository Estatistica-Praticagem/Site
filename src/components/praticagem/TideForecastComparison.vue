<template>
  <q-card class="q-pa-md bg-white shadow-2" :style="{ position: 'relative', borderRadius: '14px', maxWidth: '950px', margin: 'auto' }">
    <!-- Header -->
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-primary text-weight-bold">
        Comparação de Maré (Marinha x Medida x GA)
      </div>

      <div class="row items-center q-gutter-sm">
        <q-btn-toggle
          v-model="config.viewType"
          :options="[
            { label: 'Gráfico Linha', value: 'chart', icon: 'show_chart' },
            { label: 'Gráfico Barras', value: 'bar', icon: 'bar_chart' },
            { label: 'Tabela', value: 'table', icon: 'table_chart' }
          ]"
          toggle-color="primary"
          spread
          size="sm"
          class="tide-toggle"
        />
        <q-btn
          flat dense round icon="more_vert"
          @click="showConfig = true"
          aria-label="Configurações"
          class="q-ml-xs"
        />
      </div>
    </div>

    <!-- Configurações -->
    <q-dialog v-model="showConfig" persistent>
      <q-card style="min-width:360px;max-width:97vw;">
        <q-card-section class="row items-center q-pa-sm">
          <div class="text-h6 text-primary">Configurações do Gráfico</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showConfig = false" />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-gutter-md">

          <!-- Altura -->
          <div>
            <div class="text-bold q-mb-xs">Altura do Gráfico</div>
            <div class="row items-center">
              <q-slider
                v-model="config.chartHeight"
                :min="160"
                :max="700"
                :step="10"
                color="primary"
                label
                style="max-width:260px;margin-left:8px;"
              />
              <span class="q-ml-md text-grey-7">{{ config.chartHeight }} px</span>
            </div>
          </div>

          <!-- Pontos -->
          <div>
            <div class="text-bold q-mb-xs">Pontos no gráfico</div>
            <q-option-group
              v-model="config.showPoints"
              :options="[
                { label: 'Com bolinhas', value: true },
                { label: 'Sem bolinhas', value: false }
              ]"
              type="radio"
              color="primary"
              inline
            />
          </div>

          <!-- Linhas visíveis -->
          <div>
            <div class="text-bold q-mb-xs">Linhas Visíveis</div>
            <q-option-group
              v-model="config.lines"
              :options="linesOptions"
              type="checkbox"
              color="primary"
              @update:model-value="handleLineChange"
            />
            <span v-if="config.lines.length === 0" class="text-negative text-caption">
              Pelo menos uma linha precisa estar selecionada.
            </span>
          </div>

          <!-- Bandas -->
          <div>
            <q-toggle
              v-model="config.showBand"
              color="primary"
              label="Exibir banda (faixa entre o menor e o maior valor que cada linha atingiu na janela atual)"
            />
          </div>

          <!-- Tooltip -->
          <div>
            <div class="text-bold q-mb-xs">Tamanho do card de valores</div>
            <div class="row items-center q-gutter-sm">
              <!-- <q-select
                v-model="config.tooltipPosition"
                :options="tooltipPosOptions"
                label="Posição"
                dense
                outlined
                style="min-width: 180px"
              /> -->
              <q-slider
                v-model="config.tooltipScale"
                :min="0.8"
                :max="2"
                :step="0.05"
                color="primary"
                label
                style="max-width:160px"
              />
              <span class="text-grey-7">{{ config.tooltipScale.toFixed(2) }}×</span>
            </div>
          </div>

        </q-card-section>

        <q-separator />

        <q-card-actions align="right">
          <q-btn flat label="Fechar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Seletor temporal -->
    <div
      v-if="config.viewType !== 'table'"
      class="row items-center q-mb-sm"
      style="justify-content:left;"
    >
      <q-btn dense flat icon="chevron_left" @click="prevWindow" :disable="cursor <= 0" class="q-mr-xs" />
      <span class="text-caption">{{ windowLabel }}</span>
      <q-btn dense flat icon="chevron_right" @click="nextWindow" :disable="cursor >= maxCursor" class="q-ml-xs" />
    </div>

    <!-- GRÁFICO LINHA -->
    <div
      v-if="config.viewType === 'chart'"
      :style="{
        width: '100%',
        height: config.chartHeight + 'px',
        minHeight: '160px',
        maxHeight: '700px',
        position: 'relative',
        marginBottom: '0.2rem'
      }"
      ref="chartWrap"
    >
      <canvas ref="canvasRef" :height="config.chartHeight" style="width:100%;"></canvas>

      <!-- Ponteiro/bolinha discreta no rodapé -->
      <div
        v-if="scrubIndex !== null && showScrubPointer"
        :style="scrubPointerStyle"
        @touchmove.prevent="onScrubTouch"
        @touchstart.prevent="onScrubTouch"
      >
        <div :style="scrubDotStyle" />
      </div>

      <!-- Tooltip custom (inline style completo) -->
      <div
        v-if="tooltip.active"
        :style="tooltipStyle"
      >
        <div class="text-caption" style="font-weight:bold;">
          {{ tooltip.label }}
        </div>
        <div
          v-for="(v, l) in tooltip.values"
          :key="l"
          class="q-mt-xs"
        >
          <span :style="{ color: v.color }">
            <b>{{ l }}:</b> {{ v.value }}
          </span>
        </div>
      </div>
    </div>

    <!-- GRÁFICO BARRAS -->
    <div
      v-else-if="config.viewType === 'bar'"
      :style="{ width: '100%', height: config.chartHeight + 'px', minHeight: '160px', maxHeight: '700px', position: 'relative', marginBottom: '0.2rem' }"
    >
      <Bar :data="barChartData" :options="barChartOptions" :height="config.chartHeight" />
    </div>

    <!-- TABELA -->
    <div v-else>
      <q-table
        dense
        :rows="tableRows"
        :columns="tableColumns"
        row-key="timestamp"
        flat bordered
        :rows-per-page-options="[10, 20, 50, 100]"
        :pagination="{rowsPerPage: 10}"
        class="q-mt-md"
        style="border-radius:12px"
        color="primary"
        hide-bottom
        wrap-cells
        no-data-label="Nenhum dado disponível para exibir"
      />
    </div>
  </q-card>
</template>

<script setup>
import {
  ref, reactive, computed, onMounted, onUnmounted, watch, nextTick,
} from 'vue';
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import { Bar } from 'vue-chartjs';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip as ChartTooltip, Legend, Filler,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, ChartTooltip, Legend, Filler);

const store = useWeatherStore();
const { weatherForecast } = storeToRefs(store);

const showConfig = ref(false);

/* ---------------- Configurações ---------------- */
const linesOptions = [
  { label: 'Marinha', value: 'marinha' },
  { label: 'Maré Medida', value: 'real' },
  { label: 'Previsão GA', value: 'ga' },
];

const activeLineColors = {
  marinha: '#FFDC66',
  real: '#960d0d',
  ga: '#1e78db',
};

const lineFields = {
  marinha: 'altura_prev_getmare',
  real: 'altura_real_getmare',
  ga: 'altura_prevista',
};

const lineLabels = {
  marinha: 'Marinha',
  real: 'Maré Medida',
  ga: 'Previsão GA',
};

// const tooltipPosOptions = [
//   { label: 'Seguir o mouse', value: 'follow' },
//   { label: 'Topo esquerdo', value: 'top-left' },
//   { label: 'Topo direito', value: 'top-right' },
//   { label: 'Base esquerda', value: 'bottom-left' },
//   { label: 'Base direita', value: 'bottom-right' },
// ];

const defaultConfig = () => ({
  chartHeight: 320,
  showPoints: false,
  showBand: false,
  lines: ['marinha', 'real', 'ga'],
  viewType: 'chart',
  tooltipPosition: 'top-right',
  tooltipScale: 1,
});

const config = ref(defaultConfig());

onMounted(() => {
  const saved = localStorage.getItem('tideCompareConfig');
  if (saved) Object.assign(config.value, JSON.parse(saved));
});
watch(config, () => {
  localStorage.setItem('tideCompareConfig', JSON.stringify(config.value));
}, { deep: true });

/* ---------------- Navegação temporal ---------------- */
const windowSize = 144;
const stepSize = 72;

const dataset = computed(() => {
  const all = (weatherForecast.value || [])
    .filter((item) => item.timestamp_prev && item.altura_prevista != null);
  return all.slice().reverse();
});
const maxCursor = computed(() => Math.max(0, dataset.value.length - windowSize));
const cursor = ref(maxCursor.value);
watch(maxCursor, (max) => { cursor.value = max; });

function prevWindow() { cursor.value = Math.max(0, cursor.value - stepSize); }
function nextWindow() { cursor.value = Math.min(maxCursor.value, cursor.value + stepSize); }

const dataSlice = computed(() => dataset.value.slice(cursor.value, cursor.value + windowSize));
const windowLabel = computed(() => {
  const ini = dataSlice.value[0]?.timestamp_prev?.date?.slice(11, 16) || '';
  const fim = dataSlice.value[dataSlice.value.length - 1]?.timestamp_prev?.date?.slice(11, 16) || '';
  return ini && fim ? `${ini} – ${fim}` : '';
});

/* ---------------- Linhas visíveis ---------------- */
const lastRemoved = ref('marinha');
function removedLine(newLines) {
  const prev = config.value.lines;
  // eslint-disable-next-line no-restricted-syntax
  for (const l of prev) {
    if (!newLines.includes(l)) return l;
  }
  return 'marinha';
}
function handleLineChange(lines) {
  if (lines.length === 0) config.value.lines = [lastRemoved.value || 'marinha'];
  else lastRemoved.value = removedLine(lines);
}
const activeLines = computed(() => config.value.lines);

/* ---------------- Chart Data ---------------- */
const chartData = computed(() => {
  const pointRadius = config.value.showPoints ? 2 : 0;
  const labels = dataSlice.value.map((item) => item.timestamp_prev?.date?.slice(11, 16) || '');
  const datasets = [];

  activeLines.value.forEach((key) => {
    // Se for GA e banda ativada, coloca as linhas +0,15 e -0,15 na ORDEM CERTA!
    if (key === 'ga' && config.value.showBand) {
      const serie = dataSlice.value.map((item) => (typeof item[lineFields.ga] === 'number' ? item[lineFields.ga] : null));
      // -0.15 primeiro!
      const minus = serie.map((v) => (v != null ? v - 0.15 : null));
      const plus = serie.map((v) => (v != null ? v + 0.15 : null));

      // Linha inferior (NÃO tem fill)
      datasets.push({
        label: 'Previsão GA -0,15',
        data: minus,
        // eslint-disable-next-line no-use-before-define
        borderColor: hexToRgba(activeLineColors.ga, 0.90),
        // eslint-disable-next-line no-use-before-define
        backgroundColor: hexToRgba(activeLineColors.ga, 0.13),
        borderWidth: 1.7,
        fill: false, // Não preenche nada
        pointRadius,
        tension: 0.32,
        spanGaps: true,
        order: 1,
      });

      // Linha superior (preenche entre esta e a anterior)
      datasets.push({
        label: 'Previsão GA +0,15',
        data: plus,
        // eslint-disable-next-line no-use-before-define
        borderColor: hexToRgba(activeLineColors.ga, 0.90),
        // eslint-disable-next-line no-use-before-define
        backgroundColor: hexToRgba(activeLineColors.ga, 0.13),
        borderWidth: 1.7,
        fill: '-1', // Pinta entre +0,15 e -0,15
        pointRadius,
        tension: 0.32,
        spanGaps: true,
        order: 1,
      });
      // NÃO adiciona a linha central da previsão GA quando banda está ativa!
      return;
    }

    // Banda padrão para as outras linhas (mantém como está)
    if (config.value.showBand) {
      const serie = dataSlice.value.map((item) => (typeof item[lineFields[key]] === 'number' ? item[lineFields[key]] : null));
      const valid = serie.filter((v) => v != null);
      const minVal = Math.min(...valid);
      const maxVal = Math.max(...valid);
      const minArr = Array(labels.length).fill(minVal);
      const maxArr = Array(labels.length).fill(maxVal);

      datasets.push({
        label: `${lineLabels[key]}_band_bottom`,
        data: minArr,
        borderColor: 'rgba(0,0,0,0)',
        backgroundColor: 'rgba(0,0,0,0)',
        fill: false,
        pointRadius: 0,
        order: 0,
        hidden: true,
        spanGaps: true,
      });
      datasets.push({
        label: `${lineLabels[key]}_band_top`,
        data: maxArr,
        borderColor: 'rgba(0,0,0,0)',
        // eslint-disable-next-line no-use-before-define
        backgroundColor: hexToRgba(activeLineColors[key], 0.12),
        fill: '-1',
        pointRadius: 0,
        order: 0,
        hidden: false,
        spanGaps: true,
      });
    }

    // Linha principal (apenas se não for o caso GA+banda)
    datasets.push({
      label: lineLabels[key],
      data: dataSlice.value.map((item) => (typeof item[lineFields[key]] === 'number' ? item[lineFields[key]] : null)),
      borderColor: activeLineColors[key],
      backgroundColor: `${activeLineColors[key]}33`,
      tension: 0.32,
      pointRadius,
      borderWidth: 2,
      fill: false,
      spanGaps: true,
      order: 2,
    });
  });

  return { labels, datasets };
});

/* ---------------- Chart.js lifecycle ---------------- */
const canvasRef = ref(null);
const chartWrap = ref(null);
let chartjs = null;

// Tooltip custom (inline only)
const tooltip = reactive({
  active: false,
  label: '',
  values: {},
  left: 0,
  top: 0,
});

// estilo inline para tooltip – sempre definindo os 4 lados
const tooltipStyle = computed(() => {
  const baseBox = {
    position: 'absolute',
    zIndex: 99,
    background: '#fff',
    color: '#263238',
    borderRadius: '8px',
    boxShadow: '0 6px 16px rgba(38,50,56,0.2)',
    padding: '10px 14px 9px 12px',
    pointerEvents: 'none',
    fontSize: '1.06em',
    lineHeight: '1.18em',
    border: '1.5px solid #d7e9fa',
    userSelect: 'none',
    minWidth: '128px',
    transform: `scale(${config.value.tooltipScale})`,
  };

  // default/fallback: top-right
  let style = {
    ...baseBox, left: 'auto', top: '8px', right: '8px', bottom: 'auto',
  };

  switch (config.value.tooltipPosition) {
    case 'top-left':
      style = {
        ...baseBox, left: '8px', top: '8px', right: 'auto', bottom: 'auto',
      };
      break;
    case 'top-right':
      style = {
        ...baseBox, left: 'auto', top: '8px', right: '8px', bottom: 'auto',
      };
      break;
    case 'bottom-left':
      style = {
        ...baseBox, left: '8px', top: 'auto', right: 'auto', bottom: '8px',
      };
      break;
    case 'bottom-right':
      style = {
        ...baseBox, left: 'auto', top: 'auto', right: '8px', bottom: '8px',
      };
      break;
    case 'follow':
      style = {
        ...baseBox,
        left: `${tooltip.left}px`,
        top: `${tooltip.top}px`,
        right: 'auto',
        bottom: 'auto',
      };
      break;
    default:
      // fallback seguro
      style = {
        ...baseBox, left: 'auto', top: '8px', right: '8px', bottom: 'auto',
      };
  }

  return style;
});

// Ponteiro / scrub
const scrubIndex = ref(null);
const scrubPixel = ref(0);
const showScrubPointer = ref(false);

const scrubPointerStyle = computed(() => ({
  position: 'absolute',
  width: '22px',
  height: '22px',
  zIndex: 20,
  transform: 'translateX(-50%)',
  pointerEvents: 'none',
  left: `${scrubPixel.value}px`,
  bottom: '6px',
}));
const scrubDotStyle = {
  width: '100%',
  height: '100%',
  borderRadius: '50%',
  background: 'radial-gradient(circle at 60% 40%, #1e78db 90%, #0c3c85 100%)',
  border: '2px solid #fff',
  boxShadow: '0 2px 7px rgba(20,90,50,0.38), 0 1px 0px #aaa',
  opacity: 0.65,
};

function renderChart() {
  if (!canvasRef.value) return;
  if (chartjs) { chartjs.destroy(); chartjs = null; }

  chartjs = new Chart(canvasRef.value.getContext('2d'), {
    type: 'line',
    data: chartData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            boxWidth: 16,
            font: { size: 15 },
            filter: (item) => !item.text.includes('_band_'),
          },
        },
        tooltip: { enabled: false },
      },
      interaction: { intersect: false, mode: 'nearest', axis: 'x' },
      scales: {
        y: {
          min: -0.5,
          max: 1.5,
          title: { display: true, text: 'Altura (m)', font: { size: 14 } },
        },
        x: {
          title: { display: true, text: 'Hora', font: { size: 13 } },
          ticks: { autoSkip: true, maxTicksLimit: 20 },
        },
      },
      onHover: (event, elements) => {
        if (!elements.length) {
          if (config.value.tooltipPosition === 'follow') tooltip.active = false;
          showScrubPointer.value = false;
          return;
        }
        const point = elements[0];
        const idx = point.index;
        scrubIndex.value = idx;
        showScrubPointer.value = true;

        const vals = {};
        activeLines.value.forEach((key) => {
          const ds = chartData.value.datasets.find((d) => d.label === lineLabels[key]);
          const val = ds?.data?.[idx];
          vals[lineLabels[key]] = {
            value: val != null ? Number(val).toFixed(3) : '--',
            color: activeLineColors[key],
          };
        });

        if (config.value.tooltipPosition === 'follow') {
          tooltip.left = event.offsetX + 8;
          tooltip.top = event.offsetY + 8;
        }
        tooltip.label = chartData.value.labels[idx];
        tooltip.values = vals;
        tooltip.active = true;
      },
      onLeave: () => {
        if (config.value.tooltipPosition === 'follow') tooltip.active = false;
        showScrubPointer.value = false;
      },
    },
    plugins: [{
      id: 'scrubPointer',
      afterDraw(chart) {
        if (scrubIndex.value == null || !showScrubPointer.value) return;

        const { ctx } = chart;
        const x = chart.scales.x.getPixelForValue(scrubIndex.value);
        const y0 = chart.scales.y.top;
        const y1 = chart.scales.y.bottom;

        ctx.save();
        ctx.strokeStyle = '#1e78db55';
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(x, y0);
        ctx.lineTo(x, y1);
        ctx.stroke();
        ctx.restore();

        scrubPixel.value = x;
      },
    }],
  });
}

onMounted(() => nextTick(renderChart));
watch([chartData, config], () => nextTick(renderChart), { deep: true });
watch(dataSlice, () => nextTick(renderChart));

function onScrubTouch(ev) {
  if (!canvasRef.value || !chartjs) return;
  const rect = canvasRef.value.getBoundingClientRect();
  const touch = ev.touches?.[0] || ev;
  const relX = touch.clientX - rect.left;

  const xScale = chartjs.scales.x;
  let minDist = Infinity; let minIdx = 0;
  xScale.ticks.forEach((_, i) => {
    const px = xScale.getPixelForTick(i);
    const dist = Math.abs(px - relX);
    if (dist < minDist) { minDist = dist; minIdx = i; }
  });

  scrubIndex.value = minIdx;
  showScrubPointer.value = true;
}

const handleTouchEnd = () => {
  showScrubPointer.value = false;
  if (config.value.tooltipPosition === 'follow') tooltip.active = false;
};

document.addEventListener('touchend', handleTouchEnd);
onUnmounted(() => {
  document.removeEventListener('touchend', handleTouchEnd);
  if (chartjs) { chartjs.destroy(); chartjs = null; }
});

/* ---------------- BARRAS ---------------- */
const barChartData = computed(() => {
  const labels = dataSlice.value.map((item) => item.timestamp_prev?.date?.slice(11, 16) || '');
  const datasets = activeLines.value.map((key) => ({
    label: lineLabels[key],
    data: dataSlice.value.map((item) => (typeof item[lineFields[key]] === 'number' ? item[lineFields[key]] : null)),
    backgroundColor: activeLineColors[key],
    borderRadius: 5,
    maxBarThickness: 12,
  }));
  return { labels, datasets };
});
const barChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: true }, tooltip: { enabled: true } },
  scales: { y: { min: -0.5, max: 1.5 } },
}));

/* ---------------- TABELA ---------------- */
const tableColumns = [
  {
    name: 'hora', label: 'Hora', field: 'hora', align: 'center', sortable: true,
  },
  {
    name: 'marinha', label: 'Marinha', field: 'marinha', align: 'center', sortable: true,
  },
  {
    name: 'real', label: 'Maré Medida', field: 'real', align: 'center', sortable: true,
  },
  {
    name: 'ga', label: 'Previsão GA', field: 'ga', align: 'center', sortable: true,
  },
];
const tableRows = computed(() => dataSlice.value
  .filter((item) => {
    const dt = item.timestamp_prev?.date ?? item.timestamp_prev;
    if (!dt) return false;
    const min = dt.slice(14, 16);
    return min === '00' || min === '30';
  })
  .map((item) => {
    const dt = item.timestamp_prev?.date ?? item.timestamp_prev ?? '';
    const hora = dt.slice(11, 16);
    return {
      hora,
      marinha: item.altura_prev_getmare != null ? Number(item.altura_prev_getmare).toFixed(2) : '--',
      real: item.altura_real_getmare != null ? Number(item.altura_real_getmare).toFixed(2) : '--',
      ga: item.altura_prevista != null ? Number(item.altura_prevista).toFixed(2) : '--',
      timestamp: dt,
    };
  }));

/* ---------------- Utils ---------------- */
function hexToRgba(hex, alpha = 1) {
  const h = hex.replace('#', '');
  const bigint = parseInt(h, 16);
  // eslint-disable-next-line no-bitwise
  const r = (bigint >> 16) & 255;
  // eslint-disable-next-line no-bitwise
  const g = (bigint >> 8) & 255;
  // eslint-disable-next-line no-bitwise
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
</script>
