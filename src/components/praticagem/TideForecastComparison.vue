<template>
  <q-card class="q-pa-md bg-white shadow-2 tide-compare-card">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-primary text-weight-bold">
        Comparação de Maré (Marinha x Medida x GA)
      </div>
      <div class="row items-center q-gutter-sm">
        <q-option-group
          v-model="showPoints"
          :options="[
            { label: 'Com bolinhas', value: true },
            { label: 'Sem bolinhas', value: false }
          ]"
          type="radio"
          color="primary"
          inline
        />
        <q-btn-toggle
          v-model="viewType"
          spread
          toggle-color="primary"
          :options="[
            { label: 'Gráfico Linha', value: 'chart', icon: 'show_chart' },
            { label: 'Gráfico Barras', value: 'bar', icon: 'bar_chart' },
            { label: 'Tabela', value: 'table', icon: 'table_chart' }
          ]"
          size="sm"
          class="tide-toggle"
        />
      </div>
    </div>

    <div v-if="viewType === 'chart'" class="tide-chart-container">
      <Line :data="chartData" :options="chartOptions" :height="320"/>
    </div>
    <div v-else-if="viewType === 'bar'" class="tide-chart-container">
      <Bar :data="chartData" :options="barChartOptions" :height="320"/>
    </div>
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
  ref, computed, onMounted, onUnmounted,
} from 'vue';
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import { Line, Bar } from 'vue-chartjs';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend);

const store = useWeatherStore();
const { weatherForecast } = storeToRefs(store);

const viewType = ref('chart');
const showPoints = ref(true);

const isMobile = ref(window.innerWidth < 700);
const updateMobile = () => { isMobile.value = window.innerWidth < 700; };

onMounted(() => {
  window.addEventListener('resize', updateMobile);
  store.fetchForecast();
});
onUnmounted(() => window.removeEventListener('resize', updateMobile));

const chartData = computed(() => {
  // Exibe do mais antigo para o mais recente
  const dataset = (weatherForecast.value || [])
    .slice() // faz uma cópia
    .filter((item) => item.timestamp_prev && item.altura_prevista != null)
    .reverse(); // exibe do mais antigo pro mais recente

  const pointRadius = showPoints.value ? (isMobile.value ? 1 : 2) : 0;
  const labels = dataset.map(
    (item) => item.timestamp_prev?.date?.slice(11, 16) || '',
  );
  return {
    labels,
    datasets: [
      {
        label: 'Marinha',
        // eslint-disable-next-line no-restricted-globals
        data: dataset.map((item) => (typeof item.altura_prev_getmare === 'number' && !isNaN(item.altura_prev_getmare)
          ? item.altura_prev_getmare
          : null)),
        borderColor: '#960d0d',
        backgroundColor: '#960d0d',
        tension: 0.35,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Maré Medida',
        // eslint-disable-next-line no-restricted-globals
        data: dataset.map((item) => (typeof item.altura_real_getmare === 'number' && !isNaN(item.altura_real_getmare)
          ? item.altura_real_getmare
          : null)),
        borderColor: '#FFDC66',
        backgroundColor: '#FFDC66',
        tension: 0.32,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Previsão GA',
        // eslint-disable-next-line no-restricted-globals
        data: dataset.map((item) => (typeof item.altura_prevista === 'number' && !isNaN(item.altura_prevista)
          ? item.altura_prevista
          : null)),
        borderColor: '#1e78db',
        backgroundColor: '#1e78db',
        tension: 0.28,
        pointRadius,
        borderWidth: 2,
      },
    ],
  };
});

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: { boxWidth: 16, font: { size: 15 } },
    },
    title: {
      display: false,
    },
  },
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
}));

const barChartOptions = computed(() => ({
  ...chartOptions.value,
}));

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

const tableRows = computed(() => (weatherForecast.value || [])
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
