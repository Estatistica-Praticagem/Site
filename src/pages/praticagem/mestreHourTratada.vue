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

<div class="row q-col-gutter-md q-mb-lg">
  <div
    v-for="info in correntesInfo"
    :key="info.key"
    class="col-12 col-sm-6 col-md-4 col-lg-2"
  >
    <q-card class="current-card-gauge text-center">
      <q-card-section>
        <div class="text-caption text-grey-7 q-mb-xs">{{ info.label }}</div>
        <div>
          valor no JSON: <b>{{ rows.length ? rows[0][info.int] : '-' }}</b> m/s
        </div>
        <GaugeRelogio
          :value="parseFloat(rows.length ? rows[0][info.dir] : 0)"
          :intensidade="parseFloat(rows.length ? rows[0][info.int] : 0)"
          :max="maxIntensidade[info.key] || 3"
        />
        <div class="row justify-center items-center">
          <span class="text-bold q-mr-xs" style="font-size:1.2em">{{ parseFloat(rows.length ? rows[0][info.int] : 0).toFixed(2) }}</span>
          <span class="text-caption text-grey-7">m/s</span>
        </div>
        <div class="text-caption text-grey-6" style="font-size:.8em">
          Dir: <b>{{ parseFloat(rows.length ? rows[0][info.dir] : 0).toFixed(1) }}°</b>
        </div>
      </q-card-section>
    </q-card>
  </div>
</div>

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
    <!-- Chuva -->
    <div class="row q-col-gutter-lg q-mt-lg">
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Chuva (INMET)</div>
            <div class="text-caption q-mb-sm">
              Precipitação horária registrada em Rio Grande. Fundamental para avaliação das condições de segurança de operação e amarração.
            </div>
            <div class="chart-container chart-rain">
              <canvas ref="rainChartCanvas"></canvas>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <!-- Rosa dos ventos -->
      <div class="col-12 col-md-6">
        <q-card class="my-chart-card">
          <q-card-section>
            <div class="text-h6">Rosa dos Ventos — Direção e Intensidade Atuais</div>
            <div class="text-caption q-mb-sm">
              A seta indica a direção instantânea do vento (último registro). Cor e comprimento representam intensidade. Veja todos os quadrantes!
            </div>
            <div class="chart-container chart-rose">
              <WindRose
                :direction="parseFloat(last('ventonum'))"
                :intensidade="parseFloat(last('ventointensidade'))"
                :max="maxIntensidade.wind"
              />
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
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

import WindRose from 'components/praticagem/WindRose.vue';
import GaugeRelogio from 'components/praticagem/GaugeRelogio.vue';

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

// Dados de correnteza: todas as profundidades!
const correntesInfo = [
  {
    key: 'corr_15m', label: 'Correnteza 15m', dir: 'direcao_15m', int: 'intensidade_15m',
  },
  {
    key: 'corr_13_5m', label: 'Correnteza 13,5m', dir: 'direcao_13_5m', int: 'intensidade_13_5m',
  },
  {
    key: 'corr_12m', label: 'Correnteza 12m', dir: 'direcao_12m', int: 'intensidade_12m',
  },
  {
    key: 'corr_10_5m', label: 'Correnteza 10,5m', dir: 'direcao_10_5m', int: 'intensidade_10_5m',
  },
  {
    key: 'corr_9m', label: 'Correnteza 9m', dir: 'direcao_9m', int: 'intensidade_9m',
  },
  {
    key: 'corr_7_5m', label: 'Correnteza 7,5m', dir: 'direcao_7_5m', int: 'intensidade_7_5m',
  },
  {
    key: 'corr_6m', label: 'Correnteza 6m', dir: 'direcao_6m', int: 'intensidade_6m',
  },
  {
    key: 'corr_3m', label: 'Correnteza 3m', dir: 'direcao_3m', int: 'intensidade_3m',
  },
  {
    key: 'corr_1_5m', label: 'Correnteza 1,5m', dir: 'direcao_1_5m', int: 'intensidade_1_5m',
  },
  {
    key: 'corr_superficie', label: 'Correnteza Sup.', dir: 'direcao_superficie', int: 'intensidade_superficie',
  },
];
const maxIntensidade = {
  corr_15m: 2.5,
  corr_13_5m: 2.5,
  corr_12m: 2.5,
  corr_10_5m: 2.5,
  corr_9m: 2.5,
  corr_7_5m: 2.5,
  corr_6m: 2.5,
  corr_3m: 2.5,
  corr_1_5m: 2.5,
  corr_superficie: 2.5,
  wind: 10,
};

const rows = ref([]);
const columns = ref([]);
const loading = ref(true);

const rowsPerPageOptions = [10, 20, 50, 100, 0];
const rowsPerPage = ref(100);

const tideChartCanvas = ref(null);
const meteoChartCanvas = ref(null);
const windChartCanvas = ref(null);
const airChartCanvas = ref(null);
const rainChartCanvas = ref(null);

let tideChartInstance = null;
let meteoChartInstance = null;
let windChartInstance = null;
let airChartInstance = null;
let rainChartInstance = null;

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
  const labels = data.map((r) => format(new Date(r.timestamp_br), 'dd/MM HH:mm', { locale: ptBR }));
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
        x: {
          type: 'category',
          ticks: {
            callback: (val, idx, arr) => {
              const { label } = arr[idx];
              if (!label) return '';
              const showMinutes = arr.length <= 48;
              return showMinutes ? label : `${label.slice(0, 5)}...`;
            },
            maxRotation: 0,
            autoSkip: true,
            maxTicksLimit: 16,
          },
          title: { display: true, text: 'Hora' },
        },
        y: { title: { display: true, text: 'Altura (m)' } },
      },
    },
  });
}

// --- Gráfico Temperatura/Umidade/Pressão ---
function renderMeteoChart(data) {
  if (!meteoChartCanvas.value) return;
  if (meteoChartInstance) meteoChartInstance.destroy();
  const labels = data.map((r) => format(new Date(r.timestamp_br), 'dd/MM HH:mm', { locale: ptBR }));
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
        x: {
          type: 'category',
          ticks: {
            callback: (val, idx, arr) => {
              const { label } = arr[idx];
              if (!label) return '';
              const showMinutes = arr.length <= 48;
              return showMinutes ? label : `${label.slice(0, 5)}...`;
            },
            maxRotation: 0,
            autoSkip: true,
            maxTicksLimit: 16,
          },
          title: { display: true, text: 'Hora' },
        },
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
  const labels = data.map((r) => format(new Date(r.timestamp_br), 'dd/MM HH:mm', { locale: ptBR }));
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
        x: {
          type: 'category',
          ticks: {
            callback: (val, idx, arr) => {
              const { label } = arr[idx];
              if (!label) return '';
              const showMinutes = arr.length <= 48;
              return showMinutes ? label : `${label.slice(0, 5)}...`;
            },
            maxRotation: 0,
            autoSkip: true,
            maxTicksLimit: 16,
          },
          title: { display: true, text: 'Hora' },
        },
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
  const labels = data.map((r) => format(new Date(r.timestamp_br), 'dd/MM HH:mm', { locale: ptBR }));
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

// --- Gráfico de Chuva ---
function renderRainChart(data) {
  if (!rainChartCanvas.value) return;
  if (rainChartInstance) rainChartInstance.destroy();
  const labels = data.map((r) => format(new Date(r.timestamp_br), 'dd/MM HH:mm', { locale: ptBR }));
  rainChartInstance = new Chart(rainChartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Chuva (mm/h)',
          data: data.map((r) => r.chuva_inmet),
          backgroundColor: '#1565c0b8',
          borderRadius: 2,
        },
      ],
    },
    options: {
      plugins: { legend: { position: 'bottom' }, tooltip: { enabled: true } },
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          type: 'category',
          ticks: {
            callback: (val, idx, arr) => {
              const { label } = arr[idx];
              if (!label) return '';
              const showMinutes = arr.length <= 48;
              return showMinutes ? label : `${label.slice(0, 5)}...`;
            },
            maxRotation: 0,
            autoSkip: true,
            maxTicksLimit: 16,
          },
          title: { display: true, text: 'Hora' },
        },
        y: { beginAtZero: true, title: { display: true, text: 'mm/h' } },
      },
    },
  });
}

onMounted(async () => {
  try {
    const response = await fetch(`/kevi/backend/praticagem/get_table_mestre_hour_tratada_bq.php?limit=${rowsPerPage.value}`);
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
      renderRainChart(ordered);
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
  min-height: 150px;
  max-height: 420px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.chart-tide    { height: 340px; max-width: 98%; }
.chart-meteo   { height: 380px; max-width: 98%; }
.chart-wind    { height: 380px; max-width: 98%; }
.chart-air     { height: 300px; max-width: 98%; }
.chart-rain    { height: 240px; max-width: 98%; }
.chart-rose    { height: 260px; max-width: 98%; }
@media (max-width: 900px) {
  .chart-container { height: 120px; }
  .chart-tide, .chart-meteo, .chart-wind, .chart-air, .chart-rain, .chart-rose { height: 120px; }
}
.current-card-gauge {
  background: #f9fafc;
  border-radius: 16px;
  box-shadow: 0 2px 8px 0 #bcd6e222;
  margin-bottom: 12px;
}
.gauge-svg { display: block; margin: auto; }
.rose-svg { display: block; margin: auto; }
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
