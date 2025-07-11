<template>
  <q-page class="q-pa-md">
    <!-- Título e controle de limite -->
    <div class="row items-center justify-between q-mb-md">
      <div>
        <div class="text-h4 q-mb-xs">Monitoramento • Maré & Meteorologia</div>
        <div class="text-subtitle2 text-grey-7">
          Últimos registros de condições no Porto — Dados de maré, vento, clima, sensores locais e previsão, organizados para facilitar a manobra e segurança dos navios.
        </div>
      </div>
      <div>
        <q-select
          v-model="rowsPerPage"
          :options="rowsPerPageOptions"
          label="Linhas por página"
          dense outlined
          style="min-width:140px"
        />
      </div>
    </div>

    <!-- Painel de Resumo rápido -->
    <q-card flat class="bg-grey-1 q-mb-lg">
      <q-card-section class="row q-col-gutter-md items-center">
        <div class="col">
          <div class="text-caption text-grey-7">Maré Real (última)</div>
          <div class="text-h5">{{ last('altura_real_getmare') }} m</div>
        </div>
        <div class="col">
          <div class="text-caption text-grey-7">Vento (°/m/s)</div>
          <div class="text-h5">{{ last('ventonum') }}° / {{ last('ventointensidade') }} m/s</div>
        </div>
        <div class="col">
          <div class="text-caption text-grey-7">Temp./Umidade</div>
          <div class="text-h5">{{ last('temperatura') }} °C / {{ last('umidade') }}%</div>
        </div>
        <div class="col">
          <div class="text-caption text-grey-7">Pressão</div>
          <div class="text-h5">{{ last('pressao') }} hPa</div>
        </div>
        <div class="col">
          <div class="text-caption text-grey-7">Status</div>
          <q-badge color="green" v-if="last('status')==='PRATICAVEL'">Praticável</q-badge>
          <q-badge color="orange" v-else>{{ last('status') }}</q-badge>
        </div>
      </q-card-section>
    </q-card>

    <!-- Cards de Gráficos -->
    <div class="row q-col-gutter-lg">
      <!-- Maré -->
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Evolução da Altura da Maré</div>
            <div class="text-caption q-mb-sm">
              Altura da maré real, prevista (API GetMare) e modelo local, com escala em metros. Use para planejar a aproximação segura.
            </div>
            <div class="chart-container chart-tide">
              <canvas ref="tideChartCanvas"></canvas>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Temperatura, Umidade e Pressão -->
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Clima — Temperatura, Umidade e Pressão</div>
            <div class="text-caption q-mb-sm">
              Variação de temperatura (°C), umidade (%) e pressão (hPa) nas últimas medições.
            </div>
            <div class="chart-container chart-meteo">
              <canvas ref="meteoChartCanvas"></canvas>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-lg q-mt-lg">
      <!-- Vento Intensidade e Direção -->
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Vento — Intensidade e Direção</div>
            <div class="text-caption q-mb-sm">
              Intensidade média e direção do vento (em graus) — importante para manobras em condições desafiadoras.
            </div>
            <div class="chart-container chart-wind">
              <canvas ref="windChartCanvas"></canvas>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Qualidade do Ar -->
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Qualidade do Ar</div>
            <div class="text-caption q-mb-sm">
              Monitoramento dos principais poluentes atmosféricos.
            </div>
            <div class="chart-container chart-air">
              <canvas ref="airChartCanvas"></canvas>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Tabela (com busca, scroll, compacta) -->
    <q-card class="q-mt-xl">
      <q-card-section>
        <div class="text-h6">Todos os Registros — Planilha Completa</div>
        <div class="text-caption text-grey-7">Inclui medições, previsão e status de cada registro. Você pode rolar, buscar e baixar CSV.</div>
      </q-card-section>
      <div style="overflow-x: auto;">
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
          :loading="loading"
          style="min-width:1200px"
          virtual-scroll
        >
          <template v-slot:loading>
            <q-inner-loading showing color="primary" />
          </template>
        </q-table>
      </div>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import {
  Chart,
  LineController, LineElement, PointElement, BarController, BarElement,
  CategoryScale, LinearScale, TimeScale, RadialLinearScale, Filler, Tooltip, Legend,
  PolarAreaController, ArcElement,
} from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(
  LineController,
  LineElement,
  PointElement,
  BarController,
  BarElement,
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

const tideChartCanvas = ref(null);
const meteoChartCanvas = ref(null);
const windChartCanvas = ref(null);
const airChartCanvas = ref(null);

let tideChartInstance = null;
let meteoChartInstance = null;
let windChartInstance = null;
let airChartInstance = null;

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

// Helper para o painel de resumo rápido
function last(field) {
  if (!rows.value.length) return '--';
  const v = rows.value[0][field];
  if (typeof v === 'number') return v.toLocaleString('pt-BR', { maximumFractionDigits: 2 });
  return v || '--';
}

// --- Gráfico de Maré ---
function renderTideChart(data) {
  if (!tideChartCanvas.value) return;
  if (tideChartInstance) tideChartInstance.destroy();
  const labels = data.map((r) => r.timestamp_br);
  tideChartInstance = new Chart(tideChartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Altura Real',
          data: data.map((r) => r.altura_real_getmare),
          borderWidth: 2,
          tension: 0.3,
          borderColor: '#2196f3',
          pointRadius: 0,
          fill: false,
        },
        {
          label: 'Prev. GetMare',
          data: data.map((r) => r.altura_prev_getmare),
          borderWidth: 2,
          tension: 0.3,
          borderColor: '#f44336',
          borderDash: [6, 4],
          pointRadius: 0,
          fill: false,
        },
        {
          label: 'Modelo',
          data: data.map((r) => (typeof r.altura_prevista === 'number' ? r.altura_prevista : null)),
          borderWidth: 1,
          tension: 0.3,
          borderColor: '#43a047',
          borderDash: [2, 2],
          pointRadius: 0,
          fill: false,
        },
      ],
    },
    options: {
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { type: 'time', time: { unit: 'hour', tooltipFormat: 'dd/MM HH:mm' }, title: { display: true, text: 'Hora' } },
        y: { title: { display: true, text: 'Altura (m)' } },
      },
    },
  });
}

// --- Gráfico Temperatura/Umidade/Pressão ---
function renderMeteoChart(data) {
  if (!meteoChartCanvas.value) return;
  if (meteoChartInstance) meteoChartInstance.destroy();
  const labels = data.map((r) => r.timestamp_br);
  meteoChartInstance = new Chart(meteoChartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Temperatura (°C)', data: data.map((r) => r.temperatura), borderColor: '#ffa000', backgroundColor: '#ffe08266', fill: true, tension: 0.35, yAxisID: 'y1',
        },
        {
          label: 'Umidade (%)', data: data.map((r) => r.umidade), borderColor: '#0288d1', backgroundColor: '#b3e5fc77', fill: true, tension: 0.35, yAxisID: 'y2',
        },
        {
          label: 'Pressão (hPa)', data: data.map((r) => r.pressao), borderColor: '#388e3c', backgroundColor: '#c8e6c977', fill: true, tension: 0.25, yAxisID: 'y3',
        },
      ],
    },
    options: {
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { type: 'time', time: { unit: 'hour', tooltipFormat: 'dd/MM HH:mm' }, title: { display: true, text: 'Hora' } },
        y1: { position: 'left', title: { display: true, text: 'Temperatura' }, grid: { drawOnChartArea: false } },
        y2: { position: 'right', title: { display: true, text: 'Umidade' }, grid: { drawOnChartArea: false } },
        y3: { position: 'right', title: { display: true, text: 'Pressão' }, grid: { drawOnChartArea: false } },
      },
    },
  });
}

// --- Gráfico Vento Intensidade/Direção ---
function renderWindChart(data) {
  if (!windChartCanvas.value) return;
  if (windChartInstance) windChartInstance.destroy();
  const labels = data.map((r) => r.timestamp_br);
  windChartInstance = new Chart(windChartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Intensidade (m/s)',
          data: data.map((r) => r.ventointensidade),
          backgroundColor: '#2196f3b0',
          borderRadius: 2,
        },
        {
          label: 'Direção (°)',
          data: data.map((r) => r.ventonum),
          type: 'line',
          borderColor: '#ff7043',
          tension: 0.25,
          fill: false,
          yAxisID: 'y2',
        },
      ],
    },
    options: {
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { title: { display: true, text: 'Hora' } },
        y: { beginAtZero: true, title: { display: true, text: 'Intensidade (m/s)' } },
        y2: {
          position: 'right', beginAtZero: true, title: { display: true, text: 'Direção (°)' }, grid: { drawOnChartArea: false },
        },
      },
    },
  });
}

// --- Gráfico de Qualidade do Ar ---
function renderAirChart(data) {
  if (!airChartCanvas.value) return;
  if (airChartInstance) airChartInstance.destroy();
  const labels = data.map((r) => r.timestamp_br);
  airChartInstance = new Chart(airChartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label: 'PM2.5', data: data.map((r) => r.air_pm2_5), backgroundColor: '#8d6e63' },
        { label: 'PM10', data: data.map((r) => r.air_pm10), backgroundColor: '#bdbdbd' },
        { label: 'CO', data: data.map((r) => r.air_co), backgroundColor: '#ffb300' },
        { label: 'NO₂', data: data.map((r) => r.air_no2), backgroundColor: '#e53935' },
        { label: 'O₃', data: data.map((r) => r.air_o3), backgroundColor: '#43a047' },
      ],
    },
    options: {
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      responsive: true,
      maintainAspectRatio: false,
      scales: { x: { title: { display: true, text: 'Hora' } }, y: { beginAtZero: true, title: { display: true, text: 'Concentração' } } },
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
      renderMeteoChart(ordered);
      renderWindChart(ordered);
      renderAirChart(ordered);
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
.my-chart-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 1px 8px 0 #e0e0e033;
}
.chart-container {
  width: 100%;
  height: 240px;        /* altura padrão para todos */
  min-height: 120px;
  max-height: 320px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.chart-tide    { height: 300px; max-width: 98%; }
.chart-meteo   { height: 400px; max-width: 98%; }
.chart-wind    { height: 400px; max-width: 98%; }
.chart-air     { height: 300px; max-width: 98%; }
/* Responsividade para mobile */
@media (max-width: 900px) {
  .chart-container { height: 160px; }
  .chart-tide, .chart-meteo, .chart-wind, .chart-air { height: 140px; }
}
.q-table tbody td {
  padding: 4px 8px;
  font-size: 0.85rem;
}
.q-table thead th {
  background: #f3f7fa;
  font-weight: 600;
  font-size: 0.90rem;
  letter-spacing: .03em;
}
.q-card-section {
  padding-bottom: 18px !important;
}
.text-h6 { margin-bottom: 4px; }
</style>
