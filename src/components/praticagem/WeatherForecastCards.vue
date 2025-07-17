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
      <!-- Seletor de dias -->
      <div class="flex gap-2 items-center justify-center mb-6">
        <q-btn-group push>
          <q-btn
            v-for="dia in availableDays"
            :key="dia"
            :label="formatDay(dia)"
            :color="selectedDay === dia ? 'primary' : 'grey-5'"
            :text-color="selectedDay === dia ? 'white' : 'primary'"
            unelevated
            class="text-weight-bold"
            @click="selectedDay = dia"
          />
        </q-btn-group>
      </div>

      <div v-if="groupedByDay[selectedDay]">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-2">
          <q-card
            v-for="item in groupedByDay[selectedDay]"
            :key="item.dt_txt"
            class="p-3 bg-white/80 hover:bg-blue-100 transition rounded-xl flex flex-col gap-2 shadow cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <img
                :src="`https://openweathermap.org/img/wn/${item.weather_icon || '01d'}@2x.png`"
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
      </div>
      <div v-else class="q-mt-lg text-grey-7 text-center">
        Nenhum dado para o dia selecionado.
      </div>
    </div>
  </q-card>
</template>

<script setup>
import {
  computed, onMounted, ref, watch,
} from 'vue';
import { useWeatherStore } from 'src/stores/weather';

const store = useWeatherStore();
const selectedDay = ref(null);

onMounted(async () => {
  await store.fetchOpenWeatherForecast();
  // Inicializa o dia selecionado para o primeiro disponível
  // eslint-disable-next-line no-use-before-define, prefer-destructuring
  if (availableDays.value.length > 0) selectedDay.value = availableDays.value[0];
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

// Lista de dias disponíveis (ordenados)
const availableDays = computed(() => Object.keys(groupedByDay.value).sort());

// Quando mudar os dados, sempre mantém um dia selecionado válido
watch(groupedByDay, (val) => {
  if (!selectedDay.value || !val[selectedDay.value]) {
    selectedDay.value = availableDays.value[0] || null;
  }
});

function formatDay(dia) {
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const d = new Date(dia);
  // eslint-disable-next-line no-restricted-globals
  if (isNaN(d)) return dia;
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')} (${dias[d.getDay()]})`;
}
</script>

<style scoped>
/* Responsivo e clean para desktop/mobile */
</style>
