<template>
  <div style="margin-bottom:0;">
    <!-- Botão: Relógios ou Gráficos -->
    <div class="q-mb-md flex items-center gap-3 justify-center">
      <q-btn-toggle
        v-model="viewMode"
        unelevated
        toggle-color="primary"
        color="white"
        text-color="primary"
        :options="[
          { label: 'Relógios', value: 'gauges' },
          { label: 'Gráficos', value: 'profile' }
        ]"
      />
    </div>

    <!-- Gráfico de Perfil Vertical -->
    <VerticalCurrentProfile
      v-if="viewMode === 'profile'"
      :weather="weather"
      :corrente-campos="filteredCampos"
    />

    <!-- Relógios (Gauges) -->
    <div v-else>
      <div class="row items-center justify-between q-mb-xs" style="max-width:1100px;margin:auto;">
        <div class="text-h6 text-weight-medium" style="color:#0267C1">
          ADCP - Correntezas (kts)
        </div>
        <q-btn
          flat round dense
          icon="more_vert"
          @click="showConfig = true"
          aria-label="Configurações"
          class="q-ml-xs"
        />
      </div>

      <div class="row relogio-grid">
        <div
          v-for="info in filteredCampos"
          :key="info.label"
          class="g-rel-col"
          :style="{
            minWidth: cardDims.cardW + 'px',
            maxWidth: cardDims.cardW + 'px',
          }"
        >
          <q-card
            flat
            bordered
            class="relogio-card"
            :style="{
              minHeight: cardDims.cardMinH + 'px',
              maxWidth: cardDims.cardW + 'px',
              width: cardDims.cardW + 'px'
            }"
          >
            <div class="text-bold text-primary q-mb-xs relogio-label">
              {{ info.label }}
              <span v-if="dirSigla(info)">
                <q-tooltip anchor="top middle">{{ dirTooltip(info) }}</q-tooltip>
                <span class="q-ml-xs" style="font-size:0.94em;">({{ dirSigla(info) }})</span>
              </span>
            </div>

            <CurrentGauge
              :intensidade="parseFloat(weather[info.int] ?? 0)"
              :value="parseFloat(weather[info.dir] ?? 0)"
              :max="6"
              unidade="kts"
              :size="computedGaugeSize"
              :lang="config.dirLang"
            />
          </q-card>
        </div>
      </div>
    </div>

    <!-- Dialog de Configurações -->
    <q-dialog v-model="showConfig" persistent>
      <q-card style="min-width:350px;max-width:98vw;">
        <q-card-section class="row items-center q-pa-sm">
          <div class="text-h6 text-primary">Configurações dos Relógios</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showConfig = false" />
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="q-mb-sm">
            <div class="text-bold q-mb-xs">Direção:</div>
            <q-option-group
              v-model="config.dirLang"
              :options="[
                { label: 'Português', value: 'pt' },
                { label: 'Inglês', value: 'en' }
              ]"
              color="primary"
              type="radio"
            />
          </div>

          <div class="q-mb-sm">
            <div class="text-bold q-mb-xs">Tamanho dos Relógios:</div>
            <q-option-group
              v-model="config.gaugeSizeMode"
              :options="[
                { label: 'Pequeno', value: 'sm' },
                { label: 'Médio', value: 'md' },
                { label: 'Grande', value: 'lg' },
                { label: 'Personalizado', value: 'custom' }
              ]"
              color="primary"
              type="radio"
            />
            <div v-if="config.gaugeSizeMode === 'custom'" class="q-mt-md q-mb-xs">
              <div style="font-size:.98em;">Tamanho: <b>{{ config.gaugeSizeCustom }} px</b></div>
              <q-slider
                v-model="config.gaugeSizeCustom"
                :min="70"
                :max="170"
                :step="1"
                color="primary"
                label
                label-always
                snap
                style="max-width:220px;margin-left:8px;"
              />
            </div>
          </div>

          <div>
            <div class="text-bold q-mb-xs">Profundidades exibidas:</div>
            <q-option-group
              v-model="config.visibleProfs"
              :options="profOptions"
              color="primary"
              type="checkbox"
              :keep-color="true"
            />
          </div>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
          <q-btn flat label="Salvar" color="primary" @click="saveConfig" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Info detalhada - status -->
    <q-card class="q-pa-md bg-white shadow-2" style="border-radius:16px;max-width:1200px;margin:auto;">
      <div class="text-h6 text-weight-bold text-primary">Situação</div>
      <div class="text-body1 q-mb-xs">
        <b>{{ weather.status ?? '--' }}</b>
        <span v-if="weather.motivo" class="q-ml-sm text-negative">
          ({{ weather.motivo }})
        </span>
      </div>
      <div class="text-caption text-grey-8">Leitura: {{ weather.timestamp_br?.date ?? '--' }}</div>
    </q-card>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { ref, computed, watch, onMounted } from 'vue';
import CurrentGauge from 'src/components/praticagem/watch/GaugeRelogio.vue';
import VerticalCurrentProfile from 'src/components/praticagem/VerticalCurrentProfile.vue';
import { useWeatherStore } from 'src/stores/weather';

const { weatherLast: weather } = storeToRefs(useWeatherStore());
const viewMode = ref('gauges');
const showConfig = ref(false);

// Campos disponíveis
const correnteCamposAll = [
  { label: 'Superfície', int: 'intensidade_superficie', dir: 'direcao_superficie', deg: null, ench: null, intAj: null, profKey: 'superficie' },
  { label: '1.5m', int: 'intensidade_1_5m', dir: 'direcao_1_5m', deg: 'direcao_1_5m_deg', ench: 'enchente_vazante_1_5m', intAj: 'intensidade_1_5m_ajustada', profKey: '1_5m' },
  { label: '3m', int: 'intensidade_3m', dir: 'direcao_3m', deg: 'direcao_3m_deg', ench: 'enchente_vazante_3m', intAj: 'intensidade_3m_ajustada', profKey: '3m' },
  { label: '6m', int: 'intensidade_6m', dir: 'direcao_6m', deg: 'direcao_6m_deg', ench: 'enchente_vazante_6m', intAj: 'intensidade_6m_ajustada', profKey: '6m' },
  { label: '7.5m', int: 'intensidade_7_5m', dir: 'direcao_7_5m', deg: 'direcao_7_5m_deg', ench: 'enchente_vazante_7_5m', intAj: 'intensidade_7_5m_ajustada', profKey: '7_5m' },
  { label: '9m', int: 'intensidade_9m', dir: 'direcao_9m', deg: 'direcao_9m_deg', ench: 'enchente_vazante_9m', intAj: 'intensidade_9m_ajustada', profKey: '9m' },
  { label: '10.5m', int: 'intensidade_10_5m', dir: 'direcao_10_5m', deg: 'direcao_10_5m_deg', ench: 'enchente_vazante_10_5m', intAj: 'intensidade_10_5m_ajustada', profKey: '10_5m' },
  { label: '12m', int: 'intensidade_12m', dir: 'direcao_12m', deg: 'direcao_12m_deg', ench: 'enchente_vazante_12m', intAj: 'intensidade_12m_ajustada', profKey: '12m' },
  { label: '13.5m', int: 'intensidade_13_5m', dir: 'direcao_13_5m', deg: 'direcao_13_5m_deg', ench: 'enchente_vazante_13_5m', intAj: 'intensidade_13_5m_ajustada', profKey: '13_5m' },
  { label: '15m', int: 'intensidade_15m', dir: 'direcao_15m', deg: 'direcao_15m_deg', ench: 'enchente_vazante_15m', intAj: 'intensidade_15m_ajustada', profKey: '15m' },
];

const profOptions = correnteCamposAll.map((c) => ({ label: c.label, value: c.profKey }));

const defaultConfig = () => ({
  dirLang: 'pt',
  gaugeSizeMode: 'md',
  gaugeSizeCustom: 100,
  visibleProfs: correnteCamposAll.map((c) => c.profKey),
});
const config = ref({ ...defaultConfig() });

onMounted(() => {
  const saved = localStorage.getItem('currentProfileConfig');
  if (saved) Object.assign(config.value, JSON.parse(saved));
});
const saveConfig = () => {
  localStorage.setItem('currentProfileConfig', JSON.stringify(config.value));
  showConfig.value = false;
};

const computedGaugeSize = computed(() => {
  if (config.value.gaugeSizeMode === 'sm') return 80;
  if (config.value.gaugeSizeMode === 'md') return 100;
  if (config.value.gaugeSizeMode === 'lg') return 130;
  if (config.value.gaugeSizeMode === 'custom') return config.value.gaugeSizeCustom;
  return 100;
});

// Dimensões do card acompanham o tamanho
const cardDims = computed(() => {
  const s = computedGaugeSize.value;
  return {
    cardW: Math.round(s + 20),
    cardMinH: Math.round(s + 58),
  };
});

const filteredCampos = computed(() => correnteCamposAll.filter((c) => config.value.visibleProfs.includes(c.profKey)));

function dirSigla(info) {
  if (!info.deg || !weather.value) return null;
  const sigla = weather.value[info.deg];
  return sigla || null;
}
function dirTooltip(info) {
  if (!info.deg || !weather.value) return '';
  const sigla = weather.value[info.deg];
  return sigla || '';
}

watch(config, () => {
  localStorage.setItem('currentProfileConfig', JSON.stringify(config.value));
}, { deep: true });
</script>

<style scoped>
.relogio-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem 0.4rem;
  justify-content: center; /* CENTRALIZADO */
  align-items: stretch;
  margin-bottom: 20px;
  max-width: 1100px;
  margin-left: auto;
  margin-right: auto;
}
.g-rel-col {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 2px;
  padding: 0 2px;
}
.relogio-card {
  border-radius: 13px;
  box-shadow: 0 1px 6px #e0e0e033;
  width: 100%;
  padding: 6px 2px 2px 2px;
  margin: 0;
}
.relogio-label {
  font-size: 1.08em;
  margin-top: 2px;
  margin-bottom: 0;
  color: #1565c0;
}
@media (max-width: 1100px) {
  .relogio-grid { max-width: 100vw; }
}
</style>
