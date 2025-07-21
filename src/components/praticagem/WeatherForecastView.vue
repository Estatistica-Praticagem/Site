<template>
  <q-card class="weather-sky-bg max-w-4xl mx-auto q-pa-md q-mt-lg q-mb-lg shadow-xl relative">
    <div class="text-xl font-bold text-primary flex items-center gap-2 mb-4">
      <q-icon name="place" class="text-blue-700" />
      Tempo {{ cityLabel }} Hoje, {{ formatDay(selectedDay) }}
    </div>

    <!-- Seletor de modo e dia -->
    <div class="flex gap-2 mb-4 justify-center flex-wrap">
      <q-btn-toggle
        v-model="viewMode"
        :options="[
          { label: 'Grade', value: 'grid', icon: 'grid_on' },
          // { label: 'Tabela', value: 'table', icon: 'table_chart' },
          { label: 'Cartões', value: 'cards', icon: 'view_module' },
          { label: 'Linha', value: 'bar', icon: 'horizontal_split' },
        ]"
        color="primary"
        spread
        dense
      />
      <div class="ml-4 flex gap-1">
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
          @click="selectedDay = dia"
        />
      </div>
    </div>

    <!-- Renderiza visualização selecionada -->
    <WeatherForecastGrid v-if="viewMode === 'grid'"
      :day="selectedDay"
      :groupedByDay="groupedByDay"
      :cityLabel="cityLabel"
      :availableDays="availableDays"
      @update:day="selectedDay = $event"
    />
    <WeatherForecastTable v-else-if="viewMode === 'table'" :rows="groupedByDay[selectedDay] || []" />
    <WeatherForecastCards v-else-if="viewMode === 'cards'" :rows="groupedByDay[selectedDay] || []" />
    <WeatherForecastBar v-else-if="viewMode === 'bar'" :rows="groupedByDay[selectedDay] || []" />
  </q-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWeatherStore } from 'src/stores/weather';

import WeatherForecastTable from 'src/components/praticagem/WeatherForecast/WeatherForecastTable.vue';
import WeatherForecastCards from 'src/components/praticagem/WeatherForecast/WeatherForecastCards.vue';
import WeatherForecastBar from 'src/components/praticagem/WeatherForecast/WeatherForecastBar.vue';
import WeatherForecastGrid from 'src/components/praticagem/WeatherForecast/WeatherForecastGrid.vue';

const store = useWeatherStore();
const selectedDay = ref(null);
const viewMode = ref('grid');

// Não faz mais ajuste para UTC-3 no agrupamento, usa dt_txt "cru"
const groupedByDay = computed(() => {
  const res = {};
  (store.openWeatherForecast || []).forEach((item) => {
    if (!item.dt_txt) return;
    const dateStr = item.dt_txt.slice(0, 10); // 'YYYY-MM-DD'
    if (!res[dateStr]) res[dateStr] = [];
    res[dateStr].push(item);
  });
  return res;
});

// Os dias disponíveis para o seletor, exatamente como vêm da API
const availableDays = computed(() => Object.keys(groupedByDay.value).sort());

// Cidade
const cityLabel = computed(() => {
  const first = store.openWeatherForecast?.[0]?.city_name;
  return first ? `${first} - RS` : '';
});

// Formatações só pra visualização
function formatDayShort(dia) {
  // Exemplo: '2025-07-21' -> '21/07'
  if (!dia || !/^\d{4}-\d{2}-\d{2}$/.test(dia)) return dia;
  // eslint-disable-next-line no-unused-vars
  const [yyyy, mm, dd] = dia.split('-');
  return `${dd}/${mm}`;
}

function formatDay(dia) {
  if (!dia || !/^\d{4}-\d{2}-\d{2}$/.test(dia)) return dia;
  const [yyyy, mm, dd] = dia.split('-');
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const dow = new Date(`${yyyy}-${mm}-${dd}T12:00:00-03:00`).getDay();
  return `${dd}/${mm} (${dias[dow]})`;
}

// Inicializa o primeiro dia disponível
onMounted(async () => {
  await store.fetchOpenWeatherForecast();
  // eslint-disable-next-line prefer-destructuring
  if (availableDays.value.length) selectedDay.value = availableDays.value[0];
});
</script>

<style scoped>
.weather-sky-bg {
  background: linear-gradient(180deg, #b3d8ff 0%, #e8f4fd 100%);
  border-radius: 24px;
  overflow: visible;
  position: relative;
  box-shadow: 0 4px 24px #0e579e11;
}
</style>
