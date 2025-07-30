<template>
  <div class="q-pa-md">
    <!-- Controles globais -->
    <q-card class="q-pa-sm q-mb-md shadow-2 bg-white">
      <div class="row items-center justify-between">
        <div class="row items-center q-gutter-sm">
          <q-option-group
            v-model="showPoints"
            :options="[
              { label: 'Com bolinhas', value: true },
              { label: 'Sem bolinhas', value: false }
            ]"
            type="radio"
            color="primary"
            inline
          />
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
        </div>
      </div>
    </q-card>

    <!-- Cards por profundidade -->
    <div class="row q-col-gutter-md">
      <div
        v-for="d in visibleDepths"
        :key="d.key"
        class="col-12 col-md-6 col-lg-4"
      >
        <q-card class="q-pa-md bg-white shadow-2 tide-compare-card">
          <div class="row items-center justify-between q-mb-sm">
            <div class="text-subtitle1 text-primary text-weight-bold">
              Intensidade {{ d.label }} — Histórico x Previsão ({{ prevSourceLabel }})
            </div>
            <div v-if="latestTimestamps[d.key]" class="text-caption text-grey-7">
              Atualizado: {{ latestTimestamps[d.key] }}
            </div>
          </div>

          <div class="tide-chart-container">
            <Line
              :data="chartDataByDepth[d.key]"
              :options="chartOptionsByDepth[d.key]"
              :height="320"
            />
          </div>
        </q-card>
      </div>
    </div>

    <div class="q-mt-md text-negative" v-if="error">{{ error }}</div>
  </div>
</template>

<script setup>
import {
  ref, computed, onMounted, onUnmounted,
} from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend,
} from 'chart.js';

Chart.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

/* ======================= CONFIG ======================= */
const API_BASE = 'https://www.meusimulador.com/kevi/backend/praticagem';

// Endpoints
const ENDPOINT_MESTRE_5M = `${API_BASE}/get_table_mestre_5min_tratada_bq.php`; // ?limit=...
const ENDPOINT_PREV = `${API_BASE}/get_prev_correnteza_forecast_bq.php`; // ?tabela=hora|5min&limit=...&include_past=1

// Profundidades
const DEPTHS = [
  { key: '1_5m', label: '1.5m' },
  { key: '3m', label: '3m' },
  { key: '6m', label: '6m' },
  { key: '7_5m', label: '7.5m' },
  { key: '9m', label: '9m' },
  { key: '10_5m', label: '10.5m' },
  { key: '12m', label: '12m' },
  { key: '13_5m', label: '13.5m' },
  { key: 'superficie', label: 'Superfície' },
];

// Helpers de nome de coluna
const mestreIntensityCol = (depthKey) => `intensidade_${depthKey}`;
const prevCol = (depthKey) => `valor_previsto_${depthKey}`;

/* ======================= STATE ======================= */
const loading = ref(false);
const error = ref(null);
const showPoints = ref(false);
const prevSource = ref('5min'); // '5min' | 'hora'
const prevSourceLabel = computed(() => (prevSource.value === '5min' ? '5-min' : 'horária'));

// Seletor de cards
const depthOptions = DEPTHS.map((d) => ({ label: d.label, key: d.key }));
const selectedDepthKeys = ref(DEPTHS.map((d) => d.key)); // mostra todos por padrão
const visibleDepths = computed(() => DEPTHS.filter((d) => selectedDepthKeys.value.includes(d.key)));

// Responsividade (tamanho dos pontos)
const isMobile = ref(false);
const updateIsMobile = () => { isMobile.value = window.innerWidth < 700; };
onMounted(() => {
  updateIsMobile();
  window.addEventListener('resize', updateIsMobile);
  // eslint-disable-next-line no-use-before-define
  loadAll();
});
onUnmounted(() => window.removeEventListener('resize', updateIsMobile));

/* ======================= FETCH & NORMALIZAÇÃO ======================= */
async function fetchJSON(url) {
  const res = await fetch(url);
  const json = await res.json();
  if (!json.success) {
    throw new Error(json.erro || `Falha no endpoint: ${url}`);
  }
  return json.data || [];
}

// NUMERIC vindo como number/string/{} -> number|null
const numOrNull = (v) => {
  if (v === null || v === undefined) return null;
  if (typeof v === 'number') return Number.isFinite(v) ? v : null;
  if (typeof v === 'string') {
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
  }
  if (typeof v === 'object') return null; // caso do {}
  return null;
};

// timestamp string ou {date: "..."} -> "YYYY-MM-DD HH:mm:ss"
const normTs = (t) => {
  if (!t) return null;
  if (typeof t === 'string') return t.replace('T', ' ').slice(0, 19);
  if (t.date) return String(t.date).replace('T', ' ').slice(0, 19);
  return String(t);
};

/* ======================= DATASETS ======================= */
// Histórico (mestre 5-min)
const mestre5min = ref([]);
// Previsões
const prevHourly = ref([]);
const prev5m = ref([]);

async function loadAll() {
  loading.value = true;
  error.value = null;
  try {
    // Limites ALTOS para trazer TUDO que houver nas tabelas
    const masterLimit = 20000; // 5-min (grande folga)
    const prevHourLim = 10000; // horária (folga)
    const prev5mLim = 20000; // 5-min (folga)

    const includePast = '&include_past=1'; // se o PHP ignorar, não faz mal

    const [mestre, ph, p5] = await Promise.all([
      fetchJSON(`${ENDPOINT_MESTRE_5M}?limit=${masterLimit}`),
      fetchJSON(`${ENDPOINT_PREV}?tabela=hora&limit=${prevHourLim}${includePast}`),
      fetchJSON(`${ENDPOINT_PREV}?tabela=5min&limit=${prev5mLim}${includePast}`),
    ]);

    // ---- Histórico
    mestre5min.value = (mestre || [])
      .map((r) => {
        const out = { ...r };
        out.timestamp_br = normTs(r.timestamp_br);
        DEPTHS.forEach((d) => {
          const c = mestreIntensityCol(d.key);
          if (c in out) out[c] = numOrNull(out[c]);
        });
        return out;
      })
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));

    // ---- Prev horária
    prevHourly.value = (ph || [])
      .map((r) => {
        const out = { ...r, timestamp_br: normTs(r.timestamp_br) };
        DEPTHS.forEach((d) => { out[prevCol(d.key)] = numOrNull(r[prevCol(d.key)]); });
        return out;
      })
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));

    // ---- Prev 5-min
    prev5m.value = (p5 || [])
      .map((r) => {
        const out = { ...r, timestamp_br: normTs(r.timestamp_br) };
        DEPTHS.forEach((d) => { out[prevCol(d.key)] = numOrNull(r[prevCol(d.key)]); });
        return out;
      })
      .sort((a, b) => (a.timestamp_br > b.timestamp_br ? 1 : -1));

    // eslint-disable-next-line no-console
    console.debug('linhas (tudo):', {
      mestre5min: mestre5min.value.length,
      prevHora: prevHourly.value.length,
      prev5min: prev5m.value.length,
    });
  } catch (e) {
    error.value = e.message || String(e);
  } finally {
    loading.value = false;
  }
}

/* ======================= COMUNS POR PROFUNDIDADE ======================= */
// último timestamp válido do histórico por profundidade
const latestTimestamps = computed(() => {
  const out = {};
  DEPTHS.forEach((d) => {
    const col = mestreIntensityCol(d.key);
    const valid = mestre5min.value.filter((r) => r[col] != null);
    out[d.key] = valid.length ? valid[valid.length - 1].timestamp_br : null;
  });
  return out;
});

// escolhe a fonte de previsão respeitando o toggle e com fallback
function pickPrevRowsFor(depthKey) {
  const colPrev = prevCol(depthKey);
  const toggleRows = prevSource.value === '5min' ? prev5m.value : prevHourly.value;
  const altRows = prevSource.value === '5min' ? prevHourly.value : prev5m.value;

  const hasToggle = toggleRows.some((r) => numOrNull(r[colPrev]) != null);
  if (hasToggle) return toggleRows;

  const hasAlt = altRows.some((r) => numOrNull(r[colPrev]) != null);
  return hasAlt ? altRows : toggleRows;
}

// rótulo MM-DD HH:mm
// eslint-disable-next-line camelcase
const labelMMDD_HHMM = (ts) => (ts ? ts.slice(5, 16) : '');

// monta séries alinhadas por timestamp (união histórico + previsão)
function buildSeries(depthKey) {
  const colHist = mestreIntensityCol(depthKey);
  const colPrev = prevCol(depthKey);

  const hist = mestre5min.value.map((r) => r.timestamp_br);
  const prevRows = pickPrevRowsFor(depthKey);
  const prev = prevRows.map((r) => r.timestamp_br);

  const labels = Array.from(new Set([...hist, ...prev])).filter(Boolean).sort();

  const histMap = new Map(mestre5min.value.map((r) => [r.timestamp_br, numOrNull(r[colHist])]));
  const prevMap = new Map(prevRows.map((r) => [r.timestamp_br, numOrNull(r[colPrev])]));

  return {
    labels,
    histVals: labels.map((ts) => histMap.get(ts) ?? null),
    prevVals: labels.map((ts) => prevMap.get(ts) ?? null),
  };
}

/* ======================= CHARTS ======================= */
const chartDataByDepth = computed(() => {
  const out = {};
  const pointRadius = showPoints.value ? (isMobile.value ? 1 : 2) : 0;

  visibleDepths.value.forEach((d) => {
    const { labels, histVals, prevVals } = buildSeries(d.key);

    out[d.key] = {
      labels: labels.map(labelMMDD_HHMM),
      datasets: [
        {
          label: `Histórico ${d.label} (mestre)`,
          data: histVals,
          borderColor: '#1e78db',
          backgroundColor: '#1e78db',
          tension: 0.28,
          pointRadius,
          borderWidth: 2,
        },
        {
          label: `Previsão ${d.label} (${prevSourceLabel.value})`,
          data: prevVals,
          borderColor: '#ff6d00',
          backgroundColor: '#ff6d00',
          borderDash: [6, 4],
          tension: 0.25,
          pointRadius,
          borderWidth: 2,
        },
      ],
    };
  });

  return out;
});

const commonChartOptions = (titleText) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: { boxWidth: 16, font: { size: 13 } },
    },
    title: { display: !!titleText, text: titleText },
    tooltip: { callbacks: { title: (items) => (items?.[0]?.label ?? '') } },
  },
  scales: {
    y: {
      title: { display: true, text: 'Intensidade (tk)' },
      suggestedMin: -3,
      suggestedMax: 3,
      grid: { color: (ctx) => (ctx.tick.value === 0 ? '#00000055' : undefined) },
    },
    x: {
      title: { display: true, text: 'Hora' },
      ticks: { autoSkip: true, maxTicksLimit: 20 },
    },
  },
});

const chartOptionsByDepth = computed(() => {
  const out = {};
  visibleDepths.value.forEach((d) => {
    out[d.key] = commonChartOptions(`Profundidade ${d.label}`);
  });
  return out;
});
</script>

<style scoped>
.tide-compare-card {
  border-radius: 14px;
}
.tide-chart-container {
  width: 100%;
  min-height: 240px;
  max-height: 340px;
  height: 320px;
  position: relative;
}
.tide-toggle {
  min-width: 180px;
}
@media (max-width: 700px) {
  .tide-chart-container {
    min-height: 160px;
    max-height: 220px;
    height: 180px;
  }
  .tide-toggle {
    min-width: 120px;
  }
}
</style>
