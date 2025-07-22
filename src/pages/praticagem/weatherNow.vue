<template>
  <q-page class="bg-grey-2 q-pa-none" style="min-height:unset;padding:0;max-width:1200px;margin:auto;">
    <div style="height:22px"></div>
    <WeatherView />
    <div style="height:22px"></div>
    <WeatherDetails />
    <div style="height:22px"></div>
    <TideForecastComparison />
    <div style="height:22px"></div>
    <CurrentForecastComparison/>
    <div style="height:22px"></div>
    <OpenWeatherForecast/>
    <div style="height:22px"></div>
    <WeatherForecastVisibility/>
    <div style="height:22px"></div>
    <VerticalCurrentProfile/>
    <div style="height:22px"></div>
    <FooterPraticagem/>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue';
import { useWeatherStore } from 'src/stores/weather';
import WeatherView from 'src/components/praticagem/WeatherView.vue';
import WeatherDetails from 'src/components/praticagem/WeatherDetails.vue';
import TideForecastComparison from 'src/components/praticagem/TideForecastComparison.vue';
import CurrentForecastComparison from 'src/components/praticagem/CurrentForecastComparison.vue';
import OpenWeatherForecast from 'src/components/praticagem/WeatherForecastView.vue';
import WeatherForecastVisibility from 'src/components/praticagem/WeatherForecastVisibility.vue';
import FooterPraticagem from 'src/components/praticagem/FooterPraticagem.vue';
import VerticalCurrentProfile from 'src/components/praticagem/VerticalCurrentProfile.vue';

const store = useWeatherStore();

onMounted(() => {
  store.fetchLast();
  store.fetchHistory();
  store.fetchForecast();
  store.fetchOpenWeatherForecast();
});
</script>
