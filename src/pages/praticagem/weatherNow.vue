<template>
  <q-page class="bg-grey-2 q-pa-none" style="min-height:unset;padding:0;max-width:1200px;margin:auto;">
    <WeatherView />
    <div style="height:22px"></div>
    <WeatherDetails />
    <TideForecastComparison />
    <CurrentForecastComparison/>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue';
import { useWeatherStore } from 'src/stores/weather';
import WeatherView from 'src/components/praticagem/WeatherView.vue';
import WeatherDetails from 'src/components/praticagem/WeatherDetails.vue';
import TideForecastComparison from 'src/components/praticagem/TideForecastComparison.vue';
import CurrentForecastComparison from 'src/components/praticagem/CurrentForecastComparison.vue';

const store = useWeatherStore();

onMounted(() => {
  // Só busca se ainda não carregou
  if (!store.weatherLast) store.fetchLast();
  if (!store.weatherHistory.length) store.fetchHistory();
});
</script>
