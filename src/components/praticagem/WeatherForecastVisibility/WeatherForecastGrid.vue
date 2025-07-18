<template>
  <div class="forecast-grid-bg rounded-xl shadow-xl p-3 overflow-x-auto">
    <div class="flex items-center gap-2 mb-2">
      <q-icon name="waves" class="text-blue-8" />
      <span class="text-lg font-bold">{{ cityLabel }} — Grade de Previsão</span>
    </div>
    <div class="overflow-x-auto grid-table-wrap">
      <table class="forecast-grid-table w-full">
        <thead>
          <tr>
            <th class="text-center">Hora</th>
            <th class="text-center">Fenômeno</th>
            <th class="text-center">Prob. Nevoeiro</th>
            <th class="text-center">Temp.</th>
            <th class="text-center">Descrição</th>
            <th class="text-center">Vento</th>
            <th class="text-center">Chuva</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.dt_txt" :class="rainRowColor(row)">
            <td class="flex items-center gap-2 justify-center">
              <q-icon name="access_time" color="grey-7" size="18px"/>
              {{ row.dt_txt?.slice(11, 16) }}
            </td>
            <td>
              <q-tooltip>
                <!-- {{ phenomenonInfo(row).name }}<br>Código: {{ row.weather_id }} -->
              </q-tooltip>
              <div class="flex items-center gap-2 justify-center rounded-xl px-2 py-1"
                :class="phenomenonInfo(row).color"
                style="min-width:90px;">
                <span class="text-xl" v-html="phenomenonInfo(row).svgIcon"></span>
                <span class="font-bold">{{ phenomenonInfo(row).name }}</span>
                <span class="text-xs ml-1 px-1 py-0.5 rounded bg-white/30 text-grey-8 border font-mono">
                  {{ row.weather_id }}
                </span>
              </div>
            </td>
            <td>
              <div class="flex items-center gap-1 justify-center">
                <q-icon name="waves" color="purple-7" size="18px" />
                <span :class="fogProbColor(fogProb(row)) + ' font-bold'">
                  {{ fogProb(row) }}%
                </span>
              </div>
            </td>
            <td>
              <span class="text-lg font-bold">{{ row.temp?.toFixed(0) }}°</span>
              <span class="text-grey-6 text-xs block">Sens. {{ row.feels_like?.toFixed(0) }}°</span>
            </td>
            <td>
              <span style="text-transform: capitalize">{{ row.weather_description }}</span>
            </td>
            <td>
              <span class="flex items-center gap-2">
                <WindMiniClock :deg="row.wind_deg" :speed="row.wind_speed" />
                {{ windDir(row.wind_deg) }}
                <span class="text-blue-8 text-weight-medium">{{ row.wind_speed }} km/h</span>
              </span>
            </td>
            <td>
              <span v-if="typeof row.pop === 'number'">
                <q-icon name="water_drop" color="blue-6" size="18px"/>
                <span :class="row.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
                  {{ Math.round(row.pop * 100) }}%
                </span>
                <span v-if="row.rain_3h" class="text-grey-7">({{ row.rain_3h }}mm)</span>
              </span>
              <span v-else>--</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import WindMiniClock from 'src/components/praticagem/WeatherForecast/WindMiniClock.vue';

const props = defineProps({
  day: { type: String, required: true },
  groupedByDay: { type: Object, required: true },
  cityLabel: { type: String, required: false, default: '' },
});

// Função para cor de linha baseada na precipitação
function rainRowColor(row) {
  if (!row.pop) return '';
  if (row.pop >= 0.8) return 'rain-red';
  if (row.pop >= 0.5) return 'rain-yellow';
  if (row.pop >= 0.2) return 'rain-blue';
  return 'rain-none';
}

// Função para retornar info do fenômeno
function phenomenonInfo(row) {
  // Os mesmos ids/fenômenos do seu pai (se quiser, pode passar como prop também)
  const weatherPhenomena = {
    800: { name: 'Céu limpo', svgIcon: '☀️', color: 'phenomenon-sun' },
    801: { name: 'Poucas nuvens', svgIcon: '🌤️', color: 'phenomenon-fewclouds' },
    802: { name: 'Nuvens dispersas', svgIcon: '⛅', color: 'phenomenon-scattered' },
    // eslint-disable-next-line no-use-before-define
    803: { name: 'Nuvens fragmentadas', svgIcon: sunBehindCloudsSVG(), color: 'phenomenon-broken' },
    // eslint-disable-next-line no-use-before-define
    804: { name: 'Céu encoberto', svgIcon: cloudSVG(), color: 'phenomenon-overcast' },
    // eslint-disable-next-line no-use-before-define
    741: { name: 'Nevoeiro', svgIcon: fogSVG(), color: 'phenomenon-fog' },
    // eslint-disable-next-line no-use-before-define
    701: { name: 'Névoa', svgIcon: mistSVG(), color: 'phenomenon-mist' },
    // eslint-disable-next-line no-use-before-define
    721: { name: 'Neblina seca', svgIcon: hazeSVG(), color: 'phenomenon-haze' },
    // eslint-disable-next-line no-use-before-define
    711: { name: 'Fumaça', svgIcon: fireSVG(), color: 'phenomenon-smoke' },
  };
  return weatherPhenomena[row.weather_id] || { name: 'Desconhecido', svgIcon: '❓', color: 'phenomenon-unknown' };
}

// Função para probabilidade de nevoeiro
function fogProb(row) {
  // eslint-disable-next-line eqeqeq
  if (row.weather_id == 741) return 100;
  if (row.humidity >= 90 && row.visibility <= 4000) return 60;
  if (row.humidity >= 85 && row.visibility <= 8000) return 30;
  return 0;
}
// Cores para probabilidade de nevoeiro
function fogProbColor(prob) {
  if (prob >= 80) return 'text-purple-8';
  if (prob >= 50) return 'text-purple-6';
  if (prob > 0) return 'text-blue-7';
  return 'text-grey-6';
}

// Direção do vento
function windDir(deg) {
  if (deg == null) return '';
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}

// Filtra as linhas desse dia selecionado
const rows = computed(() => props.groupedByDay[props.day] || []);

// SVGs inline para manter visual do fenômeno (se quiser pode importar de um arquivo separado)
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
.forecast-grid-bg {
  background: linear-gradient(120deg, #f4f8ff 70%, #e1e4ec 100%);
}
.grid-table-wrap {
  overflow-x: auto;
}
.forecast-grid-table {
  min-width: 800px;
  background: rgba(255,255,255,0.98);
  border-radius: 1em;
  box-shadow: 0 4px 32px #b0bec515, 0 1px 4px #2d37481a;
  margin-top: 10px;
}
.rain-red    { background: linear-gradient(90deg, #ffebee, #ffcccb 65%, #ff5252 100%) !important; }
.rain-yellow { background: linear-gradient(90deg, #fffde7, #fff9c4 65%, #ffe082 100%) !important; }
.rain-blue   { background: linear-gradient(90deg, #e3f2fd, #bbdefb 60%, #4fc3f7 100%) !important; }
.rain-none   { background: transparent !important; }
.phenomenon-sun { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-fewclouds { background: linear-gradient(120deg, #e3f2fd 70%, #bbdefb 100%) !important; color: #0d47a1 !important; }
.phenomenon-scattered { background: linear-gradient(120deg, #e3f2fd 70%, #90caf9 100%) !important; color: #1976d2 !important; }
.phenomenon-broken { background: linear-gradient(120deg, #f5f5f5 70%, #90a4ae 100%) !important; color: #37474f !important; }
.phenomenon-overcast { background: linear-gradient(120deg, #cfd8dc 70%, #78909c 100%) !important; color: #212121 !important; }
.phenomenon-fog { background: linear-gradient(120deg, #eceff1 70%, #90a4ae 100%) !important; color: #333 !important; }
.phenomenon-mist { background: linear-gradient(120deg, #e3eaf0 70%, #b0bec5 100%) !important; color: #333 !important; }
.phenomenon-haze { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-smoke { background: linear-gradient(120deg, #fff9c4 70%, #ffe082 100%) !important; color: #ff7043 !important; }
.phenomenon-unknown { background: #fff !important; color: #888 !important; }
</style>
