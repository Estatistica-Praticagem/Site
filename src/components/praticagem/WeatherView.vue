<template>
  <div style="margin-bottom:0;">
    <div style="position: relative;">
      <!-- Botão de três pontinhos no canto superior direito -->
      <q-btn
        dense flat round
        icon="more_vert"
        style="position: absolute; top: 6px; right: 6px; z-index:2;"
        @click="showConfig = true"
      />
      <!-- Card principal do tempo -->
      <q-card
        class="weather-main-card q-pa-none shadow-2"
        v-if="!!weather"
        :class="statusClass"
      >
        <!-- Infos -->
        <div class="weather-col-info">
          <div class="text-h6 q-mb-xs flex items-center gap-1">
            <q-icon name="cloud" class="q-mr-xs"/>Condições atuais:
          </div>
          <div class="text-caption q-mb-xs station-label">ESTAÇÃO METEOROLÓGICA</div>
          <div class="row q-gutter-md">
            <div v-if="settings.showTemp">
              <div class="text-caption">TEMPERATURA</div>
              <div class="text-bold">{{ weather.temperatura ?? '--' }}°</div>
            </div>
            <div v-if="settings.showTemp">
              <div class="text-caption">SENSAÇÃO TÉRMICA</div>
              <div class="text-bold">{{ weather.sensacaotermica ?? weather.sensacao ?? '--' }}°</div>
            </div>
            <div v-if="settings.showPressao">
              <div class="text-caption">PRESSÃO</div>
              <div class="text-bold">{{ weather.pressao ?? '--' }} mb</div>
            </div>
            <div v-if="settings.showUmidade">
              <div class="text-caption">UMIDADE</div>
              <div class="text-bold">{{ weather.umidade ?? '--' }}%</div>
            </div>
            <div>
              <div class="text-caption">VENTO</div>
              <div class="text-bold">
                {{ ventokts }}kts {{ windDirLabel }}
              </div>
            </div>
            <div v-if="settings.showMare">
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
          <div class="gauge-group" v-if="settings.showCorrenteza">
            <div class="gauge-title">Correnteza 3m</div>
            <GaugeRelogio
              :value="correntezaDir"
              :intensidade="correntezakts"
              :max="4"
              :unidade="'kts'"
              colorMain="#1976D2"
              colorSecondary="#43A047"
              colorBg="#E3F2FD"
              :size="settings.sizeCorrenteza"
            />
          </div>
          <div class="gauge-group" v-if="settings.showVento">
            <div class="gauge-title">Vento</div>
            <WindRose
              :direction="ventoDir"
              :intensidade="ventokts"
              :max="40"
              :unidade="'kts'"
              :size="settings.sizeVento"
              :lang="settings.siglaEN ? 'en' : 'pt'"
            />
          </div>
        </div>
      </q-card>
      <!-- Painel de Configurações -->
      <WeatherViewConfig
        v-model="showConfig"
        @update:settings="onConfigUpdate"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useWeatherStore } from 'src/stores/weather';
import GaugeRelogio from 'src/components/praticagem/watch/GaugeRelogio.vue';
import WindRose from 'src/components/praticagem/watch/WindRose.vue';
import WeatherViewConfig from 'src/components/praticagem/WeatherViewConfig.vue';

const showConfig = ref(false);

const defaultSettings = {
  siglaEN: false,
  showVento: true,
  showCorrenteza: true,
  sizeVento: 90,
  sizeCorrenteza: 90,
  showTemp: true,
  showPressao: true,
  showUmidade: true,
  showMare: true,
};

const settings = ref({ ...defaultSettings });

// Carrega config ao abrir o painel
function loadConfig() {
  try {
    const data = JSON.parse(localStorage.getItem('weatherPanelConfig'));
    if (data) Object.assign(settings.value, data);
  // eslint-disable-next-line no-empty
  } catch {}
}
onMounted(loadConfig);

// Recebe atualização do filho (config)
function onConfigUpdate(newSettings) {
  Object.assign(settings.value, newSettings);
}

const { weatherLast: weather } = storeToRefs(useWeatherStore());

// Preferencialmente use sempre a sigla original (backend), ou grau se não houver.
const ventoDirCardinal = computed(() => weather.value?.ventodirecao || weather.value?.vento_dir || null);

// GRAU para a flecha SEMPRE: usa ventonum do backend se válido, senão tenta converter a sigla
const ventoDir = computed(() => {
  const vnum = weather.value?.ventonum;
  // eslint-disable-next-line no-restricted-globals
  if (typeof vnum === 'number' && !isNaN(vnum)) return vnum;
  // eslint-disable-next-line no-use-before-define
  return cardinalToDegree(ventoDirCardinal.value);
});

// Mostra sempre a sigla do backend (exata), fallback para cardinal se só veio grau
const windDirLabel = computed(() => (
  ventoDirCardinal.value
    ? ` ${ventoDirCardinal.value.toUpperCase()}`
    : (typeof weather.value?.ventonum === 'number'
      // eslint-disable-next-line no-use-before-define
      ? degreeToCardinal(weather.value.ventonum)
      : '')
));

// Função universal para converter cardinal para graus meteorológicos
function cardinalToDegree(cardinal) {
  if (!cardinal || typeof cardinal !== 'string') return 0;
  const c = cardinal.trim().toUpperCase().replace(/[^A-Z]/g, '');
  const map = {
    N: 0,
    NNE: 22.5,
    NE: 45,
    ENE: 67.5,
    E: 90,
    ESE: 112.5,
    SE: 135,
    SSE: 157.5,
    S: 180,
    SSO: 202.5,
    SO: 225,
    OSO: 247.5,
    O: 270,
    ONO: 292.5,
    NO: 315,
    NNO: 337.5,
    W: 270,
    WSW: 247.5,
    SW: 225,
    SSW: 202.5,
    NW: 315,
    NNW: 337.5,
    WNW: 292.5,
    NORTE: 0,
    NORDESTE: 45,
    LESTE: 90,
    SUDESTE: 135,
    SUL: 180,
    SUDOESTE: 225,
    OESTE: 270,
    NOROESTE: 315,
    NORTH: 0,
    NORTHEAST: 45,
    EAST: 90,
    SOUTHEAST: 135,
    SOUTH: 180,
    SOUTHWEST: 225,
    WEST: 270,
    NORTHWEST: 315,
  };
  if (map[c] !== undefined) return map[c];
  console.warn('SIGLA DE VENTO DESCONHECIDA:', cardinal);
  return 0;
}

// Fallback: grau para cardinal
function degreeToCardinal(deg) {
  if (deg == null || deg === '--') return '--';
  const ptDirs = ['N', 'NL', 'L', 'SL', 'S', 'SO', 'O', 'NO', 'N'];
  const enDirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  const dirs = settings.value.siglaEN ? enDirs : ptDirs;
  return dirs[Math.round((deg % 360) / 45)];
}

// Status e computadas
const statusText = computed(() => String(weather.value?.status || '')
  .toUpperCase()
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, ''));

const statusClass = computed(() => {
  switch (statusText.value) {
    case 'PRATICAVEL': return 'weather-status-praticavel';
    case 'IMPRATICAVEL TOTAL': return 'weather-status-impraticavel-total';
    case 'PRATICABILIDADE EM ANALISE': return 'weather-status-analise';
    case 'PARCIALMENTE IMPRATICAVEL': return 'weather-status-parcial';
    case 'IMPOSSIBILITADA DE EMBARQUE E DESEMBARQUE': return 'weather-status-impossibilitada';
    case 'EM NORMALIZACAO': return 'weather-status-normalizacao';
    default: return 'weather-status-default';
  }
});

const statusStyle = computed(() => {
  const padrao = {
    PRATICAVEL: 'blue-7',
    'IMPRATICAVEL TOTAL': 'cyan-6',
    'PRATICABILIDADE EM ANALISE': 'amber-7',
    'PARCIALMENTE IMPRATICAVEL': 'pink-5',
    'IMPOSSIBILITADA DE EMBARQUE E DESEMBARQUE': 'amber-6',
    'EM NORMALIZACAO': 'deep-purple-6',
  };
  const custom = {
    PRATICAVEL: 'green',
    'IMPRATICAVEL TOTAL': 'red',
    'PRATICABILIDADE EM ANALISE': 'amber',
    'PARCIALMENTE IMPRATICAVEL': 'amber',
    'IMPOSSIBILITADA DE EMBARQUE E DESEMBARQUE': 'amber',
    'IMPRATICABILIDADE EM ANALISE': 'amber',
    'EM NORMALIZACAO': 'amber',
  };
  const tipo = settings.value.statusColors || 'custom';
  return {
    badge: (tipo === 'custom' ? custom : padrao)[statusText.value] || 'grey-5',
  };
});

const correntezaDir = computed(() => parseFloat(weather.value?.direcao_3m ?? 0));
const correntezakts = computed(() => {
  const val = parseFloat(weather.value?.intensidade_3m ?? 0);
  // eslint-disable-next-line no-restricted-globals
  return isNaN(val) ? 0 : +val.toFixed(2);
});
const ventokts = computed(() => {
  const val = parseFloat(weather.value?.ventointensidade ?? weather.value?.vento_int ?? 0);
  // eslint-disable-next-line no-restricted-globals
  return isNaN(val) ? 0 : +val.toFixed(2);
});

onMounted(() => {
  loadConfig();
});
</script>

<style scoped>
/* ===== CORES POR STATUS ===== */
.weather-status-praticavel {
  --weather-bg: #e7fbe9;
  --weather-text: #146634;
  --weather-caption: #249682;
  --weather-icon: #177d42;
}
.weather-status-impraticavel-total {
  --weather-bg: #e0f8fa;
  --weather-text: #045d64;
  --weather-caption: #2595a3;
  --weather-icon: #01707a;
}
.weather-status-analise {
  --weather-bg: #fff9e3;
  --weather-text: #755100;
  --weather-caption: #997d2e;
  --weather-icon: #a68710;
}
.weather-status-parcial {
  --weather-bg: #fde5f0;
  --weather-text: #89034a;
  --weather-caption: #be4584;
  --weather-icon: #ba0f5e;
}
.weather-status-impossibilitada {
  --weather-bg: #fffce6;
  --weather-text: #7e6507;
  --weather-caption: #eab600;
  --weather-icon: #eab600;
}
.weather-status-normalizacao {
  --weather-bg: #efe8fa;
  --weather-text: #4527a0;
  --weather-caption: #7654b7;
  --weather-icon: #4527a0;
}
.weather-status-default {
  --weather-bg: #f9f9f9;
  --weather-text: #222;
  --weather-caption: #666;
  --weather-icon: #333;
}

.weather-main-card {
  display: flex;
  flex-direction: row;
  min-height: 160px;
  max-width: 830px;
  width: 100%;
  margin: 0 auto 12px auto;
  border-radius: 18px;
  background: var(--weather-bg) !important;
  color: var(--weather-text) !important;
  transition: background .22s, color .18s;
}
.weather-col-info {
  flex: 1 1 0;
  min-width: 0;
  padding-bottom: 12px;
  padding: 18px 0 0 24px;    /* <<< Aqui dá espaço lateral e superior extra */
}
.weather-main-card,
.weather-col-info,
.weather-main-card .text-h6,
.weather-main-card .text-bold {
  color: var(--weather-text) !important;
}
.weather-main-card .text-caption,
.weather-main-card .station-label {
  color: var(--weather-caption) !important;
}
.weather-main-card .q-icon {
  color: var(--weather-icon) !important;
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
    padding: 12px 0 0 10px;   /* <<< Espaço lateral/superior no mobile */
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
