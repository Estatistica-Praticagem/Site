<template>
  <q-card class="max-w-5xl mx-auto q-pa-lg shadow-lg bg-gradient-to-br from-blue-50 to-blue-200">
    <div class="text-2xl font-bold text-primary flex items-center gap-2 mb-4">
      <q-icon name="cloud" class="text-blue-700" />
      Previsão do Tempo (5 Dias - 3/3h)
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <q-spinner color="primary" size="50px" />
    </div>
    <div v-else>
      <template v-for="(group, dia) in groupedByDay" :key="dia">
        <div class="mt-6 mb-2 text-lg font-semibold flex items-center gap-2">
          <q-icon name="calendar_today" size="20px" />
          {{ formatDay(dia) }}
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-2">
          <q-card
            v-for="item in group"
            :key="item.dt_txt"
            class="p-3 bg-white/80 hover:bg-blue-100 transition rounded-xl flex flex-col gap-2 shadow cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <img
                :src="`https://openweathermap.org/img/wn/${item.weather_icon || item.weather_icon}@2x.png`"
                :alt="item.weather_description"
                class="w-10 h-10"
              />
              <div>
                <div class="text-base font-bold">{{ item.temp?.toFixed(1) }}°C</div>
                <div class="text-xs text-gray-500 capitalize">{{ item.weather_description }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-1 mt-2">
              <span class="flex items-center gap-1 text-sm">
                <q-icon name="access_time" size="16px" /> {{ item.dt_txt.slice(11, 16) }}
              </span>
              <span class="flex items-center gap-1 text-sm">
                <q-icon name="water_drop" size="16px" /> Umid. {{ item.humidity ?? '--' }}%
              </span>
              <span class="flex items-center gap-1 text-sm">
                <q-icon name="compress" size="16px" /> Pressão {{ item.pressure ?? '--' }} hPa
              </span>
              <span class="flex items-center gap-1 text-sm">
                <q-icon name="air" size="16px" /> Vento {{ item.wind_speed ?? '--' }} m/s
              </span>
            </div>
          </q-card>
        </div>
        <q-separator spaced="md" color="blue-200" />
      </template>
    </div>
  </q-card>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useWeatherStore } from 'src/stores/weather';

const store = useWeatherStore();

onMounted(() => {
  store.fetchOpenWeatherForecast(); // Troque para o método correto!
});

const loading = computed(() => store.loading);

// Agrupa por data (YYYY-MM-DD)
const groupedByDay = computed(() => {
  if (!Array.isArray(store.openWeatherForecast)) return {};
  return store.openWeatherForecast.reduce((acc, item) => {
    const dia = item.dt_txt ? item.dt_txt.slice(0, 10) : 'Indefinido';
    if (!acc[dia]) acc[dia] = [];
    acc[dia].push(item);
    return acc;
  }, {});
});

function formatDay(dia) {
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const d = new Date(dia);
  // eslint-disable-next-line no-restricted-globals
  if (isNaN(d)) return dia;
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')} (${dias[d.getDay()]})`;
}
</script>
