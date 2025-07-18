<!-- eslint-disable no-use-before-define -->
<template>
  <div>
    <!-- TOGGLE DE MODO -->
    <div class="q-mb-md flex items-center gap-4 justify-between">
      <q-btn-toggle
        v-model="mode"
        dense
        toggle-color="primary"
        color="white"
        text-color="primary"
        unelevated
        :options="[
          { label: 'Detalhado', value: 'detailed' },
          { label: 'Compacto', value: 'compact' }
        ]"
      />
      <!-- Botões de dia aparecem só no modo detalhado -->
      <div v-if="mode === 'detailed'" class="flex gap-2 items-center">
        <div class="font-bold text-base text-grey-8">Escolha o dia:</div>
        <q-btn
          v-for="opt in availableDays"
          :key="opt.value"
          @click="selectedDay = opt.value"
          color="white"
          :text-color="selectedDay === opt.value ? 'primary' : 'grey-8'"
          :unelevated="true"
          :outline="selectedDay !== opt.value"
          :class="selectedDay === opt.value ? 'btn-dia-ativo' : ''"
          class="btn-dia"
          size="sm"
        >
          <div class="flex flex-col items-center">
            <span class="text-base">{{ opt.label.split(',')[0] }}</span>
            <span class="text-xs text-grey-6">{{ opt.label.split(',')[1] }}</span>
          </div>
        </q-btn>
      </div>
    </div>

    <!-- Só UM dos modos renderiza por vez -->
    <ForecastGrid
      v-if="mode === 'detailed'"
      :day="selectedDay"
      :grouped-by-day="groupedByDay"
      city-label="Previsão Horária"
    />

    <WeatherForecastCompact
      v-else-if="mode === 'compact'"
      :days="days"
    />
  </div>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue';
import { useWeatherStore } from 'src/stores/weather';
import WeatherForecastCompact from 'src/components/praticagem/WeatherForecastVisibility/WeatherForecastCompact.vue';
import ForecastGrid from 'src/components/praticagem/WeatherForecastVisibility/WeatherForecastGrid.vue';

// STORE
const store = useWeatherStore();
const forecast = computed(() => store.openWeatherForecast || []);

// Agrupa por dia
const groupedByDay = computed(() => {
  const res = {};
  forecast.value.forEach((item) => {
    const day = item.dt_txt?.slice(0, 10);
    if (!day) return;
    res[day] = res[day] || [];
    res[day].push(item);
  });
  return res;
});

// Cards dos dias
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
      // eslint-disable-next-line camelcase
      const [weather_id] = Object.entries(dominant).sort((a, b) => b[1] - a[1])[0] || [800];
      // eslint-disable-next-line camelcase
      const phenomenon = weatherPhenomena[weather_id] || { name: 'Desconhecido', svgIcon: '❓', color: 'phenomenon-unknown' };
      // eslint-disable-next-line camelcase
      const temp_max = Math.max(...arr.map((x) => Number(x.temp_max) || 0));
      // eslint-disable-next-line camelcase
      const temp_min = Math.min(...arr.map((x) => Number(x.temp_min) || 0));
      // eslint-disable-next-line camelcase
      const humidity_avg = Math.round(arr.reduce((s, x) => s + (Number(x.humidity) || 0), 0) / arr.length);
      const fogProb = getDayFogProb(arr);
      const dt = new Date(`${date}T12:00:00`);
      const dateLabel = dt.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: '2-digit' });
      return {
        // eslint-disable-next-line camelcase
        date, dateLabel, weather_id, phenomenon, temp_max, temp_min, humidity_avg, fogProb,
      };
    });
});
const availableDays = computed(() => days.value.map((d) => ({
  label: d.dateLabel,
  value: d.date,
})));
const mode = ref('detailed'); // Começa detalhado!
const selectedDay = ref('');
watchEffect(() => {
  if (!selectedDay.value && days.value[0]) selectedDay.value = days.value[0].date;
});

// SVG functions (mantenha os mesmos, já conhecidos do projeto)
function cloudSVG() { return '<svg width="24".../svg>'; }
function fogSVG() { return '<svg width="24".../svg>'; }
function mistSVG() { return '<svg width="24".../svg>'; }
function hazeSVG() { return '<svg width="24".../svg>'; }
function sunBehindCloudsSVG() { return '<svg width="24".../svg>'; }
function fireSVG() { return '<svg width="24".../svg>'; }
</script>

<style scoped>
/* Seus estilos existentes dos cards e botões podem ficar aqui. */
</style>
