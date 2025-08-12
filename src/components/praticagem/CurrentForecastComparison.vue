<template>
  <div class="q-pa-md">
    <!-- Controles globais + configurações -->
    <q-card class="q-pa-sm q-mb-md shadow-2 bg-white">
      <div class="row items-center justify-between">
        <div class="row items-center q-gutter-sm">
          <q-btn-toggle
            v-model="prevSource"
            spread
            toggle-color="secondary"
            :options="[
              { label: 'Prev 5-min', value: '5min' },
              { label: 'Prev Horária', value: 'hora' }
            ]"
            size="sm"
            class="tide-toggle"
          />
          <q-toggle
            v-model="config.showBand"
            color="primary"
            label="Exibir banda"
            class="q-ml-md"
          />
        </div>
        <div class="row items-center q-gutter-sm">
          <q-select
            v-model="selectedDepthKeys"
            :options="depthOptions"
            option-value="key"
            option-label="label"
            multiple
            use-chips
            dense
            outlined
            emit-value
            map-options
            style="min-width: 280px"
            label="Profundidades"
          />
          <q-btn
            color="primary"
            icon="refresh"
            dense
            label="Atualizar"
            :loading="loading"
            @click="loadAll"
          />
          <q-btn flat round dense icon="more_vert" @click="showConfig = true" class="q-ml-xs" />
        </div>
      </div>
    </q-card>

    <!-- Configurações -->
    <q-dialog v-model="showConfig" persistent>
      <!-- mais transparência + leve blur -->
      <q-card style="min-width:360px;max-width:97vw; background: rgba(255,255,255,0.72); backdrop-filter: blur(2px);">
        <q-card-section class="row items-center q-pa-sm">
          <div class="text-h6 text-primary">Configurações (todas as profundidades)</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showConfig = false" />
        </q-card-section>
        <q-separator />
        <q-card-section class="q-gutter-md">
          <!-- Altura -->
          <div>
            <div class="text-bold q-mb-xs">Altura do gráfico</div>
            <div class="row items-center">
              <q-slider
                v-model="config.chartHeight"
                :min="160"
                :max="700"
                :step="10"
                color="primary"
                label
                style="max-width:260px;margin-left:8px;"
              />
              <span class="q-ml-md text-grey-7">{{ config.chartHeight }} px</span>
            </div>
          </div>

          <!-- Exibição dos valores -->
          <div>
            <div class="text-bold q-mb-xs">Exibição dos valores</div>
            <q-option-group
              v-model="config.showValuesMode"
              :options="[
                { label: 'Card flutuante (tooltip)', value: 'tooltip' },
                { label: 'Caixinhas acima do gráfico', value: 'boxes' }
              ]"
              type="radio"
              color="primary"
              inline
            />
          </div>

          <!-- Pontos -->
          <div>
            <div class="text-bold q-mb-xs">Pontos no gráfico</div>
            <q-option-group
              v-model="config.showPoints"
              :options="[
                { label: 'Com bolinhas', value: true },
                { label: 'Sem bolinhas', value: false }
              ]"
              type="radio"
              color="primary"
              inline
            />
          </div>

          <!-- Banda -->
          <div>
            <q-toggle
              v-model="config.showBand"
              color="primary"
              label="Exibir banda na previsão"
            />
            <div v-if="config.showBand" class="q-mt-xs q-gutter-sm">
              <div class="text-caption text-bold q-mb-xs">Largura da banda ±</div>
              <q-slider
                v-model="config.bandDelta"
                :min="0.05"
                :max="0.30"
                :step="0.01"
                color="primary"
                label
                style="max-width:200px"
                :label-value="config.bandDelta.toFixed(2)"
              />
              <span class="q-ml-sm text-grey-7">{{ (config.bandDelta*2).toFixed(2) }} total</span>
            </div>
            <div v-if="config.showBand" class="q-mt-sm">
              <q-option-group
                v-model="config.bandStyle"
                :options="[
                  { label: 'Linha + Fundo', value: 'both' },
                  { label: 'Só linha/borda', value: 'border' },
                  { label: 'Só fundo', value: 'fill' }
                ]"
                type="radio"
                color="primary"
                inline
              />
            </div>
          </div>

          <!-- Espessura linha -->
          <div>
            <div class="text-bold q-mb-xs">Espessura das Linhas</div>
            <q-option-group
              v-model="config.lineWidth"
              :options="[
                { label: 'Linha fina', value: 1 },
                { label: 'Linha grossa', value: 4 }
              ]"
              type="radio"
              color="primary"
              inline
            />
          </div>

          <!-- Suavização da Previsão -->
          <div>
            <div class="text-bold q-mb-xs">Suavização da previsão</div>
            <q-option-group
              v-model="config.smoothingType"
              :options="[
                { label: 'Nenhuma', value: 'none' },
                { label: 'Média móvel (SMA)', value: 'sma' },
                { label: 'Exponencial (EMA)', value: 'ema' },
                { label: 'Triangular', value: 'triangular' }
              ]"
              type="radio"
              color="primary"
              inline
            />
            <div v-if="config.smoothingType === 'sma' || config.smoothingType === 'triangular'" class="q-mt-xs row items-center q-gutter-sm">
              <div class="text-caption text-bold">Janela (ímpar)</div>
              <q-slider
                v-model="config.smoothingWindow"
                :min="3"
                :max="31"
                :step="2"
                color="primary"
                label
                style="max-width:200px"
              />
              <span class="text-grey-7">{{ config.smoothingWindow }} pts</span>
            </div>
            <div v-if="config.smoothingType === 'ema'" class="q-mt-xs row items-center q-gutter-sm">
              <div class="text-caption text-bold">α (suavização)</div>
              <q-slider
                v-model="config.smoothingAlpha"
                :min="0.05"
                :max="0.50"
                :step="0.01"
                color="primary"
                label
                style="max-width:200px"
                :label-value="config.smoothingAlpha.toFixed(2)"
              />
            </div>
          </div>

          <!-- Tooltip -->
          <div>
            <div class="text-bold q-mb-xs">Tamanho do card de valores</div>
            <div class="row items-center q-gutter-sm">
              <q-slider
                v-model="config.tooltipScale"
                :min="0.2"
                :max="3"
                :step="0.05"
                color="primary"
                label
                style="max-width:180px"
              />
              <span class="text-grey-7">{{ config.tooltipScale.toFixed(2) }}×</span>
            </div>
            <div class="q-mt-xs">
              <q-option-group
                v-model="config.tooltipPosition"
                :options="[
                  { label: 'Topo Esq.', value: 'top-left' },
                  { label: 'Topo Dir.', value: 'top-right' },
                  { label: 'Rodapé Esq.', value: 'bottom-left' },
                  { label: 'Rodapé Dir.', value: 'bottom-right' },
                  { label: 'Seguir mouse', value: 'follow' }
                ]"
                type="radio"
                color="primary"
                inline
              />
            </div>
            <div class="row items-center q-mt-xs q-gutter-sm">
              <div style="min-width:140px;">
                <q-slider v-model="config.tooltipOffsetY" :min="-200" :max="200" :step="1" color="primary" label/>
                <div class="text-caption text-grey-7">Offset Vertical: {{ config.tooltipOffsetY }}px</div>
              </div>
              <div style="min-width:140px;">
                <q-slider v-model="config.tooltipOffsetX" :min="-500" :max="500" :step="1" color="primary" label/>
                <div class="text-caption text-grey-7">Offset Horizontal: {{ config.tooltipOffsetX }}px</div>
              </div>
            </div>
          </div>

          <!-- Escala Y -->
          <div>
            <div class="text-bold q-mb-xs">Escala do eixo Y</div>
            <q-option-group
              v-model="config.yMode"
              :options="[
                { label: 'Automática (por gráfico)', value: 'auto' },
                { label: 'Fixa (global)', value: 'fixed' }
              ]"
              type="radio"
              color="primary"
              inline
            />
            <div v-if="config.yMode === 'fixed'" class="row items-center q-gutter-sm q-mt-xs">
              <div style="min-width:220px;">
                <div class="text-caption text-bold">Y mín</div>
                <q-slider v-model="config.yMin" :min="-10" :max="0" :step="0.1" color="primary" label />
              </div>
              <div style="min-width:220px;">
                <div class="text-caption text-bold">Y máx</div>
                <q-slider v-model="config.yMax" :min="0" :max="10" :step="0.1" color="primary" label />
              </div>
            </div>
          </div>
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn flat label="Fechar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Cards por profundidade -->
    <div class="row q-col-gutter-md">
      <div v-for="d in visibleDepths" :key="d.key" class="col-12 col-md-6 col-lg-4">
        <q-card class="q-pa-md bg-white shadow-2 tide-compare-card">
          <div class="row items-center justify-between q-mb-xs">
            <div class="text-subtitle1 text-primary text-weight-bold">
              Intensidade {{ d.label }} — Histórico x Previsão ({{ prevSourceLabel }})
            </div>
            <div v-if="latestTimestamps[d.key]" class="text-caption text-grey-7">
              Atualizado: {{ latestTimestamps[d.key] }}
            </div>
          </div>

          <!-- Caixinhas (opcional conforme config) -->
          <div v-if="config.showValuesMode === 'boxes'" class="row q-gutter-md tide-mini-card-row q-mb-md">
            <div class="tide-mini-value-card">
              <div class="text-caption text-grey-7">Histórico</div>
              <div class="text-h6 text-primary">
                {{ miniCardValue[d.key]?.hist !== null ? miniCardValue[d.key]?.hist : '--' }}
              </div>
            </div>
            <div class="tide-mini-value-card">
              <div class="text-caption text-grey-7">Previsão</div>
              <div class="text-h6" :style="{ color: d.color }">
                {{ miniCardValue[d.key]?.prev !== null ? miniCardValue[d.key]?.prev : '--' }}
              </div>
            </div>
          </div>

          <div class="row items-center q-mb-xs">
            <q-btn dense flat icon="chevron_left" @click="prevWindow(d.key)" :disable="cursor[d.key] <= 0" class="q-mr-xs" />
            <span class="text-caption">{{ windowLabel(d.key) }}</span>
            <q-btn dense flat icon="chevron_right" @click="nextWindow(d.key)" :disable="cursor[d.key] >= maxCursor(d.key)" class="q-ml-xs" />
          </div>

          <div class="tide-chart-container" ref="chartWrap" :style="{ height: config.chartHeight + 'px' }">
            <canvas :ref="el => setCanvasRef(d.key, el)" :height="config.chartHeight" style="width:100%;"></canvas>

            <!-- Tooltip (apenas se modo tooltip) -->
            <div v-if="config.showValuesMode === 'tooltip' && tooltip[d.key]?.active" :style="tooltipBoxStyle(d.key)">
              <div class="text-caption" style="font-weight:bold;">
                {{ tooltip[d.key].label }}
              </div>
              <div class="q-mt-xs">
                <span style="color:#1e78db"><b>Histórico:</b> {{ tooltip[d.key].hist }}</span><br>
                <span :style="{color: d.color}"><b>Previsão:</b> {{ tooltip[d.key].prev }}</span>
              </div>
            </div>
          </div>

          <!-- Big numbers: Acerto/Erro (por profundidade) -->
          <div v-if="config.showBand && acertoErro[d.key]?.total > 0" class="row q-mt-sm justify-center items-center">
            <div style="font-size:1.08em; color:#167e31; margin-right:18px;">
              Acerto: <b>{{ acertoErro[d.key].percAcerto }}%</b>
            </div>
            <div style="font-size:1.08em; color:#d32f2f;">
              Erro: <b>{{ acertoErro[d.key].percErro }}%</b>
            </div>
          </div>
        </q-card>
      </div>
    </div>

    <div class="q-mt-md text-negative" v-if="error">{{ error }}</div>
  </div>
</template>

<script setup>
import {
  ref, computed, reactive, onMounted, watch, nextTick,
} from 'vue';
import { storeToRefs } from 'pinia';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip as ChartTooltip, Legend, Filler,
} from 'chart.js';
import { useWeatherStore } from 'src/stores/weather';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Title, ChartTooltip, Legend, Filler);

/* ===== Profundidades ===== */
const DEPTHS = [
  { key: '1_5m', label: '1.5m', color: '#4f7fee' },
  { key: '3m', label: '3m', color: '#5cc77e' },
  { key: '6m', label: '6m', color: '#c2b81e' },
  { key: '7_5m', label: '7.5m', color: '#f59f41' },
  { key: '9m', label: '9m', color: '#cf5c36' },
  { key: '10_5m', label: '10.5m', color: '#b03ad7' },
  { key: '12m', label: '12m', color: '#5e6c7d' },
  { key: '13_5m', label: '13.5m', color: '#43c3d6' },
  { key: 'superficie', label: 'Superfície', color: '#e31b5c' },
];
const depthOptions = DEPTHS.map((d) => ({ label: d.label, key: d.key }));
const mestreIntensityCol = (key) => `intensidade_${key}`;
const prevCol = (key) => `valor_previsto_${key}`;

/* ===== Campos esperados ===== */
const ALL_MESTRE_COLS = [
  'timestamp_br', 'timestamp_prev',
  ...DEPTHS.flatMap((d) => [
    `intensidade_${d.key}`,
    `direcao_${d.key}`,
    `enchente_vazante_${d.key}`,
    `intensidade_${d.key}_ajustada`,
    `direcao_${d.key}_deg`,
  ]),
];
const ALL_PREV_COLS = [
  'timestamp_br',
  ...DEPTHS.map((d) => `valor_previsto_${d.key}`),
];

/* ===== Utils ===== */
const numOrNull = (v) => {
  if (v === null || v === undefined) return null;
  if (typeof v === 'object' && Object.keys(v).length === 0) return null;
  if (typeof v === 'string' && v.trim() === '') return null;
  const n = Number(v);
  return Number.isFinite(n) ? n : null;
};
const normTs = (t) => (!t
  ? null
  : (typeof t === 'string'
      ? t.replace('T', ' ').slice(0, 19)
      : (t?.date ? String(t.date).replace('T', ' ').slice(0, 19) : String(t))));

function normalizeMestreRow(r) {
  const out = { ...r };
  ALL_MESTRE_COLS.forEach((col) => {
    if (!(col in out) || out[col] === undefined) out[col] = null;
    if (col.startsWith('intensidade_')) out[col] = numOrNull(out[col]);
  });
  out.timestamp_br = normTs(out.timestamp_br);
  out.timestamp_prev = normTs(out.timestamp_prev);
  return out;
}
function normalizePrevRow(r) {
  const out = { ...r };
  ALL_PREV_COLS.forEach((col) => {
    if (!(col in out) || out[col] === undefined) out[col] = null;
    if (col.startsWith('valor_previsto_')) out[col] = numOrNull(out[col]); // preserva sinal
  });
  out.timestamp_br = normTs(out.timestamp_br);
  return out;
}

/* ===== Direção → sinal do histórico ===== */
function angDist(a, b) { return Math.abs(((a - b + 180) % 360) - 180); }
function dirStringToDeg(s) {
  if (!s) return null;
  const t = String(s).trim().toUpperCase();
  const map = { N:0,
NNE:22.5,
NE:45,
ENE:67.5,
E:90,
ESE:112.5,
SE:135,
SSE:157.5,
    S:180,
SSO:202.5,
SO:225,
OSO:247.5,
O:270,
ONO:292.5,
NO:315,
NNO:337.5,
    W:270,
WNW:292.5,
NW:315,
NNW:337.5,
SSW:202.5,
SW:225,
WSW:247.5,
// eslint-disable-next-line no-dupe-keys
ESE:112.5 };
  const token = t.split(/[^A-Z]/)[0];
  if (map[token] != null) return map[token];
  const m = t.match(/(\d+(?:\.\d+)?)\s*°?/);
  return m ? Number(m[1]) % 360 : null;
}
function signByDirection(deg, str) {
  const d = (deg == null || !Number.isFinite(deg)) ? dirStringToDeg(str) : deg;
  if (d == null) return null;
  return angDist(d, 0) <= angDist(d, 180) ? +1 : -1;
}
function hexToRgba(hex, alpha = 1) {
  const h = hex.replace('#', '');
  const bigint = parseInt(h, 16);
  // eslint-disable-next-line no-bitwise
  const r = (bigint >> 16) & 255;
  // eslint-disable-next-line no-bitwise
  const g = (bigint >> 8) & 255;
  // eslint-disable-next-line no-bitwise
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

/* ===== CONTROLE VUE ===== */
const defaultConfig = () => ({
  chartHeight: 320,
  showValuesMode: 'tooltip', // tooltip | boxes
  showPoints: false,
  showBand: false,
  bandDelta: 0.15,
  bandStyle: 'both',        // both | border | fill
  lineWidth: 1,             // fina de verdade
  tooltipScale: 1,
  tooltipPosition: 'top-right', // top-left | top-right | bottom-left | bottom-right | follow
  tooltipOffsetX: 0,
  tooltipOffsetY: 0,
  yMode: 'auto',            // auto | fixed
  yMin: -3,
  yMax: 3,
  // suavização
  smoothingType: 'none',    // none | sma | ema | triangular
  smoothingWindow: 7,       // ímpar
  smoothingAlpha: 0.2,      // EMA alpha
  // legado (se existia no localStorage)
  bandDisplay: 'both',
});
const CONFIG_KEY = 'correntezaConfigV1';
const config = ref(defaultConfig());
const showConfig = ref(false);
onMounted(() => {
  const saved = localStorage.getItem(CONFIG_KEY);
  if (saved) {
    try {
      const parsed = JSON.parse(saved);
      Object.assign(config.value, parsed);
    } catch {}
  }
});
watch(config, () => {
  localStorage.setItem(CONFIG_KEY, JSON.stringify(config.value));
}, { deep: true });

const loading = ref(false);
const error = ref(null);
const prevSource = ref('5min');
const prevSourceLabel = computed(() => (prevSource.value === '5min' ? '5-min' : 'horária'));

const SELECTED_DEPTHS_KEY = 'correntezaSelectedDepths';
function getInitialDepths() {
  try {
    const saved = localStorage.getItem(SELECTED_DEPTHS_KEY);
    const val = saved ? JSON.parse(saved) : null;
    if (Array.isArray(val) && val.length) return val;
  } catch {}
  return ['3m'];
}
const selectedDepthKeys = ref(getInitialDepths());
const visibleDepths = computed(() => DEPTHS.filter((d) => selectedDepthKeys.value.includes(d.key)));
watch(selectedDepthKeys, (val) => {
  localStorage.setItem(SELECTED_DEPTHS_KEY, JSON.stringify(val));
}, { deep: true });

/* ===== STORE ===== */
const weather = useWeatherStore();
const {
  weatherHistory,
  correnteza5Min,
  correntezaHourly,
  error: storeError,
} = storeToRefs(weather);

/* ===== Buffers normalizados ===== */
const mestre5min = ref([]);
const prev5m = ref([]);
const prevHourly = ref([]);
function syncFromStore() {
  mestre5min.value = (weatherHistory.value || []).map(normalizeMestreRow)
    .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
  prev5m.value = (correnteza5Min.value || []).map(normalizePrevRow)
    .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
  prevHourly.value = (correntezaHourly.value || []).map(normalizePrevRow)
    .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
}

/* ===== Carregamento ===== */
async function loadAll() {
  loading.value = true; error.value = null;
  try {
    await Promise.all([
      weather.fetchHistory(1000),
      weather.fetchCorrenteza5Min(4000),
      weather.fetchCorrentezaHourly(1000),
    ]);
    syncFromStore();
    DEPTHS.forEach((d) => { cursor[d.key] = maxCursor(d.key); });
  } catch (e) {
    error.value = (storeError.value || e?.message || String(e));
  } finally {
    loading.value = false;
  }
}

/* ===== Janela/Cursores ===== */
const windowSize = 288; const stepSize = 144;
const cursor = reactive({}); DEPTHS.forEach((d) => { cursor[d.key] = 0; });

const alignedTimestamps = (key) => {
  const allTs = [
    ...new Set([
      ...mestre5min.value.map((r) => r.timestamp_br),
      ...(prevSource.value === '5min' ? prev5m.value : prevHourly.value).map((r) => r.timestamp_br),
    ]),
  ].filter(Boolean).sort();
  return allTs;
};
const maxCursor = (key) => Math.max(0, alignedTimestamps(key).length - windowSize);
const prevWindow = (key) => { cursor[key] = Math.max(0, cursor[key] - stepSize); renderChart(key); };
const nextWindow = (key) => { cursor[key] = Math.min(maxCursor(key), cursor[key] + stepSize); renderChart(key); };
const dataSliceTimestamps = (key) => alignedTimestamps(key).slice(cursor[key], cursor[key] + windowSize);
const windowLabel = (key) => {
  const ts = dataSliceTimestamps(key);
  const ini = ts[0]?.slice(11, 16) || '';
  const fim = ts[ts.length - 1]?.slice(11, 16) || '';
  return ini && fim ? `${ini} – ${fim}` : '';
};

/* ===== Suavização (previsão) ===== */
function ensureOdd(n, min = 3) {
  let k = Math.max(min, Math.floor(Number(n) || min));
  if (k % 2 === 0) k += 1;
  return k;
}
function smaCentered(arr, win) {
  const w = ensureOdd(win);
  const k = (w - 1) / 2;
  return arr.map((_, i) => {
    let sum = 0, cnt = 0;
    for (let j = i - k; j <= i + k; j++) {
      if (j >= 0 && j < arr.length && typeof arr[j] === 'number') {
        sum += arr[j]; cnt++;
      }
    }
    return cnt ? sum / cnt : null;
  });
}
function ema(arr, alpha = 0.2) {
  let prev = null;
  return arr.map(v => {
    if (typeof v !== 'number') return prev;
    prev = (prev == null) ? v : (alpha * v + (1 - alpha) * prev);
    return prev;
  });
}
function triangular(arr, win) {
  const first = smaCentered(arr, win);
  return smaCentered(first, win);
}
function smoothPrevSeries(base) {
  switch (config.value.smoothingType) {
    case 'sma':
      return smaCentered(base, config.value.smoothingWindow);
    case 'ema':
      return ema(base, config.value.smoothingAlpha);
    case 'triangular':
      return triangular(base, config.value.smoothingWindow);
    default:
      return base;
  }
}

/* ===== Série por profundidade ===== */
const getDepthSeries = (key) => {
  const colHist = mestreIntensityCol(key);
  const colPrev = prevCol(key);
  const colDirDeg = `direcao_${key}_deg`;
  const colDirStr = `direcao_${key}`;

  const histMap = new Map(mestre5min.value.map((r) => [r.timestamp_br, numOrNull(r[colHist])]));
  const dirDegMap = new Map(mestre5min.value.map((r) => [r.timestamp_br, numOrNull(r[colDirDeg])]));
  const dirStrMap = new Map(mestre5min.value.map((r) => [r.timestamp_br, r[colDirStr]]));

  const prevRows = prevSource.value === '5min' ? prev5m.value : prevHourly.value;
  const prevMap = new Map(prevRows.map((r) => [r.timestamp_br, numOrNull(r[colPrev])]));

  const timestamps = dataSliceTimestamps(key);
  const rawPrev = timestamps.map(ts => prevMap.get(ts) ?? null);
  const smPrev = smoothPrevSeries(rawPrev);

  return timestamps.map((ts, idx) => {
    const rawHist = histMap.get(ts) ?? null;
    const dirDeg = dirDegMap.get(ts);
    const dirStr = dirStrMap.get(ts);
    const sgn = signByDirection(dirDeg, dirStr);
    const signedHist = (rawHist == null || sgn == null) ? rawHist : rawHist * sgn;
    return {
      timestamp: ts,
      hist: signedHist,              // enchente > 0, vazante < 0
      prev: smPrev[idx] ?? null,     // previsão suavizada conforme config
    };
  });
};

/* ===== Mini-cards ===== */
const lastScrubIdx = reactive({});
const miniCardValue = computed(() => {
  const out = {};
  visibleDepths.value.forEach((d) => {
    const serie = getDepthSeries(d.key);
    let idx = lastScrubIdx[d.key];
    if (typeof idx !== 'number' || idx < 0 || idx >= serie.length) idx = serie.length - 1;
    const hist = serie[idx]?.hist;
    const prev = serie[idx]?.prev;
    out[d.key] = {
      hist: hist != null ? Number(hist).toFixed(3) : '--',
      prev: prev != null ? Number(prev).toFixed(3) : '--',
    };
  });
  return out;
});
const latestTimestamps = computed(() => {
  const out = {};
  DEPTHS.forEach((d) => {
    const col = mestreIntensityCol(d.key);
    const valid = mestre5min.value.filter((r) => r[col] != null);
    out[d.key] = valid.length ? valid[valid.length - 1].timestamp_br : null;
  });
  return out;
});

/* ===== Acerto/Erro por profundidade (na janela atual) ===== */
const acertoErro = computed(() => {
  const out = {};
  visibleDepths.value.forEach(d => {
    const serie = getDepthSeries(d.key);
    let acertos = 0, total = 0;
    // eslint-disable-next-line no-restricted-syntax
    for (const s of serie) {
      if (s.prev == null || s.hist == null) continue;
      const min = s.prev - config.value.bandDelta;
      const max = s.prev + config.value.bandDelta;
      if (s.hist >= min && s.hist <= max) acertos++;
      total++;
    }
    const percAcerto = total ? Math.round(acertos / total * 100) : 0;
    const percErro = total ? 100 - percAcerto : 0;
    out[d.key] = { acertos, total, percAcerto, percErro };
  });
  return out;
});

/* ===== Tooltip ===== */
const tooltip = reactive({});
const tooltipBoxStyle = (key) => {
  const baseBox = {
    position: 'absolute',
    zIndex: 99,
    background: '#fff',
    color: '#263238',
    borderRadius: '8px',
    boxShadow: '0 6px 16px rgba(38,50,56,0.2)',
    padding: '10px 14px 9px 12px',
    pointerEvents: 'none',
    fontSize: '1.06em',
    lineHeight: '1.18em',
    border: '1.5px solid #d7e9fa',
    userSelect: 'none',
    minWidth: '128px',
    transform: `scale(${config.value.tooltipScale})`,
  };
  const t = tooltip[key];
  if (!t) return baseBox;
  let style = { ...baseBox, left: 'auto', top: '8px', right: '8px', bottom: 'auto' };
  switch (config.value.tooltipPosition) {
    case 'top-left':
      style = { ...baseBox, left: `${8 + config.value.tooltipOffsetX}px`, top: `${8 + config.value.tooltipOffsetY}px`, right: 'auto', bottom: 'auto' }; break;
    case 'top-right':
      style = { ...baseBox, left: 'auto', top: `${8 + config.value.tooltipOffsetY}px`, right: `${8 - config.value.tooltipOffsetX}px`, bottom: 'auto' }; break;
    case 'bottom-left':
      style = { ...baseBox, left: `${8 + config.value.tooltipOffsetX}px`, top: 'auto', right: 'auto', bottom: `${8 - config.value.tooltipOffsetY}px` }; break;
    case 'bottom-right':
      style = { ...baseBox, left: 'auto', top: 'auto', right: `${8 - config.value.tooltipOffsetX}px`, bottom: `${8 - config.value.tooltipOffsetY}px` }; break;
    case 'follow':
      style = { ...baseBox, left: `${t.left + config.value.tooltipOffsetX}px`, top: `${t.top + config.value.tooltipOffsetY}px`, right: 'auto', bottom: 'auto' }; break;
    default:
      style = { ...baseBox, left: 'auto', top: `${8 + config.value.tooltipOffsetY}px`, right: `${8 - config.value.tooltipOffsetX}px`, bottom: 'auto' };
  }
  return style;
};

/* ===== Chart infra ===== */
const canvasRefs = reactive({});
function setCanvasRef(key, el) { if (el) canvasRefs[key] = el; }
const chartjsObjs = {};

/* ===== Render por gráfico (profundidade) ===== */
function computeYScale(histVals, prevVals) {
  if (config.value.yMode === 'fixed') {
    return { min: config.value.yMin, max: config.value.yMax };
  }
  const vals = [];
  histVals.forEach(v => { if (v != null) vals.push(v); });
  prevVals.forEach(v => { if (v != null) vals.push(v); });

  if (config.value.showBand) {
    prevVals.forEach(v => { if (v != null) vals.push(v - config.value.bandDelta, v + config.value.bandDelta); });
  }
  if (!vals.length) return { min: -3, max: 3 };
  let min = Math.min(...vals);
  let max = Math.max(...vals);
  if (min === max) { const pad = Math.max(0.1, Math.abs(min) * 0.2); min -= pad; max += pad; }
  const pad = (max - min) * 0.12;
  return { min: Number((min - pad).toFixed(2)), max: Number((max + pad).toFixed(2)) };
}

function renderChart(key) {
  nextTick(() => {
    if (!canvasRefs[key]) return;
    if (chartjsObjs[key]) { chartjsObjs[key].destroy(); chartjsObjs[key] = null; }
    const d = DEPTHS.find((x) => x.key === key);
    const pointRadius = config.value.showPoints ? 2 : 0;
    const serie = getDepthSeries(key);
    const labels = serie.map((s) => s.timestamp.slice(11, 16));
    const histVals = serie.map((s) => s.hist);
    const prevVals = serie.map((s) => s.prev);

    const yRange = computeYScale(histVals, prevVals);

    const bandMode = config.value.bandStyle || (
      config.value.bandDisplay === 'lines' ? 'border'
        : config.value.bandDisplay === 'background' ? 'fill' : 'both'
    );
    const showBorder = bandMode === 'border' || bandMode === 'both';
    const showFill = bandMode === 'fill' || bandMode === 'both';
    const bandBorderWidth = showBorder ? 2 : 0;
    const bandFillColor = showFill ? hexToRgba(d.color, 0.13) : 'rgba(0,0,0,0)';

    const datasets = [];
    if (config.value.showBand) {
      const plus = prevVals.map((v) => (v != null ? v + config.value.bandDelta : null));
      const minus = prevVals.map((v) => (v != null ? v - config.value.bandDelta : null));

      datasets.push({
        label: `Previsão ${d.label} -${config.value.bandDelta.toFixed(2)}`,
        data: minus,
        borderColor: showBorder ? d.color : 'rgba(0,0,0,0)',
        backgroundColor: 'rgba(0,0,0,0)',
        borderWidth: bandBorderWidth,
        pointRadius: pointRadius ? 1.5 : 0,
        tension: 0.32,
        fill: false,
        order: 0,
        spanGaps: true,
      });
      datasets.push({
        label: `Previsão ${d.label} +${config.value.bandDelta.toFixed(2)}`,
        data: plus,
        borderColor: showBorder ? d.color : 'rgba(0,0,0,0)',
        backgroundColor: bandFillColor,
        borderWidth: bandBorderWidth,
        pointRadius: pointRadius ? 1.5 : 0,
        tension: 0.32,
        fill: '-1',
        order: 0,
        spanGaps: true,
      });
    }

    datasets.push({
      label: `Histórico ${d.label}`,
      data: histVals,
      borderColor: '#1e78db',
      backgroundColor: '#1e78db33',
      tension: 0.32,
      pointRadius,
      borderWidth: config.value.lineWidth,
      fill: false,
      spanGaps: true,
      order: 2,
    });

    if (!config.value.showBand) {
      datasets.push({
        label: `Previsão ${d.label}`,
        data: prevVals,
        borderColor: d.color,
        backgroundColor: `${d.color}33`,
        borderDash: [6, 4],
        tension: 0.32,
        pointRadius,
        borderWidth: config.value.lineWidth,
        fill: false,
        spanGaps: true,
        order: 2,
      });
    }

    chartjsObjs[key] = new Chart(canvasRefs[key].getContext('2d'), {
      type: 'line',
      data: { labels, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { boxWidth: 16, font: { size: 15 }, filter: (item) => !item.text.includes('_band_') },
          },
          tooltip: { enabled: false },
        },
        elements: {
          point: {
            radius: pointRadius,
            hoverRadius: pointRadius,
            hitRadius: Math.max(pointRadius, 6),
          }
        },
        interaction: { intersect: false, mode: 'nearest', axis: 'x' },
        scales: {
          y: { min: yRange.min, max: yRange.max, title: { display: true, text: 'Intensidade (tk)', font: { size: 14 } } },
          x: { title: { display: true, text: 'Hora', font: { size: 13 } }, ticks: { autoSkip: true, maxTicksLimit: 20 } },
        },
        onHover: (event, elements, chart) => {
          if (!elements.length) { tooltip[key] = { active: false }; lastScrubIdx[key] = null; return; }
          const idx = elements[0].index;
          lastScrubIdx[key] = idx;

          const rect = chart.canvas.getBoundingClientRect();
          const left = event?.native?.clientX != null ? event.native.clientX - rect.left : 0;
          const top  = event?.native?.clientY != null ? event.native.clientY - rect.top  : 0;

          tooltip[key] = {
            active: true,
            label: labels[idx],
            hist: histVals[idx] != null ? Number(histVals[idx]).toFixed(3) : '--',
            prev: prevVals[idx] != null ? Number(prevVals[idx]).toFixed(3) : '--',
            left,
            top,
          };
        },
        onLeave: () => { tooltip[key] = { active: false }; lastScrubIdx[key] = null; },
      },
      plugins: [{
        id: 'scrubPointer',
        afterDraw(chart) {
          // eslint-disable-next-line no-underscore-dangle
          const idx = chart.tooltip?._active?.[0]?.index ?? null;
          if (idx == null) return;
          const x = chart.scales.x.getPixelForValue(idx);
          const y0 = chart.scales.y.top; const y1 = chart.scales.y.bottom;
          const { ctx } = chart;
          ctx.save();
          ctx.strokeStyle = '#1e78db55';
          ctx.setLineDash([4, 4]);
          ctx.beginPath();
          ctx.moveTo(x, y0);
          ctx.lineTo(x, y1);
          ctx.stroke();
          ctx.restore();
        },
      }],
    });
  });
}

/* ===== Lifecycle & watchers ===== */
onMounted(async () => {
  await loadAll();
  nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key)));
});

watch(
  [
    visibleDepths, prevSource,
    () => config.value.showBand,
    () => config.value.showPoints,
    () => config.value.bandStyle,
    () => config.value.bandDelta,
    () => config.value.lineWidth,
    () => config.value.tooltipScale,
    () => config.value.tooltipPosition,
    () => config.value.tooltipOffsetX,
    () => config.value.tooltipOffsetY,
    () => config.value.yMode,
    () => config.value.yMin,
    () => config.value.yMax,
    () => config.value.smoothingType,
    () => config.value.smoothingWindow,
    () => config.value.smoothingAlpha,
    () => config.value.chartHeight,
    () => config.value.showValuesMode,
  ],
  () => nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key))),
  { deep: true },
);

watch([weatherHistory, correnteza5Min, correntezaHourly], () => {
  syncFromStore();
  nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key)));
}, { deep: true });
</script>

<style scoped>
.tide-compare-card { border-radius: 14px; margin-bottom: 18px; }
.tide-mini-card-row {
  justify-content: flex-start;
  margin-bottom: 0.6rem;
  margin-top: 0.6rem;
}
.tide-mini-value-card {
  background: #f6f8fc;
  border-radius: 9px;
  min-width: 92px;
  padding: 7px 18px 5px 18px;
  box-shadow: 0 2px 8px #e9eef3a9;
  display: flex; flex-direction: column; align-items: flex-start;
}
.tide-chart-container {
  width: 100%;
  min-height: 240px;
  max-height: 700px;
  height: 320px;
  position: relative;
}
.tide-toggle { min-width: 180px; }
@media (max-width: 700px) {
  .tide-compare-card { margin-bottom: 12px; }
  .tide-mini-value-card { min-width: 70px; font-size: 0.98em; }
  .tide-chart-container { min-height: 160px; max-height: 700px; height: 180px; }
  .tide-toggle { min-width: 120px; }
}
</style>
