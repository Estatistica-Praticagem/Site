<template>
  <div style="display:flex;align-items:center;">
    <svg
      :width="size"
      :height="size"
      :viewBox="`0 0 ${size} ${size}`"
      class="rose-svg"
      :style="{ background: 'transparent', display:'block', cursor:'pointer' }"
      @click="showConfig = true"
    >
      <defs>
        <!-- Gradiente dinâmico (tintado quando colorScope === 'both') -->
        <radialGradient :id="gradId" cx="50%" cy="50%" r="60%">
          <stop offset="0%" :stop-color="gradStop1" />
          <stop offset="98%" :stop-color="gradStop2" />
        </radialGradient>

        <filter :id="shadowId" x="-10%" y="-10%" width="120%" height="120%">
          <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#90caf9" flood-opacity="0.11" />
        </filter>
      </defs>

      <!-- ARCO DE INTENSIDADE (fora do círculo principal) -->
      <g :transform="`rotate(-135 ${center} ${center})`" v-if="showArc">
        <path
          :d="arcPath"
          :stroke="pointerColor"
          :stroke-width="size * 0.08"
          fill="none"
          stroke-linecap="round"
          style="filter: drop-shadow(0 1px 4px #eee8);"
        />
      </g>

      <!-- Círculo/anel externo + linhas de referência (opcional / sólido ou transparente) -->
      <template v-if="showBackground">
        <circle
          :cx="center"
          :cy="center"
          :r="center-3"
          :fill="`url(#${gradId})`"
          stroke="#b3e5fc"
          stroke-width="3"
          :filter="`url(#${shadowId})`"
        />
        <g v-if="showGuides">
          <line :x1="center" :y1="center-((center-3)*0.82)" :x2="center" :y2="center+((center-3)*0.82)" stroke="#bbccdd" stroke-width="2.5"/>
          <line :x1="center-((center-3)*0.82)" :y1="center" :x2="center+((center-3)*0.82)" :y2="center" stroke="#bbccdd" stroke-width="2.5"/>
          <line :x1="center-(center*0.59)" :y1="center-(center*0.59)" :x2="center+(center*0.59)" :y2="center+(center*0.59)" stroke="#e1e8ee" stroke-width="1.3"/>
          <line :x1="center-(center*0.59)" :y1="center+(center*0.59)" :x2="center+(center*0.59)" :y2="center-(center*0.59)" stroke="#e1e8ee" stroke-width="1.3"/>
        </g>
      </template>

      <!-- Direções (N S L/O E) -->
      <g v-if="lettersMode !== 'hide'">
        <text
          :x="center" :y="center-(center*0.73)"
          text-anchor="middle" :font-size="size*0.094"
          :fill="lettersFill" font-weight="bold"
          :opacity="lettersOpacity"
        >{{ labels.N }}</text>

        <text
          :x="center" :y="center+(center*0.87)"
          text-anchor="middle" :font-size="size*0.094"
          :fill="lettersFill" font-weight="bold"
          :opacity="lettersOpacity"
        >{{ labels.S }}</text>

        <text
          :x="center-(center*0.75)" :y="center+7"
          text-anchor="middle" :font-size="size*0.094"
          :fill="lettersFill" font-weight="bold"
          :opacity="lettersOpacity"
        >{{ labels.W }}</text>

        <text
          :x="center+(center*0.75)" :y="center+7"
          text-anchor="middle" :font-size="size*0.094"
          :fill="lettersFill" font-weight="bold"
          :opacity="lettersOpacity"
        >{{ labels.E }}</text>

        <text
          :x="center-(center*0.52)" :y="center-(center*0.55)"
          text-anchor="middle" :font-size="size*0.07"
          :fill="lettersSubFill" :opacity="lettersOpacity"
        >{{ labels.NW }}</text>

        <text
          :x="center+(center*0.52)" :y="center-(center*0.55)"
          text-anchor="middle" :font-size="size*0.07"
          :fill="lettersSubFill" :opacity="lettersOpacity"
        >{{ labels.NE }}</text>

        <text
          :x="center-(center*0.52)" :y="center+(center*0.63)"
          text-anchor="middle" :font-size="size*0.07"
          :fill="lettersSubFill" :opacity="lettersOpacity"
        >{{ labels.SW }}</text>

        <text
          :x="center+(center*0.52)" :y="center+(center*0.63)"
          text-anchor="middle" :font-size="size*0.07"
          :fill="lettersSubFill" :opacity="lettersOpacity"
        >{{ labels.SE }}</text>
      </g>

      <!-- PONTEIRO (estilo selecionado) -->
      <!-- a) Polígono/haste -->
      <g v-if="arrowStyle === 'poly'" :style="pointerStyle">
        <polygon
          :points="polygonPoints"
          :fill="pointerColorFill"
          :opacity="0.28"
          :filter="`url(#${shadowId})`"
        />
        <polygon
          :points="polygonPoints"
          :fill="pointerColor"
          :stroke="showBorder ? borderColor : 'none'"
          :stroke-width="showBorder ? 2 : 0"
          style="transition: fill .3s;"
        />
        <line
          :x1="center" :y1="center"
          :x2="center" :y2="center-len"
          :stroke="pointerColor"
          :stroke-width="4 * arrowScale"
          stroke-linecap="round"
          opacity="0.6"
        />
      </g>

      <!-- b) Setas via caractere (↑ ▲ ⇧) -->
      <text
        v-else
        :x="center"
        :y="center - (size * 0.38)"
        text-anchor="middle"
        dominant-baseline="middle"
        :font-size="glyphFontSize"
        :fill="pointerColor"
        :stroke="showBorder ? borderColor : 'none'"
        :stroke-width="showBorder ? 1 : 0"
        :transform="`rotate(${pointerAngle} ${center} ${center})`"
        style="transition: transform .25s, fill .2s"
      >
        {{ glyphChar }}
      </text>

      <!-- Centro / Valor -->
      <circle :cx="center" :cy="center" :r="size*0.17" fill="#fff" stroke="#b3e5fc" stroke-width="2" />
      <text
        :x="center"
        :y="center+(size*0.075)"
        text-anchor="middle"
        :font-size="size*0.17"
        fill="#1565c0"
        font-weight="bold"
        style="font-variant-numeric: tabular-nums;"
      >{{ intVal }}</text>
      <text :x="center" :y="center+(size*0.22)" text-anchor="middle" :font-size="size*0.075" fill="#666">kts</text>
    </svg>

    <!-- Card de Configuração -->
    <q-dialog v-model="showConfig" persistent>
      <q-card style="width:min(480px,96vw)">
        <q-card-section class="row items-center q-pa-sm">
          <div class="text-h6 text-primary">Configurações — Vento</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showConfig = false" />
        </q-card-section>
        <q-separator />

        <q-card-section class="q-gutter-md">
          <!-- Fundo -->
          <div>
            <div class="text-bold q-mb-xs">Fundo do relógio</div>
            <q-option-group
              v-model="bgMode"
              type="radio"
              color="primary"
              :options="[
                { label: 'Sólido (com anel e guias)', value: 'solid' },
                { label: 'Transparente', value: 'transparent' }
              ]"
            />
            <q-toggle v-if="bgMode==='solid'" v-model="showGuides" label="Mostrar linhas/diagonais" color="primary"/>
          </div>

          <!-- Onde aplicar a cor -->
          <div>
            <div class="text-bold q-mb-xs">Aplicar cor do status</div>
            <q-option-group
              v-model="colorScope"
              type="radio"
              color="primary"
              :options="[
                { label: 'Somente seta', value: 'arrow' },
                { label: 'Seta e relógio', value: 'both' }
              ]"
            />
          </div>

          <!-- Modo de cor -->
          <div>
            <div class="text-bold q-mb-xs">Modo de cor</div>
            <q-option-group
              v-model="colorMode"
              type="radio"
              color="primary"
              :options="[
                { label: 'Automático (≤15 verde, ≤35 amarelo, >35 vermelho)', value: 'auto' },
                { label: 'Personalizado por faixa', value: 'custom' },
                { label: 'Manual (cor fixa)', value: 'manual' }
              ]"
            />
            <div v-if="colorMode==='custom'" class="row items-center q-gutter-sm q-mt-xs">
              <q-input v-model="customLow"  type="color" label="0–15"    filled dense style="max-width:140px"/>
              <q-input v-model="customMid"  type="color" label="15–35"   filled dense style="max-width:140px"/>
              <q-input v-model="customHigh" type="color" label="> 35"    filled dense style="max-width:140px"/>
            </div>
            <q-input
              v-if="colorMode==='manual'"
              v-model="manualColor"
              type="color"
              label="Cor fixa"
              filled dense class="q-mt-xs" />
          </div>

          <!-- Seta -->
          <div>
            <div class="text-bold q-mb-xs">Tipo de seta</div>
            <q-option-group
              v-model="arrowStyle"
              type="radio"
              color="primary"
              :options="[
                { label: 'Polígono/haste', value: 'poly' },
                { label: '↑ (clássica)', value: 'glyph1' },
                { label: '▲ (sólida)', value: 'glyph2' },
                { label: '⇧ (teclado)', value: 'glyph3' }
              ]"
            />
            <div class="q-mt-xs">
              <div class="text-caption">Tamanho da seta</div>
              <q-slider v-model="arrowScale" :min="0.7" :max="1.6" :step="0.05" color="primary" label/>
            </div>
            <q-toggle v-model="showBorder" label="Mostrar borda da seta" color="primary" class="q-mt-xs"/>
            <q-input v-if="showBorder" v-model="borderColor" type="color" label="Cor da borda" filled dense />
          </div>

          <!-- Letras -->
          <div>
            <div class="text-bold q-mb-xs">Letras (rosa dos ventos)</div>
            <q-option-group
              v-model="lettersMode"
              type="radio"
              color="primary"
              :options="[
                { label: 'Mostrar', value: 'show' },
                { label: 'Opacas', value: 'faint' },
                { label: 'Ocultar', value: 'hide' }
              ]"
            />
            <div class="q-mt-xs">
              <div class="text-caption">Idioma das letras</div>
              <q-option-group
                v-model="langChoice"
                type="radio"
                color="primary"
                :options="[
                  { label: 'Usar do pai (prop)', value: 'auto' },
                  { label: 'Português', value: 'pt' },
                  { label: 'Inglês', value: 'en' }
                ]"
              />
            </div>
          </div>

          <!-- Outros -->
          <div>
            <q-toggle v-model="showArc" label="Mostrar arco de intensidade externo" color="primary"/>
          </div>
        </q-card-section>

        <q-separator />
        <q-card-actions align="right">
          <q-btn flat label="Fechar" color="primary" v-close-popup />
          <q-btn flat label="Padrões" color="primary" @click="resetToDefault"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';

const props = defineProps({
  direction:   { type: Number, default: 0 },
  intensidade: { type: Number, default: 0 },  // kts
  max:         { type: Number, default: 20 },
  lang:        { type: String, default: 'pt' },
  size:        { type: Number, default: 110 },
});

/* -------------------- Rotulagem por idioma -------------------- */
const dirMap = {
  pt: { N: 'N', S: 'S', E: 'L', W: 'O', NE: 'NE', NW: 'NO', SE: 'SE', SW: 'SO' },
  en: { N: 'N', S: 'S', E: 'E', W: 'W', NE: 'NE', NW: 'NW', SE: 'SE', SW: 'SW' },
};
const center = computed(() => props.size / 2);

/* -------------------- Config persistente -------------------- */
const cfgKey = 'windRoseConfig_v2';
const showConfig   = ref(false);

/* Fundo / estrutura */
const bgMode     = ref('solid');     // 'solid' | 'transparent'
const showGuides = ref(true);
const showArc    = ref(true);

/* Cor */
const colorScope  = ref('arrow');    // 'arrow' | 'both'
const colorMode   = ref('auto');     // 'auto' | 'custom' | 'manual'
const manualColor = ref('#1976d2');
const customLow   = ref('#43a047');
const customMid   = ref('#fbc02d');
const customHigh  = ref('#e53935');

/* Seta */
const arrowStyle  = ref('glyph3');   // 'poly' | 'glyph1' | 'glyph2' | 'glyph3'
const arrowScale  = ref(1.1);
const showBorder  = ref(false);
const borderColor = ref('#0d47a1');

/* Letras */
const lettersMode = ref('faint');    // 'show' | 'faint' | 'hide'
const langChoice  = ref('auto');     // 'auto' | 'pt' | 'en'

/* Salvar/carregar */
onMounted(() => {
  const saved = localStorage.getItem(cfgKey);
  if (!saved) return;
  try {
    const o = JSON.parse(saved);
    bgMode.value     = o.bgMode ?? bgMode.value;
    showGuides.value = o.showGuides ?? showGuides.value;
    showArc.value    = o.showArc ?? showArc.value;

    colorScope.value  = o.colorScope ?? colorScope.value;
    colorMode.value   = o.colorMode ?? colorMode.value;
    manualColor.value = o.manualColor ?? manualColor.value;
    customLow.value   = o.customLow ?? customLow.value;
    customMid.value   = o.customMid ?? customMid.value;
    customHigh.value  = o.customHigh ?? customHigh.value;

    arrowStyle.value  = o.arrowStyle ?? arrowStyle.value;
    arrowScale.value  = o.arrowScale ?? arrowScale.value;
    showBorder.value  = o.showBorder ?? showBorder.value;
    borderColor.value = o.borderColor ?? borderColor.value;

    lettersMode.value = o.lettersMode ?? lettersMode.value;
    langChoice.value  = o.langChoice ?? langChoice.value;
  } catch {}
});
watch(
  [bgMode, showGuides, showArc, colorScope, colorMode, manualColor, customLow, customMid, customHigh, arrowStyle, arrowScale, showBorder, borderColor, lettersMode, langChoice],
  () => {
    const o = {
      bgMode: bgMode.value,
showGuides: showGuides.value,
showArc: showArc.value,
      colorScope: colorScope.value,
colorMode: colorMode.value,
manualColor: manualColor.value,
      customLow: customLow.value,
customMid: customMid.value,
customHigh: customHigh.value,
      arrowStyle: arrowStyle.value,
arrowScale: arrowScale.value,
showBorder: showBorder.value,
      borderColor: borderColor.value,
lettersMode: lettersMode.value,
langChoice: langChoice.value,
    };
    localStorage.setItem(cfgKey, JSON.stringify(o));
  },
  { deep: true }
);

/* -------------------- Letras/idioma -------------------- */
const langEffective = computed(() => {
  if (langChoice.value === 'pt' || langChoice.value === 'en') return langChoice.value;
  return props.lang === 'en' ? 'en' : 'pt';
});
const labels = computed(() => dirMap[langEffective.value] || dirMap.pt);
const lettersFill = computed(() => '#1976d2');
const lettersSubFill = computed(() => '#64b5f6');
const lettersOpacity = computed(() => (lettersMode.value === 'faint' ? 0.45 : 0.95));
const showBackground = computed(() => bgMode.value === 'solid');

/* -------------------- Cores e gradiente -------------------- */
function colorFromKts(kts) {
  // eslint-disable-next-line no-restricted-globals
  if (kts == null || !isFinite(kts)) return manualColor.value;
  if (colorMode.value === 'manual') return manualColor.value;
  if (colorMode.value === 'custom') {
    if (kts > 35) return customHigh.value;
    if (kts >= 15) return customMid.value;
    return customLow.value;
  }
  // auto
  if (kts > 35) return '#e53935';
  if (kts >= 15) return '#fbc02d';
  return '#43a047';
}
const pointerColor = computed(() => colorFromKts(props.intensidade));
const pointerColorFill = computed(() => withAlpha(pointerColor.value, 0.28));

const gradBase1 = '#fafdff';
const gradBase2 = '#90caf9';
const gradTint1 = computed(() => mix(pointerColor.value, '#ffffff', 0.8)); // bem claro
const gradTint2 = computed(() => mix(pointerColor.value, '#ffffff', 0.25)); // mais saturado
const gradStop1 = computed(() => (colorScope.value === 'both' ? gradTint1.value : gradBase1));
const gradStop2 = computed(() => (colorScope.value === 'both' ? gradTint2.value : gradBase2));

/* -------------------- Geometria e ponteiro -------------------- */
// Inverte direção (+180°) e mapeia para rotação SVG
const pointerAngle = computed(() => {
  const dir = Number.isFinite(props.direction) ? props.direction : 0;
  const angle = 90 - ((dir + 180) % 360); // 0° (N) aponta para cima
  return ((angle % 360) + 360) % 360;
});
const len = computed(() => center.value * (0.55 + 0.35 * Math.min((props.intensidade || 0) / Math.max(props.max, 1), 1)));

// eslint-disable-next-line no-restricted-globals
const intVal = computed(() => (isNaN(props.intensidade) ? '--' : Number(props.intensidade).toFixed(2)));

const pointerStyle = computed(() => ({
  transform: `rotate(${pointerAngle.value}deg)`,
  transformOrigin: `${center.value}px ${center.value}px`,
  transition: 'transform .28s cubic-bezier(.65,.05,.36,1), filter .25s',
}));
const polygonPoints = computed(() => {
  const l = len.value;
  const c = center.value;
  const w = props.size * 0.045 * arrowScale.value;
  return `
    ${c},${c - l}
    ${c + w},${c - l + props.size * 0.12 * arrowScale.value}
    ${c},${c - l + props.size * 0.07 * arrowScale.value}
    ${c - w},${c - l + props.size * 0.12 * arrowScale.value}
  `;
});

/* Setas via caractere */
const glyphChar = computed(() => {
  if (arrowStyle.value === 'glyph2') return '▲';
  if (arrowStyle.value === 'glyph3') return '⇧';
  return '↑';
});
const glyphFontSize = computed(() => (props.size * 0.36) * arrowScale.value);

/* -------------------- Arco externo -------------------- */
function polarToCartesian(cx, cy, r, angleInDegrees) {
  const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;
  return { x: cx + (r * Math.cos(angleInRadians)), y: cy + (r * Math.sin(angleInRadians)) };
}
function describeArc(cx, cy, r, startAngle, endAngle) {
  const start = polarToCartesian(cx, cy, r, endAngle);
  const end = polarToCartesian(cx, cy, r, startAngle);
  const arcSweep = endAngle - startAngle <= 180 ? '0' : '1';
  return ['M', start.x, start.y, 'A', r, r, 0, arcSweep, 0, end.x, end.y].join(' ');
}
const arco = computed(() => (Math.min(props.intensidade ?? 0, props.max) / Math.max(props.max, 1)) * 270);
const arcPath = computed(() => describeArc(center.value, center.value, center.value - 1, 0, arco.value));

/* -------------------- Utils de cor -------------------- */
function withAlpha(hex, a = 1) {
  const { r, g, b } = hexToRgb(hex);
  return `rgba(${r},${g},${b},${a})`;
}
function hexToRgb(hex) {
  const h = hex.replace('#', '');
  const bigint = parseInt(h.length === 3 ? h.split('').map(x => x+x).join('') : h, 16);
  // eslint-disable-next-line no-bitwise
  return { r: (bigint >> 16) & 255, g: (bigint >> 8) & 255, b: bigint & 255 };
}
function clamp01(x) { return Math.max(0, Math.min(1, x)); }
function mix(hexA, hexB, t) {
  const a = hexToRgb(hexA); const b = hexToRgb(hexB);
  const p = clamp01(t);
  const r = Math.round(a.r + (b.r - a.r) * p);
  const g = Math.round(a.g + (b.g - a.g) * p);
  const b2 = Math.round(a.b + (b.b - a.b) * p);
  return `rgb(${r},${g},${b2})`;
}

/* -------------------- Gradiente/IDs únicos -------------------- */
const uid = Math.random().toString(36).slice(2, 8);
const gradId = `grad2_${uid}`;
const shadowId = `shadow_${uid}`;

/* -------------------- Reset -------------------- */
function resetToDefault() {
  bgMode.value = 'solid';
  showGuides.value = true;
  showArc.value = true;

  colorScope.value = 'arrow';
  colorMode.value = 'auto';
  manualColor.value = '#1976d2';
  customLow.value = '#43a047';
  customMid.value = '#fbc02d';
  customHigh.value = '#e53935';

  arrowStyle.value = 'glyph3';
  arrowScale.value = 1.1;
  showBorder.value = false;
  borderColor.value = '#0d47a1';

  lettersMode.value = 'faint';
  langChoice.value = 'auto';
}
</script>

<style scoped>
.rose-svg {
  display: block;
}
</style>
