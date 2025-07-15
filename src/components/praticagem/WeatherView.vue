<template>
  <div style="margin-bottom:0;">
    <q-card
      class="weather-main-card q-pa-none shadow-2"
      v-if="!!weather"
    >
      <!-- Bloco de Dados -->
      <div class="weather-col-info q-pa-md">
        <div class="text-h6 q-mb-xs">
          <q-icon name="cloud" class="q-mr-xs" />Condições atuais:
        </div>
        <div class="text-caption text-grey-7 q-mb-xs">ESTAÇÃO METEOROLÓGICA</div>
        <div class="row q-gutter-md">
          <div>
            <div class="text-caption">TEMPERATURA</div>
            <div class="text-bold">{{ weather.temperatura ?? '--' }}°</div>
          </div>
          <div>
            <div class="text-caption">SENSAÇÃO TÉRMICA</div>
            <div class="text-bold">{{ weather.sensacaotermica ?? weather.sensacao ?? '--' }}°</div>
          </div>
          <div>
            <div class="text-caption">VENTO</div>
            <div class="text-bold">
              {{ weather.ventointensidade ?? weather.vento_int ?? '--' }} kt {{ weather.ventodir ?? weather.ventonum ?? weather.vento_dir ?? '--' }}
            </div>
          </div>
          <div>
            <div class="text-caption">PRESSÃO</div>
            <div class="text-bold">{{ weather.pressao ?? '--' }} mb</div>
          </div>
        </div>
        <div class="row q-mt-sm">
          <div class="q-mr-md">
            <div class="text-caption">UMIDADE</div>
            <div class="text-bold">{{ weather.umidade ?? '--' }}%</div>
          </div>
          <div>
            <div class="text-caption">LEITURA</div>
            <div class="text-bold">
              {{ weather.timestamp_br?.date
                ? new Date(weather.timestamp_br.date).toLocaleString('pt-BR')
                : (weather.leitura ?? '--') }}
            </div>
          </div>
        </div>
      </div>
      <!-- Bloco Relógio 3m -->
      <div class="weather-col-gauge">
        <CurrentGauge3m
          :direcao="weather.direcao_3m ?? weather.corrente_dir"
          :intensidade="weather.intensidade_3m ?? weather.corrente_int"
        />
      </div>
    </q-card>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import CurrentGauge3m from 'components/praticagem/GaugeRelogio.vue';

const store = useWeatherStore();
const { weatherLast: weather } = storeToRefs(store);
</script>

<style scoped>
.weather-main-card {
  display: flex;
  flex-direction: row;
  background: #f8e3e7;
  min-height: 150px;
  max-width: 750px;
  width: 100%;
  margin: auto;
  border-radius: 18px;
}
.weather-col-info {
  flex: 1 1 0;
  min-width: 0;
  padding-bottom: 12px;
}
.weather-col-gauge {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  min-width: 220px;
  border-radius: 0 18px 18px 0;
  padding: 0 22px;
}
/* Responsivo: empilha (relógio abaixo) */
@media (max-width: 650px) {
  .weather-main-card {
    flex-direction: column;
    align-items: stretch;
    min-height: 0;
    max-width: 97vw;
  }
  .weather-col-info {
    padding-bottom: 0;
  }
  .weather-col-gauge {
    min-width: unset;
    border-radius: 0 0 18px 18px;
    padding: 16px 0 12px 0;
  }
}
.text-bold { font-weight: bold; }
</style>
