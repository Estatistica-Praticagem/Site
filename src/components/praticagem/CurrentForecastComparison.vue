<template>
  <q-card class="q-pa-md bg-white shadow-2 tide-compare-card">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-primary text-weight-bold">
        Comparação de Intensidade (1.5m x 3m x 6m)
      </div>
      <div class="row items-center q-gutter-sm">
        <!-- Radio para mostrar bolinhas -->
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
        <!-- Tipo de visualização -->
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
const { weatherHistory } = storeToRefs(store);

const viewType = ref('chart'); // chart (default) or table
const showPoints = ref(true); // bolinhas no gráfico

// Detecta se é mobile (mantém responsividade para outras configs se quiser)
const isMobile = ref(false);
const updateIsMobile = () => {
  isMobile.value = window.innerWidth < 700;
};
onMounted(() => {
  updateIsMobile();
  window.addEventListener('resize', updateIsMobile);
});
onUnmounted(() => window.removeEventListener('resize', updateIsMobile));

// Gráfico responsivo: diminui/tira bolinhas se mobile ou user desliga
const chartData = computed(() => {
  const dataset = (weatherHistory.value || []).slice().reverse();
  const pointRadius = showPoints.value ? (isMobile.value ? 1 : 2) : 0;
  const labels = dataset.map(
    (item) => item.timestamp_br?.date?.slice(11, 16)
      || item.timestamp_br?.slice(11, 16)
      || '',
  );
  return {
    labels,
    datasets: [
      {
        label: 'Intensidade 1.5m',
        data: dataset.map((item) => Number(item.intensidade_1_5m) || null),
        borderColor: '#1e78db',
        backgroundColor: '#1e78db',
        tension: 0.28,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Intensidade 3m',
        data: dataset.map((item) => Number(item.intensidade_3m) || null),
        borderColor: '#960d0d',
        backgroundColor: '#960d0d',
        tension: 0.35,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Intensidade 6m',
        data: dataset.map((item) => Number(item.intensidade_6m) || null),
        borderColor: '#FFDC66',
        backgroundColor: '#FFDC66',
        tension: 0.32,
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
    title: { display: false },
  },
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

const barChartOptions = computed(() => ({
  ...chartOptions.value,
  // Aqui você pode customizar opções do gráfico de barras se quiser
}));

// TABELA: só meia em meia hora
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
const tableRows = computed(() => (weatherHistory.value || [])
  .filter((item) => {
    const dt = item.timestamp_br?.date ?? item.timestamp_br;
    if (!dt) return false;
    const min = dt.slice(14, 16);
    return min === '00' || min === '30';
  })
  .map((item) => {
    const dt = item.timestamp_br?.date ?? item.timestamp_br ?? '';
    const hora = dt.slice(11, 16);
    return {
      hora,
      i15: item.intensidade_1_5m != null ? Number(item.intensidade_1_5m).toFixed(2) : '--',
      i3: item.intensidade_3m != null ? Number(item.intensidade_3m).toFixed(2) : '--',
      i6: item.intensidade_6m != null ? Number(item.intensidade_6m).toFixed(2) : '--',
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
