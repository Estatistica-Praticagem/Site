<template>
  <div>
    <svg width="36" height="36" viewBox="0 0 36 36">
      <!-- Círculo de fundo (opcional) -->
      <circle v-if="showCircle" cx="18" cy="18" r="16" fill="#e3f2fd" stroke="#1976d2" stroke-width="2"/>
      <circle v-if="showCircle" cx="18" cy="18" r="13" fill="#fff" opacity="0.9"/>

      <!-- Seta -->
      <text
        x="18"
        y="18"
        text-anchor="middle"
        dominant-baseline="middle"
        :font-size="arrowSize"
        :fill="arrowColor"
        :stroke="showBorder ? borderColor : 'none'"
        :stroke-width="showBorder ? 1 : 0"
        :transform="`rotate(${degOut} 18 18)`"
        style="cursor: pointer"
        @click="showConfig = !showConfig"
      >
        {{ selectedArrow }}
      </text>
    </svg>

    <!-- Card de Configuração -->
    <q-card v-if="showConfig" class="q-mt-sm q-pa-md bg-grey-1 shadow-2" style="max-width: 300px">
      <div class="text-subtitle2 q-mb-sm">Configuração da Seta</div>

      <q-toggle v-model="showCircle" label="Mostrar círculo de fundo" class="q-mb-md" />

      <q-slider
        v-model="arrowSize"
        label
        label-always
        :min="10"
        :max="30"
        step="1"
        color="primary"
        class="q-mb-md"
      >
        <template #prepend>Tamanho</template>
      </q-slider>

      <q-toggle v-model="showBorder" label="Mostrar borda" class="q-mb-md" />
      <q-input v-model="arrowColor" label="Cor da seta" type="color" filled class="q-mb-md" />
      <q-input v-model="borderColor" label="Cor da borda" type="color" filled v-if="showBorder" />

      <q-option-group
        v-model="arrowStyle"
        type="radio"
        :options="arrowOptions"
        label="Tipo de seta"
        color="primary"
        class="q-mb-md"
      />

      <div class="row q-gutter-sm q-mt-md">
        <q-btn label="Fechar" color="primary" @click="showConfig = false" flat />
        <q-btn label="Restaurar padrão" color="warning" flat @click="resetToDefault" />
      </div>
    </q-card>
  </div>
</template>

<script setup>
import {
  ref, computed, onMounted, watch,
} from 'vue';

const props = defineProps({
  deg: { type: Number, default: 0 },
});

// Normaliza o ângulo de entrada e inverte (para onde vai = +180°)
const degIn = computed(() => {
  const v = Number(props.deg);
  return Number.isFinite(v) ? ((v % 360) + 360) % 360 : 0;
});
const degOut = computed(() => (degIn.value + 180) % 360);

// LocalStorage global para todas as setas
const configKey = 'globalWindArrowConfig';

const showConfig = ref(false);

// Configurações da seta
const arrowSize = ref(18);
const arrowColor = ref('#1976d2');
const showBorder = ref(true);
const borderColor = ref('#0d47a1');
const showCircle = ref(true);
const arrowStyle = ref('arrow1');

const arrowOptions = [
  { label: 'Seta clássica ↑', value: 'arrow1' },
  { label: 'Seta sólida ▲', value: 'arrow2' },
  { label: 'Seta teclado ⇧', value: 'arrow3' },
];

// Mapeia estilo escolhido para caractere real
const selectedArrow = computed(() => {
  switch (arrowStyle.value) {
    case 'arrow2': return '▲';
    case 'arrow3': return '⇧';
    default: return '↑';
  }
});

// Carrega configuração ao montar
onMounted(() => {
  const saved = localStorage.getItem(configKey);
  if (saved) {
    try {
      const cfg = JSON.parse(saved);
      arrowSize.value = cfg.arrowSize ?? 18;
      arrowColor.value = cfg.arrowColor ?? '#1976d2';
      showBorder.value = cfg.showBorder ?? true;
      borderColor.value = cfg.borderColor ?? '#0d47a1';
      showCircle.value = cfg.showCircle ?? true;
      arrowStyle.value = cfg.arrowStyle ?? 'arrow1';
    } catch (e) {
      console.warn('Erro ao carregar config global:', e);
    }
  }
});

// Salva qualquer mudança
watch(
  [arrowSize, arrowColor, showBorder, borderColor, showCircle, arrowStyle],
  () => {
    const config = {
      arrowSize: arrowSize.value,
      arrowColor: arrowColor.value,
      showBorder: showBorder.value,
      borderColor: borderColor.value,
      showCircle: showCircle.value,
      arrowStyle: arrowStyle.value,
    };
    localStorage.setItem(configKey, JSON.stringify(config));
  },
  { deep: true },
);

// Reset
function resetToDefault() {
  arrowSize.value = 18;
  arrowColor.value = '#1976d2';
  showBorder.value = true;
  borderColor.value = '#0d47a1';
  showCircle.value = true;
  arrowStyle.value = 'arrow1';
}
</script>
