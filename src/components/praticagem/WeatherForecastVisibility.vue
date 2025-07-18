<template>
  <div>
    <!-- Toggle para modo de exibição -->
    <div class="q-mb-md flex items-center gap-3 justify-between">
      <q-btn-toggle
        v-model="mode"
        dense
        toggle-color="primary"
        color="white"
        text-color="primary"
        unelevated
        :options="[
          { label: 'Compacto', value: 'compact' },
          { label: 'Detalhado', value: 'detailed' }
        ]"
      />
      <!-- Se detalhado, exibe select de dia -->
      <q-select
        v-if="mode === 'detailed'"
        v-model="selectedDay"
        :options="availableDays"
        emit-value
        map-options
        label="Dia"
        dense
        outlined
        style="min-width:150px;"
      />
    </div>

    <!-- VISÃO COMPACTA (cards dos 5 dias) -->
    <div v-if="mode === 'compact'" class="flex flex-wrap gap-3 justify-center">
      <div
        v-for="day in days"
        :key="day.date"
        class="forecast-card flex-1 min-w-[210px] max-w-xs rounded-2xl shadow-xl px-5 py-4 flex flex-col items-center"
        :class="day.phenomenon.color"
      >
        <div class="flex items-center gap-2 mb-2">
          <span class="text-3xl" v-html="day.phenomenon.svgIcon"></span>
          <span class="font-bold text-lg">{{ day.phenomenon.name }}</span>
        </div>
        <div class="font-mono text-xs text-gray-700 mb-2">
          {{ day.dateLabel }}
        </div>
        <div class="grid grid-cols-2 gap-x-4 gap-y-1 w-full text-center text-base">
          <div>
            <span class="block text-xs text-gray-600">Máxima</span>
            <span class="font-semibold text-red-7">{{ day.temp_max }}°</span>
          </div>
          <div>
            <span class="block text-xs text-gray-600">Mínima</span>
            <span class="font-semibold text-blue-8">{{ day.temp_min }}°</span>
          </div>
          <div>
            <span class="block text-xs text-gray-600">Umidade média</span>
            <span class="font-semibold text-blue-7">{{ day.humidity_avg }}%</span>
          </div>
          <div>
            <span class="block text-xs text-gray-600">Prob. Nevoeiro</span>
            <span class="font-semibold" :class="getFogProbColor(day.fogProb)">
              {{ day.fogProb }}%
            </span>
          </div>
        </div>
        <div class="mt-2 text-xs">
          <q-tooltip anchor="bottom middle">
            Código dominante: <b>{{ day.weather_id }}</b>
          </q-tooltip>
          <span class="bg-white/40 px-2 py-1 rounded text-gray-800">#{{ day.weather_id }}</span>
        </div>
      </div>
    </div>

    <!-- VISÃO DETALHADA (tabela horária do dia escolhido) -->
    <div v-else-if="mode === 'detailed'">
      <div class="mb-2 text-lg font-bold text-primary">
        {{ labelDiaSelecionado }}
      </div>
      <q-table
        dense
        flat
        :rows="hourlyRows"
        :columns="columns"
        row-key="dt_txt"
        hide-bottom
        no-data-label="Nenhum dado disponível"
        class="bg-white/95 rounded-xl shadow-xl overflow-x-auto"
        style="font-size: 1em;"
      >
        <template #body="props">
          <q-tr :props="props" :class="rainRowColor(props.row)">
            <q-td key="hour" class="flex items-center gap-2">
              <q-icon name="access_time" color="grey-7" size="18px"/>
              {{ props.row.dt_txt?.slice(11, 16) }}
            </q-td>
            <q-td key="phenomenon">
              <q-tooltip>
                {{ phenomenonInfo(props.row).name }}<br>Código: {{ props.row.weather_id }}
              </q-tooltip>
              <div class="flex items-center gap-2 justify-center rounded-xl px-2 py-1"
                :class="phenomenonInfo(props.row).color"
                style="min-width:90px;">
                <span class="text-xl" v-html="phenomenonInfo(props.row).svgIcon"></span>
                <span class="font-bold">{{ phenomenonInfo(props.row).name }}</span>
                <span class="text-xs ml-1 px-1 py-0.5 rounded bg-white/30 text-grey-8 border font-mono">
                  {{ props.row.weather_id }}
                </span>
              </div>
            </q-td>
            <q-td key="fogprob">
              <div class="flex items-center gap-1 justify-center">
                <q-icon name="waves" color="purple-7" size="18px" />
                <span :class="getFogProbColor(fogProb(props.row)) + ' font-bold'">
                  {{ fogProb(props.row) }}%
                </span>
              </div>
            </q-td>
            <q-td key="temp">
              <span class="text-lg font-bold">{{ props.row.temp?.toFixed(0) }}°</span>
              <span class="text-grey-6 text-xs block">Sens. {{ props.row.feels_like?.toFixed(0) }}°</span>
            </q-td>
            <q-td key="desc">
              <span style="text-transform: capitalize">{{ props.row.weather_description }}</span>
            </q-td>
            <q-td key="vento">
              <span class="flex items-center gap-2">
                <WindMiniClock :deg="props.row.wind_deg" :speed="props.row.wind_speed" />
                {{ windDir(props.row.wind_deg) }}
                <span class="text-blue-8 text-weight-medium">{{ props.row.wind_speed }} km/h</span>
              </span>
            </q-td>
            <q-td key="prob">
              <span v-if="typeof props.row.pop === 'number'">
                <q-icon name="water_drop" color="blue-6" size="18px"/>
                <span :class="props.row.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
                  {{ Math.round(props.row.pop * 100) }}%
                </span>
                <span v-if="props.row.rain_3h" class="text-grey-7">({{ props.row.rain_3h }}mm)</span>
              </span>
              <span v-else>--</span>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </div>
</template>

<script setup>

// -------- IMPORTS --------
import { ref, computed, watchEffect } from 'vue';
import WindMiniClock from 'src/components/praticagem/WeatherForecast/WindMiniClock.vue';
import { useWeatherStore } from 'src/stores/weather';

// -------- PINIA STORE --------
const store = useWeatherStore();
// Para garantir compatibilidade com suas funções, use 'openWeatherForecast'!
const forecast = computed(() => store.openWeatherForecast || []);

// -------- MODO DE VISUALIZAÇÃO --------
const mode = ref('compact');

// -------- MAPA DE FENÔMENOS DO CLIMA --------
const weatherPhenomena = {
  800: { name: 'Céu limpo', svgIcon: '☀️', color: 'bg-blue-1 text-blue-10' },
  801: { name: 'Poucas nuvens', svgIcon: '🌤️', color: 'bg-blue-2 text-blue-10' },
  802: { name: 'Nuvens dispersas', svgIcon: '⛅', color: 'bg-blue-3 text-blue-10' },
  // eslint-disable-next-line no-use-before-define
  803: { name: 'Nuvens fragmentadas', svgIcon: sunBehindCloudsSVG(), color: 'bg-gray-3 text-gray-9' },
  // eslint-disable-next-line no-use-before-define
  804: { name: 'Céu encoberto', svgIcon: cloudSVG(), color: 'bg-gray-6 text-white' },
  // eslint-disable-next-line no-use-before-define
  741: { name: 'Nevoeiro', svgIcon: fogSVG(), color: 'bg-gray-5 text-white' },
  // eslint-disable-next-line no-use-before-define
  701: { name: 'Névoa', svgIcon: mistSVG(), color: 'bg-gray-2 text-gray-9' },
  // eslint-disable-next-line no-use-before-define
  721: { name: 'Neblina seca', svgIcon: hazeSVG(), color: 'bg-yellow-2 text-yellow-9' },
  // eslint-disable-next-line no-use-before-define
  711: { name: 'Fumaça', svgIcon: fireSVG(), color: 'bg-yellow-4 text-yellow-10' },
};
function phenomenonInfo(row) {
  return weatherPhenomena[row.weather_id] || { name: 'Desconhecido', svgIcon: '❓', color: 'bg-white text-grey-9' };
}

// -------- AGRUPA POR DIA PARA OS CARDS --------
const days = computed(() => {
  const byDate = {};
  forecast.value.forEach((row) => {
    const day = row.dt_txt?.slice(0, 10);
    if (!day) return;
    byDate[day] = byDate[day] || [];
    byDate[day].push(row);
  });
  return Object.entries(byDate)
    .slice(0, 5)
    .map(([date, arr]) => {
      const dominant = arr.map((r) => r.weather_id)
        .reduce((acc, v) => { acc[v] = (acc[v] || 0) + 1; return acc; }, {});
        // eslint-disable-next-line no-use-before-define, camelcase
      const [weather_id] = Object.entries(dominant).sort((a, b) => b[1] - a[1])[0] || [800];
      // eslint-disable-next-line no-use-before-define, camelcase
      const phenomenon = weatherPhenomena[weather_id] || { name: 'Desconhecido', svgIcon: '❓', color: 'bg-white text-grey-9' };
      // eslint-disable-next-line no-use-before-define, camelcase
      const temp_max = Math.max(...arr.map((x) => Number(x.temp_max) || 0));
      // eslint-disable-next-line no-use-before-define, camelcase
      const temp_min = Math.min(...arr.map((x) => Number(x.temp_min) || 0));
      // eslint-disable-next-line no-use-before-define, camelcase
      const humidity_avg = Math.round(arr.reduce((s, x) => s + (Number(x.humidity) || 0), 0) / arr.length);
      // eslint-disable-next-line no-use-before-define, camelcase, no-shadow
      const fogProb = getDayFogProb(arr);
      const dt = new Date(`${date}T12:00:00`);
      const dateLabel = dt.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: '2-digit' });
      return {
        // eslint-disable-next-line camelcase
        date, dateLabel, weather_id, phenomenon, temp_max, temp_min, humidity_avg, fogProb,
      };
    });
});

// -------- SELEÇÃO DE DIA (DETALHADO) --------
const availableDays = computed(() => days.value.map((d) => ({
  label: d.dateLabel,
  value: d.date,
})));
const selectedDay = ref('');
const hourlyRows = computed(() => forecast.value.filter((row) => row.dt_txt?.slice(0, 10) === selectedDay.value));
const labelDiaSelecionado = computed(() => {
  const d = availableDays.value.find((x) => x.value === selectedDay.value);
  return d ? `Previsão horária para ${d.label}` : '';
});
watchEffect(() => {
  if (!selectedDay.value && days.value[0]) selectedDay.value = days.value[0].date;
});

// -------- COLUNAS DA TABELA --------
const columns = [
  {
    name: 'hour', label: 'Hora', field: 'dt_txt', align: 'center', sortable: false,
  },
  {
    name: 'phenomenon', label: 'Fenômeno', field: 'weather_id', align: 'center', sortable: false,
  },
  {
    name: 'fogprob', label: 'Prob. Nevoeiro', field: 'weather_id', align: 'center', sortable: false,
  },
  {
    name: 'temp', label: 'Temp.', field: 'temp', align: 'center', sortable: false,
  },
  {
    name: 'desc', label: 'Descrição', field: 'weather_description', align: 'left', sortable: false,
  },
  {
    name: 'vento', label: 'Vento', field: 'wind_speed', align: 'center', sortable: false,
  },
  {
    name: 'prob', label: 'Chuva', field: 'pop', align: 'center', sortable: false,
  },
];

// -------- FUNÇÕES UTILITÁRIAS --------
function fogProb(row) {
  // eslint-disable-next-line eqeqeq
  if (row.weather_id == 741) return 100;
  if (row.humidity >= 90 && row.visibility <= 4000) return 60;
  if (row.humidity >= 85 && row.visibility <= 8000) return 30;
  return 0;
}
function getFogProbColor(prob) {
  if (prob >= 80) return 'text-purple-8';
  if (prob >= 50) return 'text-purple-6';
  if (prob > 0) return 'text-blue-7';
  return 'text-grey-6';
}
function getDayFogProb(arr) {
  // eslint-disable-next-line eqeqeq
  if (arr.some((x) => x.weather_id == 741)) return 100;
  let maxProb = 0;
  arr.forEach((x) => {
    let prob = 0;
    // eslint-disable-next-line eqeqeq
    if (x.weather_id == 741) prob = 100;
    else if (x.humidity >= 90 && x.visibility <= 4000) prob = 60;
    else if (x.humidity >= 85 && x.visibility <= 8000) prob = 30;
    if (prob > maxProb) maxProb = prob;
  });
  return maxProb;
}
function windDir(deg) {
  if (deg == null) return '';
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}
function rainRowColor(row) {
  if (!row.pop) return '';
  if (row.pop >= 0.8) return 'rain-red';
  if (row.pop >= 0.5) return 'rain-yellow';
  if (row.pop >= 0.2) return 'rain-blue';
  return 'rain-none';
}

// -------- SVGs FENÔMENOS (editável ao seu gosto) --------
function cloudSVG() {
  return '<svg width="24" height="24" fill="none"><ellipse cx="12" cy="15" rx="8" ry="6" fill="#b0bec5"/><ellipse cx="8" cy="14" rx="4" ry="3" fill="#e3eaf0"/><ellipse cx="17" cy="15" rx="3" ry="2.3" fill="#90a4ae"/></svg>';
}
function fogSVG() {
  return '<svg width="24" height="24" fill="none"><ellipse cx="12" cy="16" rx="8" ry="5" fill="#cfd8dc"/><rect x="4" y="18" width="16" height="2" rx="1" fill="#90a4ae"/><rect x="6" y="21" width="12" height="1.5" rx="0.7" fill="#b0bec5"/></svg>';
}
function mistSVG() {
  return '<svg width="24" height="24" fill="none"><ellipse cx="12" cy="15" rx="7" ry="4" fill="#cfd8dc"/><rect x="5" y="19" width="14" height="1.5" rx="0.7" fill="#b0bec5"/></svg>';
}
function hazeSVG() {
  return '<svg width="24" height="24" fill="none"><ellipse cx="12" cy="15" rx="8" ry="4.5" fill="#fff9c4"/><ellipse cx="12" cy="20" rx="8" ry="2" fill="#ffe082" opacity="0.4"/><rect x="6" y="21" width="12" height="1" rx="0.5" fill="#fffde7"/></svg>';
}
function sunBehindCloudsSVG() {
  return '<svg width="24" height="24" fill="none"><circle cx="8" cy="13" r="4" fill="#ffe082"/><ellipse cx="15" cy="15" rx="7" ry="5" fill="#b0bec5"/><ellipse cx="19" cy="16" rx="3" ry="2" fill="#e3eaf0"/></svg>';
}
function fireSVG() {
  return '<svg width="24" height="24" fill="none"><path d="M12 22c4-3 7-7.1 2-13 0 2-2 3-2 7-2-2-2-6 0-8-5 3-6 9-2 14z" fill="#ffb300"/><path d="M12 19c2-2 4-5 1-9 0 1-1 1-1 4-1-1-1-4 0-5-3 2-4 6-1 10z" fill="#ff7043"/></svg>';
}
</script>

<style scoped>
.forecast-card {
  transition: transform .13s, box-shadow .18s;
  cursor: pointer;
}
.forecast-card:hover {
  transform: translateY(-5px) scale(1.03);
  box-shadow: 0 8px 32px #2d37481a, 0 2px 10px #1a202c11;
}
.rain-red    { background: linear-gradient(90deg, #ffebee, #ffcccb 65%, #ff5252 100%) !important; }
.rain-yellow { background: linear-gradient(90deg, #fffde7, #fff9c4 65%, #ffe082 100%) !important; }
.rain-blue   { background: linear-gradient(90deg, #e3f2fd, #bbdefb 60%, #4fc3f7 100%) !important; }
.rain-none   { background: transparent !important; }
</style>
