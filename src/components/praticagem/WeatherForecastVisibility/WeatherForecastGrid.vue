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
            <th class="text-center">Sensação</th>
            <th class="text-center">Pressão</th>
            <th class="text-center">Umidade</th>
            <th class="text-center">Visibilidade</th>
            <th class="text-center">Vento</th>
            <!-- <th class="text-center">Rajada</th>
            <th class="text-center">Chuva</th>
            <th class="text-center">Nuvens</th>
            <th class="text-center">Descrição</th> -->
            <!-- <th class="text-center">Ícone</th> -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.dt_txt" :class="rainRowColor(row)">
            <!-- Hora -->
            <td class="flex items-center gap-2 justify-center">
              <q-icon name="access_time" color="grey-7" size="18px"/>
              {{ row.dt_txt?.slice(11, 16) }}
            </td>
            <!-- Fenômeno -->
            <td>
              <div class="flex items-center gap-2 justify-center rounded-xl px-2 py-1"
                :class="phenomenonInfo(row).color"
                style="min-width:90px;">
                <span class="text-xl" v-html="phenomenonInfo(row).svgIcon"></span>
                <span class="font-bold">{{ phenomenonInfo(row).name }}</span>
                <span class="text-xs ml-1 px-1 py-0.5 rounded bg-white/30 text-grey-8 border font-mono">
                  <!-- {{ row.weather_id }} -->
                </span>
              </div>
            </td>
            <!-- Prob. Nevoeiro -->
            <td>
              <div class="flex items-center gap-1 justify-center">
                <q-icon name="waves" color="purple-7" size="18px" />
                <span :class="fogProbColor(fogProb(row)) + ' font-bold'">
                  {{ fogProb(row) }}%
                </span>
              </div>
            </td>
            <!-- Temperatura -->
            <td>
              <span class="text-lg font-bold">{{ row.temp?.toFixed(1) }}°</span>
            </td>
            <!-- Sensação térmica -->
            <td>
              <span class="text-blue-7 font-bold">{{ row.feels_like?.toFixed(1) }}°</span>
            </td>
            <!-- Pressão -->
            <td>
              <span class="font-mono">{{ row.pressure ? row.pressure + ' hPa' : '--' }}</span>
            </td>
            <!-- Umidade -->
            <td>
              <span class="font-mono">{{ row.humidity ? row.humidity + '%' : '--' }}</span>
            </td>
            <!-- Visibilidade -->
            <td>
              <span>{{ row.visibility != null ? (row.visibility/1000).toFixed(1) + ' km' : '--' }}</span>
            </td>
            <!-- Vento (em knots) -->
            <td>
              <span class="flex items-center gap-2">
                <WindMiniSeta :deg="row.wind_deg" :speed="row.wind_speed" />
                {{ windDir(row.wind_deg) }}
                <span class="text-blue-8 text-weight-medium">
                  {{ row.wind_speed_knots != null ? row.wind_speed_knots.toFixed(1) + ' kts' : '--' }}
                </span>
              </span>
            </td>
            <!-- Rajada de vento (em knots)
            <td>
              <span>
                {{ row.wind_gust_knots != null ? row.wind_gust_knots.toFixed(1) + ' kts' : '--' }}
              </span>
            </td>
            Chuva
             <td>
              <span v-if="typeof row.pop === 'number'">
                <q-icon name="water_drop" color="blue-6" size="18px"/>
                <span :class="row.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
                  {{ Math.round(row.pop * 100) }}%
                </span>
                <span v-if="row.rain_3h" class="text-grey-7">({{ row.rain_3h }}mm/3h)</span>
                <span v-else-if="row.rain_1h" class="text-grey-7">({{ row.rain_1h }}mm/1h)</span>
              </span>
              <span v-else>--</span>
            </td>
            Nuvens
            <td>
              <span>{{ row.clouds != null ? row.clouds + '%' : '--' }}</span>
            </td>
             Descrição -->
            <!-- <td>
              <span style="text-transform: capitalize">{{ row.weather_description }}</span>
            </td>  -->
            <!-- Ícone openweather -->
            <!-- <td>
              <img
                v-if="row.weather_icon"
                :src="`https://openweathermap.org/img/wn/${row.weather_icon}@2x.png`"
                :alt="row.weather_description"
                style="width: 38px; height: 38px;"
              >
            </td> -->
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import WindMiniSeta from 'src/components/praticagem/WeatherForecast/WindMiniSeta.vue';

const props = defineProps({
  day: { type: String, required: true },
  groupedByDay: { type: Object, required: true },
  cityLabel: { type: String, required: false, default: '' },
});

// Converte m/s para knots (nós)
function msToKnots(ms) {
  if (typeof ms !== 'number') return null;
  return ms * 1.94384;
}

// Cor de linha baseada na precipitação
function rainRowColor(row) {
  if (!row.pop) return '';
  if (row.pop >= 0.8) return 'rain-red';
  if (row.pop >= 0.5) return 'rain-yellow';
  if (row.pop >= 0.2) return 'rain-blue';
  return 'rain-none';
}
const rows = computed(() => (props.groupedByDay[props.day] || []).map((item) => {
  // eslint-disable-next-line camelcase
  const wind_speed_ms = item.wind_speed ?? item.wind?.speed;
  // eslint-disable-next-line camelcase
  const wind_gust_ms = item.wind_gust ?? item.wind?.gust;
  return {
    ...item,
    temp: item.temp ?? item.main?.temp,
    feels_like: item.feels_like ?? item.main?.feels_like,
    pressure: item.pressure ?? item.main?.pressure,
    humidity: item.humidity ?? item.main?.humidity,
    clouds: item.clouds?.all ?? item.clouds,
    // eslint-disable-next-line camelcase
    wind_speed: wind_speed_ms,
    // eslint-disable-next-line camelcase
    wind_speed_knots: wind_speed_ms != null ? msToKnots(wind_speed_ms) : null,
    wind_deg: item.wind_deg ?? item.wind?.deg,
    // eslint-disable-next-line camelcase
    wind_gust: wind_gust_ms,
    // eslint-disable-next-line camelcase
    wind_gust_knots: wind_gust_ms != null ? msToKnots(wind_gust_ms) : null,
    pop: item.pop,
    rain_1h: item.rain?.['1h'],
    rain_3h: item.rain?.['3h'],
    weather_id: item.weather_id ?? item.weather?.[0]?.id,
    weather_description: item.weather_description ?? item.weather?.[0]?.description,
    weather_icon: item.weather_icon ?? item.weather?.[0]?.icon,
    visibility: item.visibility,
  };
}));

function phenomenonInfo(row) {
  // Lista completa dos principais fenômenos meteorológicos por ID da OpenWeather
  const weatherPhenomena = {
    // Thunderstorm
    200: { name: 'Trovoada com chuva leve', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    201: { name: 'Trovoada com chuva', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    202: { name: 'Trovoada com chuva forte', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    210: { name: 'Trovoada leve', svgIcon: '🌩️', color: 'phenomenon-thunderstorm' },
    211: { name: 'Trovoada', svgIcon: '🌩️', color: 'phenomenon-thunderstorm' },
    212: { name: 'Trovoada forte', svgIcon: '🌩️', color: 'phenomenon-thunderstorm' },
    221: { name: 'Trovoada irregular', svgIcon: '🌩️', color: 'phenomenon-thunderstorm' },
    230: { name: 'Trovoada com garoa leve', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    231: { name: 'Trovoada com garoa', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    232: { name: 'Trovoada com garoa forte', svgIcon: '⛈️', color: 'phenomenon-thunderstorm' },
    // Drizzle
    300: { name: 'Garoa leve', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    301: { name: 'Garoa', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    302: { name: 'Garoa forte', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    310: { name: 'Chuvisco leve', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    311: { name: 'Chuvisco', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    312: { name: 'Chuvisco forte', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    313: { name: 'Garoa com chuva', svgIcon: '🌧️', color: 'phenomenon-drizzle' },
    314: { name: 'Garoa forte com chuva', svgIcon: '🌧️', color: 'phenomenon-drizzle' },
    321: { name: 'Chuvisco', svgIcon: '🌦️', color: 'phenomenon-drizzle' },
    // Rain
    500: { name: 'Chuva leve', svgIcon: '🌧️', color: 'phenomenon-rain' },
    501: { name: 'Chuva moderada', svgIcon: '🌧️', color: 'phenomenon-rain' },
    502: { name: 'Chuva forte', svgIcon: '🌧️', color: 'phenomenon-rain' },
    503: { name: 'Chuva muito forte', svgIcon: '🌧️', color: 'phenomenon-rain' },
    504: { name: 'Chuva extrema', svgIcon: '🌧️', color: 'phenomenon-rain' },
    511: { name: 'Chuva congelante', svgIcon: '🧊', color: 'phenomenon-rain' },
    520: { name: 'Aguaceiro leve', svgIcon: '🌦️', color: 'phenomenon-rain' },
    521: { name: 'Aguaceiro', svgIcon: '🌦️', color: 'phenomenon-rain' },
    522: { name: 'Aguaceiro forte', svgIcon: '🌦️', color: 'phenomenon-rain' },
    531: { name: 'Aguaceiro irregular', svgIcon: '🌦️', color: 'phenomenon-rain' },
    // Snow
    600: { name: 'Neve leve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    601: { name: 'Neve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    602: { name: 'Neve forte', svgIcon: '🌨️', color: 'phenomenon-snow' },
    611: { name: 'Aguanieve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    612: { name: 'Aguanieve leve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    613: { name: 'Aguanieve intensa', svgIcon: '🌨️', color: 'phenomenon-snow' },
    615: { name: 'Chuva leve com neve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    616: { name: 'Chuva com neve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    620: { name: 'Nevasca leve', svgIcon: '🌨️', color: 'phenomenon-snow' },
    621: { name: 'Nevasca', svgIcon: '🌨️', color: 'phenomenon-snow' },
    622: { name: 'Nevasca forte', svgIcon: '🌨️', color: 'phenomenon-snow' },
    // Atmosphere
    701: { name: 'Névoa', svgIcon: '🌫️', color: 'phenomenon-mist' },
    711: { name: 'Fumaça', svgIcon: '🔥', color: 'phenomenon-smoke' },
    721: { name: 'Neblina seca', svgIcon: '🌫️', color: 'phenomenon-haze' },
    731: { name: 'Redemoinho de areia', svgIcon: '🌪️', color: 'phenomenon-dust' },
    741: { name: 'Nevoeiro', svgIcon: '🌁', color: 'phenomenon-fog' },
    751: { name: 'Areia', svgIcon: '🏜️', color: 'phenomenon-sand' },
    761: { name: 'Poeira', svgIcon: '🏜️', color: 'phenomenon-dust' },
    762: { name: 'Cinzas vulcânicas', svgIcon: '🌋', color: 'phenomenon-ash' },
    771: { name: 'Rajadas de vento', svgIcon: '💨', color: 'phenomenon-squall' },
    781: { name: 'Tornado', svgIcon: '🌪️', color: 'phenomenon-tornado' },
    // Clear
    800: { name: 'Céu limpo', svgIcon: '☀️', color: 'phenomenon-sun' },
    // Clouds
    801: { name: 'Poucas nuvens', svgIcon: '🌤️', color: 'phenomenon-fewclouds' },
    802: { name: 'Nuvens dispersas', svgIcon: '⛅', color: 'phenomenon-scattered' },
    803: { name: 'Nuvens fragmentadas', svgIcon: '🌥️', color: 'phenomenon-broken' },
    804: { name: 'Céu encoberto', svgIcon: '☁️', color: 'phenomenon-overcast' },
    // Extreme (casos raros, categoria adicional)
    900: { name: 'Tornado', svgIcon: '🌪️', color: 'phenomenon-tornado' },
    901: { name: 'Tempestade tropical', svgIcon: '🌀', color: 'phenomenon-tropical' },
    902: { name: 'Furacão', svgIcon: '🌀', color: 'phenomenon-hurricane' },
    903: { name: 'Frio extremo', svgIcon: '🥶', color: 'phenomenon-cold' },
    904: { name: 'Calor extremo', svgIcon: '🥵', color: 'phenomenon-hot' },
    905: { name: 'Ventos fortes', svgIcon: '💨', color: 'phenomenon-windy' },
    906: { name: 'Granizo', svgIcon: '🌨️', color: 'phenomenon-hail' },
    // Nota: códigos a partir de 950 são variações do vento e geralmente não aparecem na forecast da API comum
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
function fogProbColor(prob) {
  if (prob >= 80) return 'text-purple-8';
  if (prob >= 50) return 'text-purple-6';
  if (prob > 0) return 'text-blue-7';
  return 'text-grey-6';
}
function windDir(deg) {
  if (deg == null) return '';
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}

</script>

<style scoped>
.forecast-grid-bg {
  background: linear-gradient(120deg, #f4f8ff 70%, #e1e4ec 100%);
}
.grid-table-wrap {
  overflow-x: auto;
}
.forecast-grid-table tr,
.forecast-grid-table td {
  color: #111 !important;   /* Preto forte */
  background: #fff !important; /* Fundo branco, opcional */
  border-bottom: 1px solid #e0e0e0; /* Opcional, para visual divisão */
}

/* Tira qualquer cor de linha de chuva ou gradiente */
.rain-red,
.rain-yellow,
.rain-blue,
.rain-none {
  background: #fff !important;
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
