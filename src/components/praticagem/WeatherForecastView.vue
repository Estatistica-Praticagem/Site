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
          { label: 'Tabela', value: 'table', icon: 'table_chart' },
          { label: 'Cartões', value: 'cards', icon: 'view_module' },
          { label: 'Linha', value: 'bar', icon: 'horizontal_split' },
          { label: 'Grade', value: 'grid', icon: 'grid_on' }
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
    <WeatherForecastTable v-if="viewMode === 'table'" :rows="groupedByDay[selectedDay] || []" />
    <WeatherForecastCards v-else-if="viewMode === 'cards'" :rows="groupedByDay[selectedDay] || []" />
    <WeatherForecastBar v-else-if="viewMode === 'bar'" :rows="groupedByDay[selectedDay] || []" />
    <WeatherForecastGrid v-else-if="viewMode === 'grid'"
      :day="selectedDay"
      :groupedByDay="groupedByDay"
      :cityLabel="cityLabel"
      :availableDays="availableDays"
      @update:day="selectedDay = $event"
    />
  </q-card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useWeatherStore } from 'src/stores/weather';

// Importando os componentes de visualização:
import WeatherForecastTable from 'src/components/praticagem/WeatherForecast/WeatherForecastTable.vue';
import WeatherForecastCards from 'src/components/praticagem/WeatherForecast/WeatherForecastCards.vue';
import WeatherForecastBar from 'src/components/praticagem/WeatherForecast/WeatherForecastBar.vue';
import WeatherForecastGrid from 'src/components/praticagem/WeatherForecast/WeatherForecastGrid.vue';

const store = useWeatherStore();
const selectedDay = ref(null);
const viewMode = ref('table');

onMounted(async () => {
  await store.fetchOpenWeatherForecast();
  // eslint-disable-next-line prefer-destructuring, no-use-before-define
  if (availableDays.value.length) selectedDay.value = availableDays.value[0];
});

const groupedByDay = computed(() => {
  if (!Array.isArray(store.openWeatherForecast)) return {};
  return store.openWeatherForecast.reduce((acc, item) => {
    const dia = item.dt_txt ? item.dt_txt.slice(0, 10) : 'Indefinido';
    if (!acc[dia]) acc[dia] = [];
    acc[dia].push(item);
    return acc;
  }, {});
});
const availableDays = computed(() => Object.keys(groupedByDay.value).sort());
const cityLabel = computed(() => {
  const first = store.openWeatherForecast?.[0]?.city_name;
  return first ? `${first} - RS` : '';
});
function formatDay(dia) {
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const d = new Date(dia);
  // eslint-disable-next-line no-restricted-globals
  if (isNaN(d)) return dia;
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')} (${dias[d.getDay()]})`;
}
function formatDayShort(dia) {
  const d = new Date(dia);
  // eslint-disable-next-line no-restricted-globals
  if (isNaN(d)) return dia;
  return `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth() + 1).toString().padStart(2, '0')}`;
}
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
