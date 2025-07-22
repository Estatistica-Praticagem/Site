<template>
  <q-dialog v-model="model" persistent>
    <q-card style="min-width:320px;max-width:94vw">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Configurações do Painel</div>
        <q-space/>
        <q-btn dense flat round icon="close" v-close-popup @click="closeConfig"/>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <!-- Idioma -->
        <q-toggle
          v-model="settings.siglaEN"
          label="Usar siglas em inglês (N, SW, NE...)"
          @update:model-value="saveConfig"
        />
        <!-- Exibir/ocultar cards -->
        <div class="q-mt-sm q-mb-sm">
          <q-checkbox v-model="settings.showVento" label="Mostrar card Vento" @update:model-value="saveConfig"/>
          <q-checkbox v-model="settings.showCorrenteza" label="Mostrar card Correnteza" @update:model-value="saveConfig"/>
        </div>
        <!-- Ajuste tamanho -->
        <div class="q-mt-md">
          <q-slider v-model="settings.sizeVento" :min="70" :max="150" step="1" label="Tamanho Vento" @change="saveConfig"/>
          <q-slider v-model="settings.sizeCorrenteza" :min="70" :max="150" step="1" label="Tamanho Correnteza" @change="saveConfig"/>
        </div>
        <!-- Ocultar campos principais -->
        <div class="q-mt-md">
          <q-checkbox v-model="settings.showTemp" label="Temperatura" @update:model-value="saveConfig"/>
          <q-checkbox v-model="settings.showPressao" label="Pressão" @update:model-value="saveConfig"/>
          <q-checkbox v-model="settings.showUmidade" label="Umidade" @update:model-value="saveConfig"/>
          <q-checkbox v-model="settings.showMare" label="Altura Real da Maré" @update:model-value="saveConfig"/>
        </div>
        <!-- Tipo de Cores dos Status -->
        <div class="q-mt-md">
          <q-option-group
            v-model="settings.statusColors"
            :options="[
              { label: 'Padrão', value: 'padrao' },
              { label: 'Personalizado', value: 'custom' }
            ]"
            color="primary"
            inline
            @update:model-value="saveConfig"
          />
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import {
  ref, watch, onMounted, computed, defineProps, defineEmits,
} from 'vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue', 'update:settings']);

const model = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

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
  statusColors: 'custom', // <-- Novo campo (default "custom" se quiser)
};

const settings = ref({ ...defaultSettings });

function saveConfig() {
  localStorage.setItem('weatherPanelConfig', JSON.stringify(settings.value));
  emit('update:settings', { ...settings.value });
}

watch(settings, saveConfig, { deep: true });

function loadConfig() {
  try {
    const data = JSON.parse(localStorage.getItem('weatherPanelConfig'));
    if (data) Object.assign(settings.value, data);
  // eslint-disable-next-line no-empty
  } catch {}
}
onMounted(loadConfig);

function closeConfig() {
  model.value = false;
}
</script>
