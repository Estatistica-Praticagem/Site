<template>
  <q-page class="q-pa-md column gap-md">
    <!-- Título -->
    <div class="text-h5 text-center">Monitoramento – mestre_hour_tratada</div>

    <!-- Seletor de linhas / Tabela -->
    <q-card flat bordered class="q-pa-none">
      <q-card-section class="row items-center q-gutter-sm">
        <div class="text-subtitle2">Dados ("planilha")</div>
        <q-select
          dense outlined square
          v-model="rowsPerPage"
          :options="rowsPerPageOptions"
          label="Linhas por página"
          style="width: 140px;"
        />
      </q-card-section>

      <!-- Scroll horizontal externo  -->
      <div class="scroll" style="overflow-x:auto;">
        <q-table
          :rows="rows"
          :columns="columns"
          row-key="timestamp_br"
          dense
          flat
          bordered
          separator="horizontal"
          :rows-per-page-options="rowsPerPageOptions"
          :rows-per-page="rowsPerPage"
          :style="`max-height:${maxTableHeight}px;min-width:1200px;`"
          virtual-scroll
          no-data-label="Sem dados"
          :loading="loading"
        >
          <template v-slot:loading>
            <q-inner-loading showing color="primary" />
          </template>
        </q-table>
      </div>
    </q-card>

    <!-- Gráfico Altura da Maré -->
    <q-card flat bordered class="q-pa-md">
      <div class="text-subtitle2 q-mb-md">Altura da Maré (Prevista x Real)</div>
      <canvas ref="tideChartCanvas" height="200"></canvas>
    </q-card>

    <!-- Gráfico Direção Vento -->
    <q-card flat bordered class="q-pa-md">
      <div class="text-subtitle2 q-mb-md">Direção Média do Vento</div>
      <canvas ref="windChartCanvas" height="200"></canvas>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  TimeScale,
  RadialLinearScale,
  Filler,
  Tooltip,
  Legend,
  PolarAreaController,
  ArcElement,
} from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  TimeScale,
  RadialLinearScale,
  Filler,
  Tooltip,
  Legend,
  PolarAreaController,
  ArcElement,
);

const $q = useQuasar();

const rows = ref([]);
const columns = ref([]);
const loading = ref(true);

const rowsPerPageOptions = [10, 20, 50, 100, 0];
const rowsPerPage = ref(100);
const maxTableHeight = 600;

const tideChartCanvas = ref(null);
const windChartCanvas = ref(null);
let tideChartInstance = null;
let windChartInstance = null;

function flattenDates(row) {
  const flat = { ...row };
  // eslint-disable-next-line no-restricted-syntax
  for (const key in flat) {
    if (flat[key] && typeof flat[key] === 'object' && 'date' in flat[key]) {
      flat[key] = flat[key].date;
    }
  }
  return flat;
}

function buildColumns(sampleRow) {
  return Object.keys(sampleRow).map((key) => ({
    name: key,
    label: key.toUpperCase(),
    align: 'left',
    field: key,
    sortable: true,
  }));
}

function renderTideChart(data) {
  if (!tideChartCanvas.value) return;
  if (tideChartInstance) tideChartInstance.destroy();

  const labels = data.map((r) => r.timestamp_br);
  const alturaReal = data.map((r) => r.altura_real_getmare);
  const alturaPrev = data.map((r) => r.altura_prev_getmare);
  const alturaPrevista = data.map((r) => (typeof r.altura_prevista === 'number' ? r.altura_prevista : null));

  tideChartInstance = new Chart(tideChartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Real', data: alturaReal, borderWidth: 2, tension: 0.3, borderColor: '#2196f3',
        },
        {
          label: 'Prev. GetMare', data: alturaPrev, borderWidth: 2, tension: 0.3, borderColor: '#f44336', borderDash: [4, 2],
        },
        {
          label: 'Modelo', data: alturaPrevista, borderWidth: 2, tension: 0.3, borderColor: '#4caf50', borderDash: [1, 1],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { type: 'time', time: { unit: 'hour', tooltipFormat: 'dd/MM HH:mm' } },
        y: { title: { display: true, text: 'Altura (m)' } },
      },
    },
  });
}

function renderWindChart(data) {
  if (!windChartCanvas.value) return;
  if (windChartInstance) windChartInstance.destroy();

  const recent = data.slice(0, 24).reverse();
  const labels = recent.map((r) => r.timestamp_br);
  const dir = recent.map((r) => r.ventonum);

  windChartInstance = new Chart(windChartCanvas.value, {
    type: 'polarArea',
    data: {
      labels,
      datasets: [
        {
          label: 'Direção do Vento (°)',
          data: dir,
          backgroundColor: '#2196f3',
          borderColor: '#0d47a1',
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        r: {
          angleLines: { display: true },
          suggestedMin: 0,
          suggestedMax: 360,
          ticks: { stepSize: 90 },
        },
      },
      plugins: { legend: { display: false } },
    },
  });
}

onMounted(async () => {
  try {
    const response = await fetch(`/kevi/backend/get_table_mestre_hour_tratada_bq.php?limit=${rowsPerPage.value}`);
    const result = await response.json();

    if (result.success && result.data?.length) {
      const flattened = result.data.map(flattenDates);
      rows.value = flattened;
      columns.value = buildColumns(flattened[0]);

      const ordered = [...flattened].sort((a, b) => new Date(a.timestamp_br) - new Date(b.timestamp_br));
      renderTideChart(ordered);
      renderWindChart(ordered);
    } else {
      $q.notify({ type: 'warning', message: 'Nenhum dado retornado.' });
    }
  } catch (err) {
    $q.notify({ type: 'negative', message: 'Erro ao carregar dados.' });
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.q-table tbody td {
  padding: 4px 8px;
  font-size: 0.75rem;
}
</style>
