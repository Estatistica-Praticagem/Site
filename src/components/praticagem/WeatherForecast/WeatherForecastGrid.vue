<template>
  <div class="forecast-grid-bg rounded-xl shadow-xl p-3 overflow-x-auto">
    <div class="flex items-center gap-2 mb-2">
      <q-icon name="waves" class="text-blue-8" />
      <span class="text-lg font-bold">{{ cityLabel }} — Grade de Previsão</span>
    </div>
    <!-- Seletor de dia -->
    <div class="flex gap-2 mb-2">
      <q-btn
        v-for="dia in availableDays"
        :key="dia"
        size="sm"
        :label="formatDayShort(dia)"
        :color="selectedDay === dia ? 'primary' : 'white'"
        :text-color="selectedDay === dia ? 'white' : 'primary'"
        flat
        rounded
        class="shadow"
        @click="emitDay(dia)"
      />
    </div>
    <div class="overflow-x-auto grid-table-wrap">
      <table class="forecast-grid-table w-full">
        <thead>
          <tr>
            <th class="left-th bg-transparent border-none"></th>
            <th v-for="item in hoursList" :key="item.dt_txt" class="text-center th-hour">
              {{ item.dt_txt.slice(11, 16) }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in variables" :key="row.key">
            <td class="left-th label-cell font-semibold">
              <q-icon :name="row.icon" class="mr-1" :color="row.color" size="18px"/>
              {{ row.label }}
            </td>
            <td v-for="item in hoursList" :key="item.dt_txt" class="cell-val" :class="row.bg ? row.bg(item) : ''">
              <template v-if="row.renderComp">
                <component :is="row.renderComp" :deg="item.wind_deg" :speed="item.wind_speed" />
              </template>
              <template v-else-if="row.renderFn">
                <span v-html="row.renderFn(item)"></span>
              </template>
              <template v-else>
                {{ row.field ? item[row.field] : '--' }}
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue';
import WindMiniClock from 'src/components/praticagem/WeatherForecast/WindMiniClock.vue';

const props = defineProps({
  day: String,
  groupedByDay: Object,
  cityLabel: String,
  availableDays: Array,
});

const emit = defineEmits(['update:day']);
const selectedDay = computed(() => props.day);
const hoursList = computed(() => props.groupedByDay[props.day] || []);
function emitDay(dia) { emit('update:day', dia); }

function formatDayShort(dia) {
  const d = new Date(dia);
  // eslint-disable-next-line no-restricted-globals
  if (isNaN(d)) return dia;
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}`;
}

// Renderização dos relógios igual aos outros componentes!
const variables = [
  {
    key: 'vento_dir',
    label: 'Direção do vento',
    icon: 'explore',
    color: 'blue-7',
    // Passa as props que o componente espera
    renderComp: WindMiniClock,
  },
  {
    key: 'vento_vel',
    label: 'Velocidade do vento (km/h)',
    icon: 'air',
    color: 'indigo',
    field: 'wind_speed',
    bg: (item) => (item.wind_speed >= 30 ? 'bg-red-1' : item.wind_speed >= 15 ? 'bg-yellow-1' : ''),
  },
  {
    key: 'gust',
    label: 'Rajadas (km/h)',
    icon: 'whatshot',
    color: 'orange',
    field: 'wind_gust',
  },
  {
    key: 'temp',
    label: 'Temperatura (°C)',
    icon: 'thermostat',
    color: 'deep-orange',
    field: 'temp',
    bg: (item) => (item.temp >= 28 ? 'temp-hot' : item.temp <= 12 ? 'temp-cold' : ''),
  },
  {
    key: 'feels',
    label: 'Sensação (°C)',
    icon: 'thermostat',
    color: 'grey',
    field: 'feels_like',
  },
  {
    key: 'nuvens',
    label: 'Nuvens (%)',
    icon: 'filter_drama',
    color: 'blue',
    field: 'clouds_all',
  },
  {
    key: 'pop',
    label: 'Chuva (%)',
    icon: 'water_drop',
    color: 'blue-7',
    renderFn: (item) => {
      const perc = typeof item.pop === 'number' ? `${Math.round(item.pop * 100)}%` : '--';
      const cls = item.pop >= 0.7 ? 'text-red-7' : item.pop >= 0.3 ? 'text-blue-7' : 'text-grey-7';
      const mm = item.rain_3h ? ` <span class="ml-1 text-grey-7">(${item.rain_3h}mm)</span>` : '';
      return `<span class="${cls}">${perc}</span>${mm}`;
    },
    bg: (item) => (item.pop >= 0.7 ? 'rain-red' : item.pop >= 0.3 ? 'rain-blue' : ''),
  },
  {
    key: 'pressure',
    label: 'Pressão (hPa)',
    icon: 'compress',
    color: 'grey',
    field: 'pressure',
  },
  {
    key: 'humidity',
    label: 'Umidade (%)',
    icon: 'water',
    color: 'blue',
    field: 'humidity',
  },
];
</script>

<style scoped>
.forecast-grid-bg {
  background: linear-gradient(180deg, #bbdeff 0%, #e8f4fd 100%);
  border-radius: 22px;
}
.grid-table-wrap {
  overflow-x: auto;
}
.forecast-grid-table {
  min-width: 720px;
  border-collapse: separate;
  border-spacing: 0;
  background: rgba(255,255,255,0.94);
  border-radius: 16px;
  margin-top: 2px;
  font-size: 0.99em;
}
.forecast-grid-table th,
.forecast-grid-table td {
  padding: 8px 7px;
  text-align: center;
  border-bottom: 1px solid #e3eefc;
}
.forecast-grid-table th.th-hour {
  background: #cbe7fd;
  color: #14539b;
  font-weight: 600;
  font-size: 1.01em;
}
.forecast-grid-table .left-th {
  text-align: left;
  background: #e8f4fd;
  font-size: 0.98em;
  color: #1666b7;
  width: 135px;
  min-width: 135px;
  border-right: 1.5px solid #e3eefc;
  padding-left: 10px;
}
.temp-hot { background: #ffe0b2 !important; }
.temp-cold { background: #e3f2fd !important; }
.rain-red { background: #ffeaea !important; }
.rain-blue { background: #e6f5ff !important; }
.bg-yellow-1 { background: #fff9c4 !important; }
.bg-red-1 { background: #ffebee !important; }
</style>
