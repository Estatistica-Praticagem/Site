<template>
  <div class="forecast-cards-bg">
    <div class="forecast-cards-container">
      <div
        v-for="day in daysSummary"
        :key="day.date"
        class="forecast-card"
        :class="day.phenomenon.color"
      >
        <div class="phenomenon-row">
          <span class="phenomenon-icon" v-html="day.phenomenon.svgIcon"></span>
          <span class="phenomenon-name">{{ day.phenomenon.name }}</span>
        </div>
        <div class="date-label">{{ day.dateLabel }}</div>
        <div class="values-grid">
          <div>
            <div class="label">Máxima</div>
            <div class="val-max">{{ showVal(day.temp_max) }}°</div>
          </div>
          <div>
            <div class="label">Mínima</div>
            <div class="val-min">{{ showVal(day.temp_min) }}°</div>
          </div>
          <div>
            <div class="label">Umidade média</div>
            <div class="val-humidity">{{ showVal(day.humidity_avg) }}%</div>
          </div>
          <div>
            <div class="label">Prob. Nevoeiro</div>
            <div :class="['val-fog', fogProbColor(day.fogProb)]">{{ showVal(day.fogProb) }}%</div>
          </div>
        </div>
        <div class="weather-id-row">
          <q-tooltip anchor="bottom middle">
            <!-- Código dominante: <b>{{ day.weather_id }}</b> -->
          </q-tooltip>
          <!-- <span class="weather-id">#{{ day.weather_id }}</span> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  // Dias do pai: array [{ date, arr }]
  days: { type: Array, required: true },
});

// Faz o resumo igual ao pai antes de exibir
const daysSummary = computed(() => props.days.map(({ date, arr }) => {
  // Fenômeno dominante
  const dominant = arr
    .map((r) => r.weather_id)
    .reduce((acc, v) => {
      acc[v] = (acc[v] || 0) + 1;
      return acc;
    }, {});
  // eslint-disable-next-line camelcase
  const [weather_id] = Object.entries(dominant).sort((a, b) => b[1] - a[1])[0] || [800];
  // eslint-disable-next-line no-use-before-define, camelcase
  const phenomenon = phenomenonInfo({ weather_id });

  // Temp/humidade
  // eslint-disable-next-line camelcase
  const temp_max = Math.max(...arr.map((x) => Number(x.temp_max ?? x.temp)));
  // eslint-disable-next-line camelcase
  const temp_min = Math.min(...arr.map((x) => Number(x.temp_min ?? x.temp)));
  // eslint-disable-next-line camelcase
  const humidity_avg = Math.round(arr.reduce((s, x) => s + (Number(x.humidity) || 0), 0) / arr.length);

  // Prob. nevoeiro
  // eslint-disable-next-line eqeqeq
  const fogProb = arr.some((x) => x.weather_id == 741)
    ? 100
    : arr.some((x) => x.humidity >= 90 && x.visibility <= 4000)
      ? 60
      : arr.some((x) => x.humidity >= 85 && x.visibility <= 8000)
        ? 30
        : 0;

  // Label
  const dt = new Date(`${date}T12:00:00`);
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const dateLabel = `${dt.getDate().toString().padStart(2, '0')}/${(dt.getMonth() + 1).toString().padStart(2, '0')} (${dias[dt.getDay()]})`;

  return {
    date,
    dateLabel,
    // eslint-disable-next-line camelcase
    temp_max,
    // eslint-disable-next-line camelcase
    temp_min,
    // eslint-disable-next-line camelcase
    humidity_avg,
    fogProb,
    weather_id: Number(weather_id),
    phenomenon,
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

function fogProbColor(prob) {
  if (prob >= 80) return 'text-purple';
  if (prob >= 50) return 'text-purple-light';
  if (prob > 0) return 'text-blue';
  return 'text-grey';
}
function showVal(val) {
  // eslint-disable-next-line no-restricted-globals
  return typeof val === 'number' && !isNaN(val) ? val : '--';
}
</script>

<!-- Seu CSS já está ótimo, não precisa mudar! -->

<style scoped>
.forecast-cards-bg {
  background: linear-gradient(120deg, #f5f9ff 70%, #e3eaf6 100%);
  padding: 26px 0 20px 0;
  width: 100%;
}
.forecast-cards-container {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  max-width: 1100px;
  margin: auto;
}
.forecast-card {
  min-width: 210px;
  max-width: 255px;
  min-height: 205px;
  border-radius: 2rem;
  box-shadow: 0 8px 32px #2d37481a, 0 2px 8px #1a202c10;
  border: 1.5px solid #e3eaf0;
  padding: 23px 23px 18px 23px;
  background: rgba(255,255,255,0.97);
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: box-shadow .15s, transform .13s;
  cursor: pointer;
}
.forecast-card:hover {
  box-shadow: 0 12px 46px #0072c21b, 0 2px 12px #1a202c13;
  transform: translateY(-3px) scale(1.035);
}

/* Fenômeno */
.phenomenon-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 5px;
}
.phenomenon-icon {
  font-size: 2.2rem;
  filter: drop-shadow(0 1px 3px #fff5);
}
.phenomenon-name {
  font-weight: bold;
  font-size: 1.15rem;
  line-height: 1.2;
}

/* Data */
.date-label {
  font-family: 'JetBrains Mono', 'Fira Mono', monospace;
  font-size: 0.93em;
  color: #465e7a;
  margin-bottom: 13px;
  font-weight: 600;
  letter-spacing: 0.01em;
}

/* Grid dos valores */
.values-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px 24px;
  width: 100%;
  margin-bottom: 5px;
}
.label {
  font-size: 0.80em;
  color: #6d7990;
  font-weight: 600;
  margin-bottom: 1px;
}
.val-max {
  color: #de1616;
  font-size: 1.15em;
  font-weight: bold;
  letter-spacing: .02em;
}
.val-min {
  color: #2283d5;
  font-size: 1.15em;
  font-weight: bold;
  letter-spacing: .02em;
}
.val-humidity {
  color: #2888a4;
  font-size: 1.10em;
  font-weight: 600;
}
.val-fog.text-purple      { color: #7c3aed; font-weight: 700; }
.val-fog.text-purple-light{ color: #b39ddb; font-weight: 700; }
.val-fog.text-blue        { color: #2483c9; font-weight: 700; }
.val-fog.text-grey        { color: #838a94; font-weight: 600; }

/* Código weather_id */
.weather-id-row {
  margin-top: 7px;
  font-size: 0.97em;
  display: flex;
  align-items: center;
  gap: 6px;
}
.weather-id {
  background: #fffefb99;
  border-radius: 7px;
  color: #485054;
  font-family: 'JetBrains Mono', 'Fira Mono', monospace;
  font-size: 0.94em;
  border: 1px solid #e3eaf0;
  padding: 3px 8px;
  margin-left: 2px;
}

/* Gradientes fenômeno (classes já existentes no seu projeto) */
.phenomenon-sun        { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-fewclouds  { background: linear-gradient(120deg, #e3f2fd 70%, #bbdefb 100%) !important; color: #0d47a1 !important; }
.phenomenon-scattered  { background: linear-gradient(120deg, #e3f2fd 70%, #90caf9 100%) !important; color: #1976d2 !important; }
.phenomenon-broken     { background: linear-gradient(120deg, #f5f5f5 70%, #90a4ae 100%) !important; color: #37474f !important; }
.phenomenon-overcast   { background: linear-gradient(120deg, #cfd8dc 70%, #78909c 100%) !important; color: #212121 !important; }
.phenomenon-fog        { background: linear-gradient(120deg, #eceff1 70%, #90a4ae 100%) !important; color: #333 !important; }
.phenomenon-mist       { background: linear-gradient(120deg, #e3eaf0 70%, #b0bec5 100%) !important; color: #333 !important; }
.phenomenon-haze       { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-smoke      { background: linear-gradient(120deg, #fff9c4 70%, #ffe082 100%) !important; color: #ff7043 !important; }
.phenomenon-unknown    { background: #fff !important; color: #888 !important; }

@media (max-width: 750px) {
  .forecast-card {
    min-width: 150px;
    max-width: 99vw;
    padding: 13px 7px 12px 7px;
    font-size: 0.94em;
  }
  .forecast-cards-container {
    gap: 15px;
  }
}
</style>
