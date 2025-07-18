<template>
  <div style="margin-bottom:0;">
    <q-card
      class="weather-main-card q-pa-none shadow-2"
      v-if="!!weather"
      :style="{
        background: statusStyle.bg,
        color: statusStyle.text,
      }"
    >
      <!-- Infos -->
      <div class="weather-col-info q-pa-md" :style="{ color: statusStyle.text }">
        <div class="text-h6 q-mb-xs flex items-center gap-1">
          <q-icon name="cloud" class="q-mr-xs" :style="{ color: statusStyle.icon }"/>Condições atuais:
        </div>
        <div class="text-caption q-mb-xs" :style="{ color: statusStyle.caption }">ESTAÇÃO METEOROLÓGICA</div>
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
              {{ ventoKts }} kt {{ windDirLabel }}
            </div>
          </div>
          <div>
            <div class="text-caption">PRESSÃO</div>
            <div class="text-bold">{{ weather.pressao ?? '--' }} mb</div>
          </div>
        </div>
        <div class="row q-mt-xs">
          <div class="q-mr-md">
            <div class="text-caption">UMIDADE</div>
            <div class="text-bold">{{ weather.umidade ?? '--' }}%</div>
          </div>
          <div>
            <div class="text-caption">ALTURA REAL DA MARÉ</div>
            <div class="text-bold text-primary">
              {{ weather.altura_real_getmare ?? '--' }} m
            </div>
          </div>
        </div>
        <div class="row q-mt-xs">
          <div class="q-mr-md">
            <div class="text-caption">LEITURA</div>
            <div class="text-bold">
              {{ weather.timestamp_br?.date
                ? new Date(weather.timestamp_br.date).toLocaleString('pt-BR')
                : (weather.leitura ?? '--') }}
            </div>
          </div>
          <div>
            <div class="text-caption">STATUS</div>
            <q-badge
              :color="statusStyle.badge"
              align="top"
              class="q-ml-xs q-mt-xs text-bold"
              style="font-size:1.1em;padding:3px 14px;border-radius:9px;"
            >
              {{ weather.status ?? '--' }}
            </q-badge>
          </div>
        </div>
      </div>
      <!-- Blocos: Gauge Correnteza 3m e Rosa do Vento -->
      <div class="weather-col-gauges">
        <div class="gauge-group">
          <div class="gauge-title" :style="{ color: statusStyle.text }">Correnteza 3m</div>
          <GaugeRelogio
            :value="correntezaDir"
            :intensidade="correntezaKts"
            :max="4"
            :unidade="'kt'"
            colorMain="#1976D2"
            colorSecondary="#43A047"
            colorBg="#E3F2FD"
            size="90"
          />
        </div>
        <div class="gauge-group">
          <div class="gauge-title" :style="{ color: statusStyle.text }">Vento</div>
          <WindRose
            :direcao="ventoDir"
            :intensidade="ventoKts"
            :max="40"
            :unidade="'kt'"
            size="90"
          />
        </div>
      </div>
    </q-card>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import GaugeRelogio from 'components/praticagem/GaugeRelogio.vue';
import WindRose from 'components/praticagem/WindRose.vue';
import { computed } from 'vue';

// Pegando o último registro do weather
const { weatherLast: weather } = storeToRefs(useWeatherStore());

// Mapa de cores por status
const statusColorMap = {
  PRATICAVEL: {
    bg: '#E9F7EF',
    text: '#145A32',
    badge: 'blue-7',
    caption: '#31708F',
    icon: '#145A32',
  },
  'IMPRATICAVEL TOTAL': {
    bg: '#E0F7FA',
    text: '#006064',
    badge: 'cyan-6',
    caption: '#006064',
    icon: '#006064',
  },
  'PRATICABILIDADE EM ANALISE': {
    bg: '#FFF8E1',
    text: '#6D4C00',
    badge: 'amber-7',
    caption: '#6D4C00',
    icon: '#6D4C00',
  },
  'PARCIALMENTE IMPRATICAVEL': {
    bg: '#FCE4EC',
    text: '#880E4F',
    badge: 'pink-5',
    caption: '#880E4F',
    icon: '#880E4F',
  },
  'IMPOSSIBILITADA DE EMBARQUE E DESEMBARQUE': {
    bg: '#FFFDE7',
    text: '#7E5109',
    badge: 'amber-6',
    caption: '#7E5109',
    icon: '#7E5109',
  },
  'IMPRATICABILIDADE EM ANALISE': {
    bg: '#F1F8E9',
    text: '#33691E',
    badge: 'light-green-7',
    caption: '#33691E',
    icon: '#33691E',
  },
  'EM NORMALIZACAO': {
    bg: '#EDE7F6',
    text: '#4527A0',
    badge: 'deep-purple-6',
    caption: '#4527A0',
    icon: '#4527A0',
  },
  DEFAULT: {
    bg: '#f9f9f9',
    text: '#333',
    badge: 'grey-5',
    caption: '#666',
    icon: '#222',
  },
};

// Normaliza status do painel para usar como chave do mapa
const statusText = computed(() => String(weather.value?.status || '')
  .toUpperCase()
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, ''));

const statusStyle = computed(() => statusColorMap[statusText.value] || statusColorMap.DEFAULT);

// Correnteza 3m (em knots), agora igual aos relógios do grid:
const correntezaDir = computed(() => parseFloat(weather.value?.direcao_3m ?? 0));
const correntezaKts = computed(() => {
  const val = parseFloat(weather.value?.intensidade_3m ?? 0);
  // eslint-disable-next-line no-restricted-globals
  return isNaN(val) ? 0 : +val.toFixed(2);
});

// Vento em knots (converte m/s caso valor baixo)
const ventoDir = computed(() =>
  // cobre as principais variantes do seu dataset
  // eslint-disable-next-line implicit-arrow-linebreak
  parseFloat(
    weather.value?.ventodir
    ?? weather.value?.ventodirecao
    ?? weather.value?.ventonum
    ?? weather.value?.vento_dir
    ?? 0,
  ));
const ventoKts = computed(() => {
  const val = parseFloat(weather.value?.ventointensidade ?? weather.value?.vento_int ?? 0);
  // eslint-disable-next-line no-restricted-globals
  return isNaN(val) ? 0 : +val.toFixed(2);
});

// Direção do vento para label brasileiro
function getWindDirLabel(deg) {
  if (deg == null || deg === '--') return '--';
  const dirs = ['N', 'NL', 'L', 'SL', 'S', 'SO', 'O', 'NO', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}
const windDirLabel = computed(() => getWindDirLabel(ventoDir.value));
</script>

<style scoped>
.weather-main-card {
  display: flex;
  flex-direction: row;
  min-height: 160px;
  max-width: 830px;
  width: 100%;
  margin: 0 auto 12px auto;
  border-radius: 18px;
  transition: background .22s, color .18s;
}
.weather-col-info {
  flex: 1 1 0;
  min-width: 0;
  padding-bottom: 12px;
}
.weather-col-gauges {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #fff;
  min-width: 164px;
  border-radius: 0 18px 18px 0;
  padding: 0 14px;
  gap: 10px;
}
.gauge-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2px;
}
.gauge-title {
  font-size: .99em;
  font-weight: 500;
  margin-bottom: 2px;
  letter-spacing: .01em;
}
.text-bold { font-weight: bold; }
@media (max-width: 650px) {
  .weather-main-card {
    flex-direction: column;
    align-items: stretch;
    min-height: 0;
    max-width: 98vw;
  }
  .weather-col-info {
    padding-bottom: 0;
  }
  .weather-col-gauges {
    min-width: unset;
    border-radius: 0 0 18px 18px;
    padding: 14px 0 12px 0;
    flex-direction: row;
    gap: 22px;
  }
  .gauge-group { margin-bottom: 0; }
}
</style>
