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

    <q-dialog v-model="showConfig">
      <q-card style="min-width:320px;">
        <q-card-section class="row items-center">
          <div class="text-h6 text-primary">Configurações</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="q-mb-md">
            <div class="text-bold">Altura do gráfico</div>
            <q-slider
              v-model="config.chartHeight"
              :min="160"
              :max="700"
              :step="10"
              color="primary"
              label
              style="max-width:260px;"
            />
            <span class="q-ml-md text-grey-7">{{ config.chartHeight }} px</span>
          </div>
          <div class="q-mb-md">
            <div class="text-bold">Exibir pontos nos gráficos</div>
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
          <div class="q-mb-md">
            <div class="text-bold">Exibição da banda</div>
            <q-btn-toggle
              v-model="config.bandDisplay"
              toggle-color="primary"
              :options="[
                { label: 'Fundo e linhas', value: 'both' },
                { label: 'Apenas fundo', value: 'background' },
                { label: 'Apenas linhas', value: 'lines' }
              ]"
              size="sm"
              class="q-mt-xs"
            />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

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
          <div class="row q-gutter-md tide-mini-card-row q-mb-md">
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
          <div class="tide-chart-container" ref="chartWrap">
            <canvas :ref="el => setCanvasRef(d.key, el)" :height="config.chartHeight" style="width:100%;"></canvas>
            <div v-if="tooltip[d.key]?.active" :style="tooltipStyle">
              <div class="text-caption" style="font-weight:bold;">
                {{ tooltip[d.key].label }}
              </div>
              <div class="q-mt-xs">
                <span style="color:#1e78db"><b>Histórico:</b> {{ tooltip[d.key].hist }}</span><br>
                <span :style="{color: d.color}"><b>Previsão:</b> {{ tooltip[d.key].prev }}</span>
              </div>
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
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip as ChartTooltip, Legend, Filler,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Title, ChartTooltip, Legend, Filler);

const API_BASE = 'https://www.meusimulador.com/kevi/backend/praticagem';
const ENDPOINT_MESTRE_5M = `${API_BASE}/get_table_mestre_5min_tratada_bq.php`;
const ENDPOINT_PREV = `${API_BASE}/get_prev_correnteza_forecast_bq.php`;

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
const BAND_DELTA = 0.25;

// Campos esperados sempre presentes nos registros mestre
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

// Campos esperados sempre presentes nos registros previsão
const ALL_PREV_COLS = [
  'timestamp_br',
  ...DEPTHS.map((d) => `valor_previsto_${d.key}`),
];

// ========= UTILIDADES ==========

const numOrNull = (v) => {
  if (v === null || v === undefined) return null;
  if (typeof v === 'object' && Object.keys(v).length === 0) return null;
  if (typeof v === 'string' && v.trim() === '') return null;
  const num = Number(v);
  return Number.isFinite(num) ? num : null;
};
const normTs = (t) => (!t ? null : (typeof t === 'string' ? t.replace('T', ' ').slice(0, 19) : (t.date ? String(t.date).replace('T', ' ').slice(0, 19) : String(t))));

// Garante todos os campos existem e são normatizados
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
    if (col.startsWith('valor_previsto_')) out[col] = numOrNull(out[col]);
  });
  out.timestamp_br = normTs(out.timestamp_br);
  return out;
}

// ============ CONTROLE VUE ==============

const config = ref({
  chartHeight: 320,
  showPoints: false,
  showBand: false,
  bandDisplay: 'both',
});
const showConfig = ref(false);
const loading = ref(false);
const error = ref(null);
const prevSource = ref('5min');
const prevSourceLabel = computed(() => (prevSource.value === '5min' ? '5-min' : 'horária'));
const SELECTED_DEPTHS_KEY = 'correntezaSelectedDepths';
function getInitialDepths() {
  const saved = localStorage.getItem(SELECTED_DEPTHS_KEY);
  if (saved) {
    try {
      const val = JSON.parse(saved);
      if (Array.isArray(val) && val.length) return val;
    // eslint-disable-next-line no-empty
    } catch (e) {}
  }
  return ['3m'];
}
const selectedDepthKeys = ref(getInitialDepths());
const visibleDepths = computed(() => DEPTHS.filter((d) => selectedDepthKeys.value.includes(d.key)));
const mestre5min = ref([]); const prevHourly = ref([]); const prev5m = ref([]);
watch(selectedDepthKeys, (val) => {
  localStorage.setItem(SELECTED_DEPTHS_KEY, JSON.stringify(val));
}, { deep: true });

// ================= AJAX =================

async function fetchJSON(url) {
  const res = await fetch(url);
  const json = await res.json();
  if (!json.success) throw new Error(json.erro || `Falha no endpoint: ${url}`);
  return json.data || [];
}

// ========== AJUSTE PRINCIPAL: carregamento e normalização ==========
async function loadAll() {
  loading.value = true; error.value = null;
  try {
    const [mestre, ph, p5] = await Promise.all([
      fetchJSON(`${ENDPOINT_MESTRE_5M}?limit=1000`),
      fetchJSON(`${ENDPOINT_PREV}?tabela=hora&limit=400&include_past=1`),
      fetchJSON(`${ENDPOINT_PREV}?tabela=5min&limit=1000&include_past=1`),
    ]);
    mestre5min.value = mestre.map(normalizeMestreRow)
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
    prevHourly.value = ph.map(normalizePrevRow)
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
    prev5m.value = p5.map(normalizePrevRow)
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));
  } catch (e) { error.value = e.message || String(e); } finally {
    // eslint-disable-next-line no-use-before-define
    DEPTHS.forEach((d) => { cursor[d.key] = maxCursor(d.key); });
    loading.value = false;
  }
}

// ==================== GESTÃO DOS DADOS PARA O GRÁFICO ======================

const windowSize = 288; const stepSize = 144;
const cursor = reactive({}); DEPTHS.forEach((d) => { cursor[d.key] = 0; });

// eslint-disable-next-line no-unused-vars
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
// eslint-disable-next-line no-use-before-define
const prevWindow = (key) => { cursor[key] = Math.max(0, cursor[key] - stepSize); renderChart(key); };
// eslint-disable-next-line no-use-before-define
const nextWindow = (key) => { cursor[key] = Math.min(maxCursor(key), cursor[key] + stepSize); renderChart(key); };
const dataSliceTimestamps = (key) => alignedTimestamps(key).slice(cursor[key], cursor[key] + windowSize);
const windowLabel = (key) => {
  const ts = dataSliceTimestamps(key);
  const ini = ts[0]?.slice(11, 16) || '';
  const fim = ts[ts.length - 1]?.slice(11, 16) || '';
  return ini && fim ? `${ini} – ${fim}` : '';
};

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

  return dataSliceTimestamps(key).map((ts) => {
    const rawHist = histMap.get(ts) ?? null;
    const dirDeg = dirDegMap.get(ts);
    const dirStr = dirStrMap.get(ts);
    const sgn = signByDirection(dirDeg, dirStr);
    const signedHist = (rawHist == null || sgn == null) ? rawHist : rawHist * sgn;

    return {
      timestamp: ts,
      hist: signedHist,          // agora: enchente > 0, vazante < 0
      prev: prevMap.get(ts) ?? null, // previsão permanece como vem
    };
  });
};

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
      hist: hist !== null && hist !== undefined ? Number(hist).toFixed(3) : '--',
      prev: prev !== null && prev !== undefined ? Number(prev).toFixed(3) : '--',
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

const canvasRefs = reactive({});
function setCanvasRef(key, el) { if (el) canvasRefs[key] = el; }
const chartjsObjs = {};
const tooltip = reactive({});
const tooltipStyle = {
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
  left: 'auto',
  top: '-56px',
  right: '8px',
  bottom: 'auto',
};
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

    const datasets = [];
    if (config.value.showBand) {
      const plus = prevVals.map((v) => (v != null ? v + BAND_DELTA : null));
      const minus = prevVals.map((v) => (v != null ? v - BAND_DELTA : null));
      if (config.value.bandDisplay === 'both' || config.value.bandDisplay === 'lines') {
        datasets.push({
          label: `Previsão ${d.label} -${BAND_DELTA.toFixed(2)}`,
          data: minus,
          borderColor: d.color,
          backgroundColor: 'rgba(0,0,0,0)',
          fill: false,
          pointRadius: 1.5,
          borderWidth: 2,
          order: 0,
          hidden: false,
          spanGaps: true,
        });
        datasets.push({
          label: `Previsão ${d.label} +${BAND_DELTA.toFixed(2)}`,
          data: plus,
          borderColor: d.color,
          // eslint-disable-next-line no-use-before-define
          backgroundColor: (config.value.bandDisplay === 'background' || config.value.bandDisplay === 'both') ? hexToRgba(d.color, 0.13) : 'rgba(0,0,0,0)',
          fill: (config.value.bandDisplay === 'background' || config.value.bandDisplay === 'both') ? '-1' : false,
          pointRadius: 1.5,
          borderWidth: 2,
          order: 0,
          hidden: false,
          spanGaps: true,
        });
      } else if (config.value.bandDisplay === 'background') {
        datasets.push({
          label: `Banda previsão ${d.label}`,
          data: plus,
          borderColor: 'rgba(0,0,0,0)',
          // eslint-disable-next-line no-use-before-define
          backgroundColor: hexToRgba(d.color, 0.13),
          // eslint-disable-next-line no-use-before-define
          fill: { target: { value: minus }, above: hexToRgba(d.color, 0.13) },
          pointRadius: 0,
          borderWidth: 0,
          order: 0,
          hidden: false,
          spanGaps: true,
        });
      }
    }
    datasets.push({
      label: `Histórico ${d.label}`,
      data: histVals,
      borderColor: '#1e78db',
      backgroundColor: '#1e78db33',
      tension: 0.32,
      pointRadius,
      borderWidth: 2,
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
        borderWidth: 2,
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
        interaction: { intersect: false, mode: 'nearest', axis: 'x' },
        scales: {
          y: { min: -3, max: 3, title: { display: true, text: 'Intensidade (tk)', font: { size: 14 } } },
          x: { title: { display: true, text: 'Hora', font: { size: 13 } }, ticks: { autoSkip: true, maxTicksLimit: 20 } },
        },
        onHover: (event, elements) => {
          if (!elements.length) { tooltip[key] = { active: false }; lastScrubIdx[key] = null; return; }
          const idx = elements[0].index;
          lastScrubIdx[key] = idx;
          tooltip[key] = {
            active: true,
            label: labels[idx],
            hist: histVals[idx] !== null && histVals[idx] !== undefined ? Number(histVals[idx]).toFixed(3) : '--',
            prev: prevVals[idx] !== null && prevVals[idx] !== undefined ? Number(prevVals[idx]).toFixed(3) : '--',
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

// --- direção → sinal (+1 enchente, -1 vazante) ---
function angDist(a, b) {
  // distância angular mínima entre a e b (0..180)
  return Math.abs(((a - b + 180) % 360) - 180);
}
function dirStringToDeg(s) {
  if (!s) return null;
  const t = String(s).trim().toUpperCase();
  // mapeamento básico; ajuste se vierem outras variações
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
WNW: 292.5,
NW: 315,
NNW: 337.5, // caso venha em inglês
    SSW: 202.5,
SW: 225,
WSW: 247.5,
// eslint-disable-next-line no-dupe-keys
ESE: 112.5,
  };
  // tenta match exato; se vier "N (10°)" ou algo assim, pega o primeiro token
  const token = t.split(/[^A-Z]/)[0];
  if (map[token] != null) return map[token];
  // tenta extrair número em graus
  const m = t.match(/(\d+(?:\.\d+)?)\s*°?/);
  return m ? Number(m[1]) % 360 : null;
}
function signByDirection(deg, str) {
  let d = (deg == null || !Number.isFinite(deg)) ? dirStringToDeg(str) : deg;
  if (d == null) return null; // sem direção → não altera sinal
  // decide pelo mais próximo: 0° (N) = enchente (+), 180° (S) = vazante (-)
  return angDist(d, 0) <= angDist(d, 180) ? +1 : -1;
}

onMounted(async () => {
  await loadAll();
  nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key)));
});
watch(
  [visibleDepths, prevSource, () => config.value.showBand, () => config.value.showPoints, () => config.value.bandDisplay, () => config.value.chartHeight],
  () => nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key))),
  { deep: true },
);
watch(
  [mestre5min, prev5m, prevHourly],
  () => nextTick(() => visibleDepths.value.forEach((d) => renderChart(d.key))),
  { deep: true },
);
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
  max-height: 340px;
  height: 320px;
  position: relative;
}
.tide-toggle { min-width: 180px; }
@media (max-width: 700px) {
  .tide-compare-card { margin-bottom: 12px; }
  .tide-mini-value-card { min-width: 70px; font-size: 0.98em;}
  .tide-chart-container { min-height: 160px; max-height: 220px; height: 180px; }
  .tide-toggle { min-width: 120px; }
}
</style>
