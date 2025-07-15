<template>
  <q-card class="q-pa-md bg-white shadow-2 tide-compare-card">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-primary text-weight-bold">
        Comparação de Maré (Marinha x Medida x GA)
      </div>
      <q-btn-toggle
        v-model="viewType"
        spread
        toggle-color="primary"
        :options="[
          { label: 'Gráfico', value: 'chart', icon: 'show_chart' },
          { label: 'Tabela', value: 'table', icon: 'table_chart' }
        ]"
        size="sm"
        class="tide-toggle"
      />
    </div>

    <div v-if="viewType === 'chart'" class="tide-chart-container">
      <Line :data="chartData" :options="chartOptions" :height="320"/>
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
import { Line } from 'vue-chartjs';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const store = useWeatherStore();
const { weatherHistory } = storeToRefs(store);

const viewType = ref('chart');

// -- Detecta mobile dinamicamente
const isMobile = ref(window.innerWidth < 700);
const updateMobile = () => { isMobile.value = window.innerWidth < 700; };
onMounted(() => {
  window.addEventListener('resize', updateMobile);
});
onUnmounted(() => {
  window.removeEventListener('resize', updateMobile);
});

// Gráfico
const chartData = computed(() => {
  const dataset = weatherHistory.value || [];
  const labels = dataset.map(
    (item) => item.timestamp_prev?.date?.slice(11, 16)
      || item.timestamp_prev?.slice(11, 16)
      || '',
  );
  const pointRadius = isMobile.value ? 0 : 2;
  return {
    labels,
    datasets: [
      {
        label: 'Marinha',
        data: dataset.map((item) => Number(item.altura_prev_getmare) || null),
        borderColor: '#960d0d',
        backgroundColor: '#960d0d',
        tension: 0.35,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Maré Medida',
        data: dataset.map((item) => Number(item.altura_real_getmare) || null),
        borderColor: '#FFDC66',
        backgroundColor: '#FFDC66',
        tension: 0.32,
        pointRadius,
        borderWidth: 2,
      },
      {
        label: 'Previsão GA',
        data: dataset.map((item) => Number(item.altura_prevista) || null),
        borderColor: '#1e78db',
        backgroundColor: '#1e78db',
        tension: 0.28,
        pointRadius,
        borderWidth: 2,
      },
    ],
  };
});

const chartOptions = {
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
};

// TABELA: mostra só de meia em meia hora
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
const tableRows = computed(() => (weatherHistory.value || [])
  .filter((item) => {
    // Filtra meia em meia hora
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
  min-width: 160px;
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
    min-width: 80px;
  }
}
</style>
