<template>
  <q-card class="q-pa-md bg-white shadow-2 tide-compare-card" :style="{ position: 'relative', borderRadius: '14px', maxWidth: '950px', margin: 'auto' }">
    <!-- Cards Profundidades (valores atuais) -->
    <div class="row q-col-gutter-md q-mb-md justify-center">
      <q-card
        v-for="depth in depths"
        :key="depth"
        flat
        bordered
        class="text-center bg-grey-1 q-pa-sm"
        style="min-width: 130px; max-width: 160px; border-radius: 12px"
      >
        <div class="text-subtitle2 text-bold">{{ formatDepth(depth) }}</div>
        <div class="text-h6 text-weight-bold">
          {{ getLastValue(depth) }} <span class="text-caption">tk</span>
        </div>
      </q-card>
    </div>

    <!-- Header -->
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-primary text-weight-bold">
        Comparativo Correnteza por Profundidade
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
    >
      <Line :data="lineChartData" :options="lineChartOptions" :height="config.chartHeight" />
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
        :pagination="{ rowsPerPage: 10 }"
        class="q-mt-md"
        style="border-radius: 12px"
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
  ref, computed, onMounted, watch,
} from 'vue';
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import { Line, Bar } from 'vue-chartjs';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend, Filler);

const store = useWeatherStore();
const { weatherHistory } = storeToRefs(store);

const depths = ['1_5m', '3m', '6m'];
const formatDepth = (d) => d.replace('_', '.');
const getLastValue = (depth) => {
  // eslint-disable-next-line no-use-before-define
  const last = [...(dataset.value || [])].reverse().find((item) => item[`intensidade_${depth}`] != null);
  return last ? Number(last[`intensidade_${depth}`]).toFixed(2) : '--';
};

/* ---- Config Avançada ---- */
const showConfig = ref(false);

const linesOptions = [
  { label: '1.5m', value: 'i15' },
  { label: '3m', value: 'i3' },
  { label: '6m', value: 'i6' },
];
const activeLineColors = {
  i15: '#1e78db',
  i3: '#960d0d',
  i6: '#FFDC66',
};
const lineFields = {
  i15: 'intensidade_1_5m',
  i3: 'intensidade_3m',
  i6: 'intensidade_6m',
};
const lineLabels = {
  i15: '1.5m',
  i3: '3m',
  i6: '6m',
};
const defaultConfig = () => ({
  chartHeight: 320,
  showPoints: false,
  showBand: false,
  lines: ['i15', 'i3', 'i6'],
  viewType: 'chart',
});
const config = ref(defaultConfig());
onMounted(() => {
  const saved = localStorage.getItem('tideCompareCorrConfig');
  if (saved) Object.assign(config.value, JSON.parse(saved));
});
watch(config, () => {
  localStorage.setItem('tideCompareCorrConfig', JSON.stringify(config.value));
}, { deep: true });

/* ---- Navegação temporal ---- */
const windowSize = 144;
const stepSize = 72;

// ==== AJUSTE DE NORMALIZAÇÃO AQUI ====
const dataset = computed(() => (weatherHistory.value || [])
  .filter((item) => item.timestamp_br)
  .map((item) => {
    let ts = '';
    if (item.timestamp_br?.date) {
      ts = item.timestamp_br.date.slice(0, 16);
    } else if (typeof item.timestamp_br === 'string') {
      ts = item.timestamp_br.slice(0, 16);
    }
    const i15 = item.intensidade_1_5m;
    const i3 = item.intensidade_3m;
    const i6 = item.intensidade_6m;
    return {
      ...item,
      timestamp_br: ts,
      // eslint-disable-next-line no-restricted-globals
      intensidade_1_5m: isFinite(Number(i15)) ? Number(i15) : null,
      // eslint-disable-next-line no-restricted-globals
      intensidade_3m: isFinite(Number(i3)) ? Number(i3) : null,
      // eslint-disable-next-line no-restricted-globals
      intensidade_6m: isFinite(Number(i6)) ? Number(i6) : null,
    };
  })
  .filter((item) => (
    item.intensidade_1_5m !== null
      || item.intensidade_3m !== null
      || item.intensidade_6m !== null
  )));
// =====================================

const maxCursor = computed(() => Math.max(0, dataset.value.length - windowSize));
const cursor = ref(maxCursor.value);
watch(maxCursor, (max) => { cursor.value = max; });
function prevWindow() { cursor.value = Math.max(0, cursor.value - stepSize); }
function nextWindow() { cursor.value = Math.min(maxCursor.value, cursor.value + stepSize); }
const dataSlice = computed(() => dataset.value.slice(cursor.value, cursor.value + windowSize));
const windowLabel = computed(() => {
  const ini = dataSlice.value[0]?.timestamp_br?.slice(11, 16) || '';
  const fim = dataSlice.value[dataSlice.value.length - 1]?.timestamp_br?.slice(11, 16) || '';
  return ini && fim ? `${ini} – ${fim}` : '';
});

/* ---- Linhas visíveis ---- */
const lastRemoved = ref('i15');
function removedLine(newLines) {
  const prev = config.value.lines;
  // eslint-disable-next-line no-restricted-syntax
  for (const l of prev) {
    if (!newLines.includes(l)) return l;
  }
  return 'i15';
}
function handleLineChange(lines) {
  if (lines.length === 0) config.value.lines = [lastRemoved.value || 'i15'];
  else lastRemoved.value = removedLine(lines);
}
const activeLines = computed(() => config.value.lines);

/* ---- Dados para o gráfico de linhas ---- */
const lineChartData = computed(() => {
  const labels = dataSlice.value.map((item) => item.timestamp_br?.slice(11, 16) || '');
  const datasets = [];
  activeLines.value.forEach((key) => {
    const serie = dataSlice.value.map((item) => (
      typeof item[lineFields[key]] === 'number' ? item[lineFields[key]] : Number(item[lineFields[key]]) || null
    ));
    if (config.value.showBand) {
      const valid = serie.filter((v) => v != null);
      if (valid.length) {
        const minVal = Math.min(...valid);
        const maxVal = Math.max(...valid);
        datasets.push({
          label: `${lineLabels[key]} min`,
          data: Array(labels.length).fill(minVal),
          borderColor: 'rgba(0,0,0,0)',
          backgroundColor: 'rgba(0,0,0,0)',
          fill: false,
          pointRadius: 0,
          order: 0,
          hidden: true,
          spanGaps: true,
        });
        datasets.push({
          label: `${lineLabels[key]} max`,
          data: Array(labels.length).fill(maxVal),
          borderColor: 'rgba(0,0,0,0)',
          // eslint-disable-next-line no-use-before-define
          backgroundColor: hexToRgba(activeLineColors[key], 0.13),
          fill: '-1',
          pointRadius: 0,
          order: 0,
          hidden: false,
          spanGaps: true,
        });
      }
    }
    datasets.push({
      label: lineLabels[key],
      data: serie,
      borderColor: activeLineColors[key],
      backgroundColor: `${activeLineColors[key]}33`,
      tension: 0.32,
      pointRadius: config.value.showPoints ? 2 : 0,
      borderWidth: 2,
      fill: false,
      spanGaps: true,
      order: 2,
    });
  });
  return { labels, datasets };
});
const lineChartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  animation: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        boxWidth: 16,
        font: { size: 15 },
        filter: (item) => !item.text.includes('min') && !item.text.includes('max'),
      },
    },
    tooltip: {
      enabled: true,
      callbacks: {
        label: (context) => {
          let val = context.parsed.y;
          val = val != null ? Number(val).toFixed(3) : '--';
          return `${context.dataset.label}: ${val} tk`;
        },
      },
    },
  },
  interaction: { intersect: false, mode: 'nearest', axis: 'x' },
  scales: {
    y: {
      min: 0.0,
      max: 3.0,
      title: { display: true, text: 'Intensidade (tk)', font: { size: 14 } },
    },
    x: {
      title: { display: true, text: 'Hora', font: { size: 13 } },
      ticks: { autoSkip: true, maxTicksLimit: 20 },
    },
  },
}));

/* ---- Dados para o gráfico de barras ---- */
const barChartData = computed(() => {
  const labels = dataSlice.value.map((item) => item.timestamp_br?.slice(11, 16) || '');
  const datasets = activeLines.value.map((key) => ({
    label: lineLabels[key],
    data: dataSlice.value.map((item) => (typeof item[lineFields[key]] === 'number' ? item[lineFields[key]] : Number(item[lineFields[key]]) || null)),
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
  scales: { y: { min: 0.0, max: 3.0 } },
}));

/* ---- Tabela ---- */
const tableColumns = [
  {
    name: 'hora', label: 'Hora', field: 'hora', align: 'center', sortable: true,
  },
  {
    name: 'i15', label: '1.5m', field: 'i15', align: 'center', sortable: true,
  },
  {
    name: 'i3', label: '3m', field: 'i3', align: 'center', sortable: true,
  },
  {
    name: 'i6', label: '6m', field: 'i6', align: 'center', sortable: true,
  },
];
const tableRows = computed(() => dataSlice.value
  .filter((item) => {
    const dt = item.timestamp_br;
    if (!dt) return false;
    const min = dt.slice(14, 16);
    return min === '00' || min === '30';
  })
  .map((item) => {
    const dt = item.timestamp_br ?? '';
    return {
      hora: dt.slice(11, 16),
      i15: item.intensidade_1_5m != null ? Number(item.intensidade_1_5m).toFixed(2) : '--',
      i3: item.intensidade_3m != null ? Number(item.intensidade_3m).toFixed(2) : '--',
      i6: item.intensidade_6m != null ? Number(item.intensidade_6m).toFixed(2) : '--',
      timestamp: dt,
    };
  }));

/* ---- Utils ---- */
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

<style scoped>
.tide-compare-card {
  border-radius: 14px;
  max-width: 950px;
  margin: auto;
}
.tide-chart-container {
  width: 100%;
  min-height: 240px;
  max-height: 340px;
  height: 320px;
  position: relative;
}
.tide-toggle {
  min-width: 180px;
}
@media (max-width: 700px) {
  .tide-chart-container {
    min-height: 160px;
    max-height: 220px;
    height: 180px;
  }
  .tide-compare-card {
    max-width: 99vw;
    padding: 2px !important;
  }
  .tide-toggle {
    min-width: 120px;
  }
}
</style>
