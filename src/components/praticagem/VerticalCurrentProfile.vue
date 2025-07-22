<template>
  <div class="cbp-wrap">
    <div class="cbp-card">
      <div class="cbp-header row items-center justify-between">
        <span class="cbp-title">Perfil de Correnteza por Profundidade</span>
        <q-btn dense flat round icon="more_vert" @click="showConfig = true" />
      </div>

      <!-- SELETOR DE VISUALIZAÇÃO -->
      <div class="cbp-view-select row items-center q-mb-md">
        <q-btn-toggle
          v-model="settings.view"
          :options="viewOptions"
          unelevated
          color="primary"
          size="sm"
          toggle-color="blue-7"
          dense
        />
      </div>

      <!-- VISOR PADRÃO: BARRAS (default) -->
      <div v-if="settings.view === 'bars'" class="cbp-graph-ct" :style="{ transform: `scale(${settings.scale})` }">
        <div class="cbp-bars">
          <div
            v-for="d in shownDepths"
            :key="d.key"
            class="cbp-bar-row"
          >
            <span class="cbp-bar-label">{{ d.label }}</span>
            <div class="cbp-bar-line">
              <div
                v-if="getBarVal(d.key) < 0"
                class="cbp-bar cbp-bar-enchente"
                :style="{ width: barWidth(getBarVal(d.key)) + 'px' }"
              ></div>
              <div
                v-if="getBarVal(d.key) > 0"
                class="cbp-bar cbp-bar-vazante"
                :style="{ width: barWidth(getBarVal(d.key)) + 'px' }"
              ></div>
              <span
                v-if="getBarVal(d.key) !== 0"
                class="cbp-bar-value"
                :style="{ left: getBarVal(d.key) < 0 ? '0' : '', right: getBarVal(d.key) > 0 ? '0' : '' }"
              >
                {{ Math.abs(getBarVal(d.key)).toFixed(2) }} kts
              </span>
            </div>
          </div>
        </div>
        <div class="cbp-legend row items-center">
          <span class="cbp-leg-enchente"></span> Enchente
          <span class="cbp-leg-vazante"></span> Vazante
          <span class="cbp-leg-label"> Intensidade (kts)</span>
        </div>
      </div>

      <!-- VISOR NOVO: SVG (current-graph) -->
      <div v-if="settings.view === 'current-graph'" class="cbp-current-svg-ct">
        <div class="current-bars-graph">
          <svg :width="width" :height="height">
            <line :x1="xZero" y1="20" :x2="xZero" :y2="height-10" stroke="#bbb" stroke-width="2"/>
            <g v-for="(row, idx) in currentBarData" :key="row.label">
              <rect
                :x="row.value >= 0 ? xZero : xZero + scale(row.value)"
                :y="yScale(idx)"
                :width="Math.abs(scale(row.value))"
                :height="barHeight"
                :fill="row.value >= 0 ? '#e57373' : '#1976d2'"
              />
              <text
                :x="row.value >= 0 ? xZero + Math.abs(scale(row.value)) + 4 : xZero + scale(row.value) - 38"
                :y="yScale(idx) + barHeight/2 + 5"
                font-size="12"
                fill="#222"
              >
                {{ row.value.toFixed(2) }} kts
              </text>
              <text
                x="12"
                :y="yScale(idx) + barHeight/2 + 5"
                font-size="14"
                fill="#222"
              >
                {{ row.label }}
              </text>
            </g>
          </svg>
          <div style="margin-top:8px; font-size:13px; color:#888;">
            <span style="color:#1976d2;">⬅ Enchente</span>
            &nbsp; Intensidade (kts) &nbsp;
            <span style="color:#e57373;">Vazante ➡</span>
          </div>
        </div>
      </div>

      <!-- VISOR: TABELA -->
      <div v-if="settings.view === 'table'" class="cbp-table-ct">
        <table class="cbp-table">
          <thead>
            <tr>
              <th>Profundidade</th>
              <th>Intensidade</th>
              <th>Tipo</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="d in shownDepths" :key="d.key">
              <td>{{ d.label }}</td>
              <td>{{ Math.abs(getBarVal(d.key)).toFixed(2) }} kts</td>
              <td>
                <span v-if="getBarVal(d.key) < 0" class="cbp-leg-enchente"></span>
                <span v-if="getBarVal(d.key) > 0" class="cbp-leg-vazante"></span>
                {{ getBarVal(d.key) > 0 ? 'Vazante' : getBarVal(d.key) < 0 ? 'Enchente' : '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAINEL DE CONFIGURAÇÃO -->
    <q-dialog v-model="showConfig">
      <q-card style="min-width:320px;max-width:94vw;">
        <q-card-section>
          <div class="text-h6">Configurações do Gráfico</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-slider
            v-model="settings.scale"
            :min="0.7" :max="1.5" step="0.01"
            label="Escala do Gráfico"
            :label-value="`${Math.round(settings.scale * 100)}%`"
            label-always
            class="q-mb-md"
          />
          <div class="q-mb-sm text-caption">Profundidades Visíveis</div>
          <div class="row q-gutter-xs q-mb-md">
            <q-checkbox
              v-for="d in allDepths"
              :key="d.key"
              v-model="settings.selectedDepths"
              :label="d.label"
              :val="d.key"
              dense
            />
          </div>
          <div class="text-caption">Modo de Visualização</div>
          <q-btn-toggle
            v-model="settings.view"
            :options="viewOptions"
            unelevated
            color="primary"
            size="sm"
            toggle-color="blue-7"
            dense
            class="q-mt-xs"
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Fechar" v-close-popup/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import {
  ref, computed, onMounted, watch,
} from 'vue';

// Simulação (troque por props/store/api real)
const profileData = {
  superficie: 0.5,
  '1_5m': 0.7,
  '3m': -0.3,
  '6m': -0.8,
  '7_5m': -1.2,
  '15m': -1.6,
};

const allDepths = [
  { label: 'SUP (0m)', key: 'superficie' },
  { label: '1,5m', key: '1_5m' },
  { label: '3m', key: '3m' },
  { label: '6m', key: '6m' },
  { label: '7,5m', key: '7_5m' },
  { label: '15m', key: '15m' },
];

const viewOptions = [
  { label: 'Barras', value: 'bars', icon: 'fas fa-grip-horizontal' },
  { label: 'Gráfico SVG', value: 'current-graph', icon: 'fas fa-align-left' },
  { label: 'Tabela', value: 'table', icon: 'fas fa-table' },
];

const defaultSettings = {
  scale: 1,
  selectedDepths: ['superficie', '3m', '6m'],
  view: 'bars', // <-- Default visor é o barras
};

const showConfig = ref(false);
const settings = ref({ ...defaultSettings });

function saveConfig() {
  localStorage.setItem('currentBarProfileConfig', JSON.stringify(settings.value));
}
onMounted(() => {
  const saved = localStorage.getItem('currentBarProfileConfig');
  if (saved) Object.assign(settings.value, JSON.parse(saved));
});
watch(settings, saveConfig, { deep: true });

const shownDepths = computed(() => allDepths.filter((d) => settings.value.selectedDepths.includes(d.key)));

function getBarVal(key) {
  return Number(profileData[key]) || 0;
}
function barWidth(val) {
  const max = 2;
  // eslint-disable-next-line no-mixed-operators
  return Math.abs(val) / max * 180;
}

// --- NOVO SVG --- //
const width = 390;
const height = 180;
const barHeight = 22;
const yScale = (idx) => 25 + idx * (barHeight + 5);
const xZero = 180;
const maxVal = computed(() => Math.max(...shownDepths.value.map((d) => Math.abs(getBarVal(d.key))), 1.3));
// eslint-disable-next-line no-mixed-operators
const scale = (v) => v / maxVal.value * 100;

const currentBarData = computed(() => shownDepths.value.map((d) => ({
  label: d.label,
  value: getBarVal(d.key),
})));
</script>

<style scoped>
.cbp-wrap {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.cbp-card {
  background: #f9fbfc;
  border-radius: 14px;
  box-shadow: 0 2px 13px 2px #d6e4ef42;
  padding: 24px 34px 18px 34px;
  min-width: 350px;
  max-width: 490px;
  width: 100%;
  margin: 0 auto;
  transition: box-shadow 0.18s;
}
.cbp-header { margin-bottom: 12px; }
.cbp-title { font-weight: 600; font-size: 1.2em; letter-spacing: 0.02em; }
.cbp-view-select { margin-bottom: 8px; justify-content: flex-end; }
.cbp-graph-ct { margin-bottom: 18px; min-width: 100%; will-change: transform; transition: transform 0.2s cubic-bezier(.44,1.9,.59,.93);}
.cbp-bars { display: flex; flex-direction: column; align-items: flex-start; gap: 13px; }
.cbp-bar-row { display: flex; align-items: center; min-height: 26px; }
.cbp-bar-label { width: 72px; font-size: 1em; color: #333; margin-right: 5px; text-align: right; font-weight: 500; }
.cbp-bar-line { position: relative; width: 180px; height: 18px; background: #e9eef5; border-radius: 8px; margin-left: 10px; margin-right: 10px; display: flex; align-items: center; overflow: visible; }
.cbp-bar { position: absolute; height: 18px; border-radius: 8px; transition: width .2s; }
.cbp-bar-enchente { left: 0; background: #1976d2; }
.cbp-bar-vazante { right: 0; background: #e57373; }
.cbp-bar-value { position: absolute; top: -22px; font-size: .97em; font-weight: 500; color: #333; white-space: nowrap; }
.cbp-legend { font-size: .97em; margin-top: 6px; gap: 16px; }
.cbp-leg-enchente { width: 18px; height: 10px; background: #1976d2; display: inline-block; border-radius: 5px; margin-right: 3px; }
.cbp-leg-vazante { width: 18px; height: 10px; background: #e57373; display: inline-block; border-radius: 5px; margin-left: 10px; margin-right: 3px; }
.cbp-leg-label { color: #888; margin-left: 16px; }
.cbp-table-ct, .cbp-current-svg-ct { min-height: 170px; display: flex; align-items: center; justify-content: center; }
.cbp-table { width: 100%; border-collapse: collapse; }
.cbp-table th, .cbp-table td { padding: 7px 12px; text-align: left; font-size: .97em; color: #222; }
.cbp-table th { font-weight: bold; background: #f3f7fa; border-bottom: 1px solid #e2e7ee; }
.cbp-table td { border-bottom: 1px solid #f0f1f3; }
@media (max-width: 650px) {
  .cbp-card { min-width: 94vw; padding: 10vw 2vw 6vw 2vw; }
  .cbp-bar-label { width: 48px; }
  .cbp-bar-line { width: 100px; }
}
/* SVG gráfico novo */
.current-bars-graph {
  background: #fafcfd;
  border-radius: 14px;
  box-shadow: 0 2px 6px #0001;
  padding: 8px 6px 10px 2px;
  width: 100%;
  max-width: 420px;
  margin: 0 auto;
}
</style>
