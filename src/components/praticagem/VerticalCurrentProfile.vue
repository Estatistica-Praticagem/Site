<template>
  <div class="cbp-wrap">
    <div
      class="cbp-card"
      :style="{
        width: `${cardBaseWidth * settings.scale}px`,
        minWidth: `${cardBaseMinWidth * settings.scale}px`,
        maxWidth: `${cardBaseMaxWidth * settings.scale}px`,
        fontSize: `${1 * settings.scale}em`,
        padding: `${24 * settings.scale}px ${34 * settings.scale}px ${18 * settings.scale}px ${34 * settings.scale}px`,
        transition: 'all 0.18s',
      }"
    >
      <div class="cbp-header row items-center justify-between">
        <span class="cbp-title" :style="{ fontSize: `${1.12 * settings.scale}em` }">
          Perfil de Correnteza por Profundidade
        </span>
        <q-btn dense flat round icon="more_vert" @click="showConfig = true" />
      </div>

      <!-- Seletor de visualização -->
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

      <!-- VISOR: Barras -->
      <div v-if="settings.view === 'bars'" class="cbp-graph-ct">
        <div class="cbp-bars">
          <div
            v-for="d in shownDepths"
            :key="d.key"
            class="cbp-bar-row"
          >
            <span
              class="cbp-bar-label"
              :style="{ fontSize: `${1 * settings.scale}em`, width: `${72 * settings.scale}px` }"
            >{{ d.label }}</span>

            <div
              class="cbp-bar-line"
              :style="{ width: `${180 * settings.scale}px`, height: `${18 * settings.scale}px` }"
            >
              <div
                v-if="getBarVal(d.key) < 0"
                class="cbp-bar cbp-bar-enchente"
                :style="{ width: barWidth(getBarVal(d.key)) * settings.scale + 'px', height: `${18 * settings.scale}px` }"
              />
              <div
                v-if="getBarVal(d.key) > 0"
                class="cbp-bar cbp-bar-vazante"
                :style="{ width: barWidth(getBarVal(d.key)) * settings.scale + 'px', height: `${18 * settings.scale}px` }"
              />
              <span
                v-if="getBarVal(d.key) !== 0"
                class="cbp-bar-value"
                :style="{
                  left: getBarVal(d.key) < 0 ? '0' : '',
                  right: getBarVal(d.key) > 0 ? '0' : '',
                  fontSize: `${0.97 * settings.scale}em`,
                  top: `${-22 * settings.scale}px`
                }"
              >
                {{ Math.abs(getBarVal(d.key)).toFixed(2) }} kts
              </span>
            </div>
          </div>
        </div>
        <div class="cbp-legend row items-center" :style="{ fontSize: `${0.97 * settings.scale}em` }">
          <span class="cbp-leg-enchente" :style="{ width: `${18 * settings.scale}px`, height: `${10 * settings.scale}px` }"></span> Enchente
          <span class="cbp-leg-vazante" :style="{ width: `${18 * settings.scale}px`, height: `${10 * settings.scale}px` }"></span> Vazante
          <span class="cbp-leg-label" :style="{ marginLeft: `${16 * settings.scale}px` }"> Intensidade (kts)</span>
        </div>
      </div>

      <!-- VISOR: SVG -->
      <div v-if="settings.view === 'current-graph'" class="cbp-current-svg-ct">
        <div class="current-bars-graph">
          <svg :width="width * settings.scale" :height="height * settings.scale" style="display:block;margin:0 auto">
            <line
              :x1="xZero * settings.scale"
              :y1="20 * settings.scale"
              :x2="xZero * settings.scale"
              :y2="(height - 10) * settings.scale"
              stroke="#bbb"
              :stroke-width="2 * settings.scale"
            />
            <g v-for="(row, idx) in currentBarData" :key="row.label">
              <rect
                :x="row.value >= 0 ? xZero * settings.scale : (xZero + scale(row.value)) * settings.scale"
                :y="yScale(idx) * settings.scale"
                :width="Math.abs(scale(row.value)) * settings.scale"
                :height="barHeight * settings.scale"
                :fill="row.value >= 0 ? '#e57373' : '#1976d2'"
              />
              <text
                :x="row.value >= 0
                  ? xZero * settings.scale + Math.abs(scale(row.value)) * settings.scale + 4 * settings.scale
                  : xZero * settings.scale + scale(row.value) * settings.scale - 38 * settings.scale"
                :y="yScale(idx) * settings.scale + barHeight * settings.scale / 2 + 5 * settings.scale"
                :font-size="12 * settings.scale"
                fill="#222"
              >{{ Math.abs(row.value).toFixed(2) }} kts</text>
              <text
                :x="12 * settings.scale"
                :y="yScale(idx) * settings.scale + barHeight * settings.scale / 2 + 5 * settings.scale"
                :font-size="14 * settings.scale"
                fill="#222"
              >{{ row.label }}</text>
            </g>
          </svg>
          <div :style="{ marginTop: `${8 * settings.scale}px`, fontSize: `${13 * settings.scale}px`, color: '#888' }">
            <span style="color:#1976d2;">⬅ Enchente</span>
            &nbsp; Intensidade (kts) &nbsp;
            <span style="color:#e57373;">Vazante ➡</span>
          </div>
        </div>
      </div>

      <!-- VISOR: Tabela -->
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

      <!-- CONFIG -->
      <q-dialog v-model="showConfig">
        <q-card style="min-width:320px;max-width:94vw;">
          <q-card-section>
            <div class="text-h6">Configurações do Gráfico</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <div class="q-mb-sm text-caption">Tamanho do Gráfico</div>
            <q-slider
              v-model="settings.scale"
              :min="0.7"
              :max="1.6"
              step="0.01"
              label="Tamanho do Gráfico"
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
  </div>
</template>

<script setup>
import {
  ref, computed, onMounted, watch,
} from 'vue';
import { useWeatherStore } from 'src/stores/weather';
import { storeToRefs } from 'pinia';

// Dimensões base
const cardBaseWidth = 490;
const cardBaseMinWidth = 350;
const cardBaseMaxWidth = 490;

// Pinia
const store = useWeatherStore();
const { weatherLast } = storeToRefs(store);
onMounted(() => { store.fetchLast(); });

// Opções de visualização
const viewOptions = [
  { label: 'Gráfico SVG', value: 'current-graph', icon: 'fas fa-align-left' },
  { label: 'Barras', value: 'bars', icon: 'fas fa-grip-horizontal' },
  { label: 'Tabela', value: 'table', icon: 'fas fa-table' },
];

const defaultSettings = {
  scale: 1,
  selectedDepths: [],
  view: 'bars',
};

const showConfig = ref(false);
const settings = ref({ ...defaultSettings });

// Lista dinâmica de profundidades
const depthObjects = computed(() => {
  const w = weatherLast.value || {};
  const list = [];

  // SUP
  if ('intensidade_superficie' in w || 'intensidade_superficie_ajustada' in w) {
    list.push({
      key: 'superficie',
      label: 'SUP (0m)',
      intKeys: ['intensidade_superficie_ajustada', 'intensidade_superficie'],
      enchKey: 'enchente_vazante_superficie',
    });
  }

  Object.keys(w).forEach((k) => {
    const m = k.match(/^intensidade_(\d+(?:_\d+)?)m(?:_ajustada)?$/); // pega 1_5m, 13_5m etc
    if (m) {
      const id = m[1]; // ex "1_5" ou "3"
      const key = `${id}m`; // "1_5m"
      if (list.some((o) => o.key === key)) return;
      list.push({
        key,
        label: `${id.replace('_', ',')}m`,
        intKeys: [
          `intensidade_${id}m_ajustada`,
          `intensidade_${id}m`,
        ],
        enchKey: `enchente_vazante_${id}m`,
      });
    }
  });

  // Ordena: SUP primeiro, depois crescente
  return list.sort((a, b) => {
    if (a.key === 'superficie') return -1;
    if (b.key === 'superficie') return 1;
    return parseFloat(a.label.replace(',', '.')) - parseFloat(b.label.replace(',', '.'));
  });
});

const allDepths = computed(() => depthObjects.value.map(({ key, label }) => ({ key, label })));

// Primeira vez seleciona todas
watch(depthObjects, (arr) => {
  if (!settings.value.selectedDepths.length) {
    settings.value.selectedDepths = arr.map((d) => d.key);
  }
}, { immediate: true });

// Salvar/restaurar config
function saveConfig() {
  localStorage.setItem('currentBarProfileConfig', JSON.stringify(settings.value));
}
onMounted(() => {
  const saved = localStorage.getItem('currentBarProfileConfig');
  if (saved) Object.assign(settings.value, JSON.parse(saved));
});
watch(settings, saveConfig, { deep: true });

// Filtradas
const shownDepths = computed(() => allDepths.value.filter((d) => settings.value.selectedDepths.includes(d.key)));

// Valor (kts) assinado
function getBarVal(key) {
  const w = weatherLast.value || {};
  const obj = depthObjects.value.find((o) => o.key === key);
  if (!obj) return 0;

  // Prioriza ajustada
  let raw = 0;
  // eslint-disable-next-line no-restricted-syntax
  for (const k of obj.intKeys) {
    if (w[k] != null) {
      raw = Number(w[k]) || 0;
      break;
    }
  }
  return raw; // já inclui sinal
}

// Largura barra normalizada
function barWidth(val) {
  const max = 2; // ajuste se quiser
  // eslint-disable-next-line no-mixed-operators
  return Math.abs(val) / max * 180;
}

// SVG helpers
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
  align-items: flex-start;
  justify-content: center;
  overflow: auto;
  padding: 16px 0;
}
.cbp-card {
  background: #f9fbfc;
  border-radius: 14px;
  box-shadow: 0 2px 13px 2px #d6e4ef42;
  margin: 0 auto;
  transition: all 0.18s;
}
.cbp-header { margin-bottom: 12px; }
.cbp-title { font-weight: 600; font-size: 1.2em; letter-spacing: 0.02em; }
.cbp-view-select { margin-bottom: 8px; justify-content: flex-end; }
.cbp-graph-ct, .cbp-table-ct, .cbp-current-svg-ct { min-width: 100%; }
.cbp-bars { display: flex; flex-direction: column; align-items: flex-start; gap: 13px; }
.cbp-bar-row { display: flex; align-items: center; min-height: 26px; }
.cbp-bar-label { font-size: 1em; color: #333; margin-right: 5px; text-align: right; font-weight: 500; }
.cbp-bar-line { position: relative; background: #e9eef5; border-radius: 8px; margin-left: 10px; margin-right: 10px; display: flex; align-items: center; overflow: visible; }
.cbp-bar { position: absolute; border-radius: 8px; transition: width .2s, height .2s; }
.cbp-bar-enchente { left: 0; background: #1976d2; }
.cbp-bar-vazante { right: 0; background: #e57373; }
.cbp-bar-value { position: absolute; font-weight: 500; color: #333; white-space: nowrap; }
.cbp-legend { margin-top: 6px; gap: 16px; }
.cbp-leg-enchente { background: #1976d2; display: inline-block; border-radius: 5px; margin-right: 3px; }
.cbp-leg-vazante { background: #e57373; display: inline-block; border-radius: 5px; margin-left: 10px; margin-right: 3px; }
.cbp-leg-label { color: #888; }
.cbp-table { width: 100%; border-collapse: collapse; }
.cbp-table th, .cbp-table td { padding: 7px 12px; text-align: left; font-size: .97em; color: #222; }
.cbp-table th { font-weight: bold; background: #f3f7fa; border-bottom: 1px solid #e2e7ee; }
.cbp-table td { border-bottom: 1px solid #f0f1f3; }
@media (max-width: 650px) {
  .cbp-card { min-width: 94vw !important; padding: 10vw 2vw 6vw 2vw !important; }
}
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
