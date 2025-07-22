<template>
  <div style="margin-bottom:0;">
    <!-- Botão: Relógios ou Tabela -->
    <div class="q-mb-md flex items-center gap-3 justify-center">
      <q-btn-toggle
        v-model="viewMode"
        unelevated
        toggle-color="primary"
        color="white"
        text-color="primary"
        :options="[
          { label: 'Relógios', value: 'gauges' },
          { label: 'Tabela', value: 'table' }
        ]"
      />
    </div>

    <!-- Tabela de Intensidades -->
    <q-card
      v-if="viewMode === 'table'"
      flat
      class="bg-white shadow-2 q-pa-sm q-mb-md"
      style="border-radius:12px; max-width:520px;margin:auto;"
    >
      <div class="text-bold q-mb-xs" style="color:#1976D2;font-size:1.09em;">ADCP Vazante/Ajustada (kts)</div>
      <q-table
        dense flat hide-bottom
        :rows="adcpVazanteRows"
        :columns="[
          {name:'prof',label:'Profundidade',field:'prof',align:'center'},
          {name:'int',label:'Intensidade',field:'int',align:'center'},
          {name:'status',label:'Status',field:'status',align:'center'},
        ]"
        :pagination="{rowsPerPage:12}"
        class="q-mb-none"
      />
    </q-card>

    <!-- Relógios: Rosa dos Ventos + Correntezas -->
    <div v-else>
      <!-- Rosa dos Ventos / Relógio de Vento -->
      <!-- <div class="row items-center justify-center q-mb-sm wind-gauge-wrap">
        <q-card flat bordered class="wind-gauge-card">
          <div class="text-bold text-primary q-mb-xs" style="text-align:center;font-size:1.09em">
            Vento Médio
          </div>
          <WindGauge
            :value="parseFloat(weather.ventodirecao ?? 0)"
            :intensidade="parseFloat(weather.ventointensidade ?? 0)"
            :max="60"
            :size="120"
          />
          <div class="text-center text-grey-7 q-mt-xs" style="font-size:.92em">
            {{ weather.ventointensidade ?? '--' }} kts
            <span v-if="weather.ventodirecao" class="q-ml-xs" style="font-size:0.9em;">
              ({{ weather.ventodirecao }}°)
            </span>
          </div>
        </q-card>
      </div> -->

      <div class="text-h6 text-weight-medium q-mb-xs" style="color:#0267C1">
        ADCP - Correntezas (kts)
      </div>
      <div class="row relogio-grid">
        <div
          v-for="info in correnteCampos"
          :key="info.label"
          class="g-rel-col"
        >
          <q-card flat bordered class="relogio-card">
            <div class="text-bold text-primary q-mb-xs relogio-label">{{ info.label }}</div>
            <CurrentGauge
              :intensidade="parseFloat(weather[info.int] ?? 0)"
              :value="parseFloat(weather[info.dir] ?? 0)"
              :max="6"
              unidade="kts"
              :profundidade="info.label"
              :size="100"
            />

          </q-card>

        </div>
      </div>
    </div>

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
import { ref, computed } from 'vue';
// import WindGauge from 'components/praticagem/WindGauge.vue';
import CurrentGauge from 'src/components/praticagem/watch/GaugeRelogio.vue';
import { useWeatherStore } from 'src/stores/weather';

const { weatherLast: weather } = storeToRefs(useWeatherStore());
const viewMode = ref('gauges'); // default

const correnteCampos = [
  {
    label: '15m', int: 'intensidade_15m', dir: 'direcao_15m', ench: 'enchente_vazante_15m', intAj: 'intensidade_15m_ajustada',
  },
  {
    label: '13.5m', int: 'intensidade_13_5m', dir: 'direcao_13_5m', ench: 'enchente_vazante_13_5m', intAj: 'intensidade_13_5m_ajustada',
  },
  {
    label: '12m', int: 'intensidade_12m', dir: 'direcao_12m', ench: 'enchente_vazante_12m', intAj: 'intensidade_12m_ajustada',
  },
  {
    label: '10.5m', int: 'intensidade_10_5m', dir: 'direcao_10_5m', ench: 'enchente_vazante_10_5m', intAj: 'intensidade_10_5m_ajustada',
  },
  {
    label: '9m', int: 'intensidade_9m', dir: 'direcao_9m', ench: 'enchente_vazante_9m', intAj: 'intensidade_9m_ajustada',
  },
  {
    label: '7.5m', int: 'intensidade_7_5m', dir: 'direcao_7_5m', ench: 'enchente_vazante_7_5m', intAj: 'intensidade_7_5m_ajustada',
  },
  {
    label: '6m', int: 'intensidade_6m', dir: 'direcao_6m', ench: 'enchente_vazante_6m', intAj: 'intensidade_6m_ajustada',
  },
  {
    label: '3m', int: 'intensidade_3m', dir: 'direcao_3m', ench: 'enchente_vazante_3m', intAj: 'intensidade_3m_ajustada',
  },
  {
    label: '1.5m', int: 'intensidade_1_5m', dir: 'direcao_1_5m', ench: 'enchente_vazante_1_5m', intAj: 'intensidade_1_5m_ajustada',
  },
];

const adcpVazanteRows = computed(() => {
  if (!weather.value) return [];
  return correnteCampos.map((info) => {
    const ench = weather.value[info.ench];
    const intAj = weather.value[info.intAj];
    let status = '--';
    if (ench !== undefined && ench !== null) status = ench === -1 ? 'Vazante' : (ench === 1 ? 'Enchente' : '--');
    // NÃO converte, já está em kts!
    const intkts = intAj != null ? Number(intAj).toFixed(2) : '--';
    return {
      prof: info.label,
      int: intkts !== '--' ? `${intkts} kts` : '--',
      status,
    };
  });
});

</script>

<style scoped>
.relogio-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem 0.3rem;
  justify-content: flex-start;
  align-items: stretch;
  margin-bottom: 20px;
  max-width: 1100px;
  margin-left: auto;
  margin-right: auto;
}
.g-rel-col {
  flex: 1 1 120px;
  min-width: 110px;
  max-width: 155px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 2px;
  padding: 0 2px;
}
.relogio-card {
  border-radius: 13px;
  box-shadow: 0 1px 6px #e0e0e033;
  min-height: 164px;
  max-width: 145px;
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
.wind-gauge-wrap {
  margin-bottom: 0.4em;
}
.wind-gauge-card {
  border-radius: 14px;
  box-shadow: 0 2px 12px #b3b3b326;
  padding: 10px 14px 4px 14px;
  min-width: 150px;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 6px;
}
@media (max-width: 1100px) {
  .relogio-grid { max-width: 100vw; }
}
@media (max-width: 850px) {
  .relogio-grid { gap: 0.3rem 0.2rem; }
  .g-rel-col { min-width: 94px; max-width: 120px; }
  .relogio-card { min-height: 120px; max-width: 120px; }
}
@media (max-width: 700px) {
  .g-rel-col { min-width: 88px; max-width: 110px; }
  .relogio-card { min-height: 100px; max-width: 108px; }
}
</style>
