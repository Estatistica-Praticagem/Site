<template>
  <div>
    <!-- TOGGLE DE MODO -->
    <div class="q-mb-md flex items-center gap-4 justify-between">
      <q-btn-toggle
        v-model="mode"
        dense
        toggle-color="primary"
        color="white"
        text-color="primary"
        unelevated
        :options="[
          { label: 'Detalhado', value: 'detailed' },
          { label: 'Compacto', value: 'compact' }
        ]"
      />
      <!-- Botões de dia aparecem só no modo detalhado -->
      <div v-if="mode === 'detailed'" class="flex gap-2 items-center">
        <div class="font-bold text-base text-grey-8">Escolha o dia:</div>
        <q-btn
          v-for="opt in availableDays"
          :key="opt.value"
          @click="selectedDay = opt.value"
          color="white"
          :text-color="selectedDay === opt.value ? 'primary' : 'grey-8'"
          :unelevated="true"
          :outline="selectedDay !== opt.value"
          :class="selectedDay === opt.value ? 'btn-dia-ativo' : ''"
          class="btn-dia"
          size="sm"
        >
          <div class="flex flex-col items-center">
            <span class="text-base">{{ formatDayShort(opt.value) }}</span>
            <span class="text-xs text-grey-6">{{ getWeekdayLabel(opt.value) }}</span>
          </div>
        </q-btn>
      </div>
    </div>

    <!-- Só UM dos modos renderiza por vez -->
    <ForecastGrid
      v-if="mode === 'detailed'"
      :day="selectedDay"
      :grouped-by-day="groupedByDay"
      city-label="Previsão Horária"
    />

    <WeatherForecastCompact
      v-else-if="mode === 'compact'"
      :days="days"
    />
  </div>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue';
import { useWeatherStore } from 'src/stores/weather';
import WeatherForecastCompact from 'src/components/praticagem/WeatherForecastVisibility/WeatherForecastCompact.vue';
import ForecastGrid from 'src/components/praticagem/WeatherForecastVisibility/WeatherForecastGrid.vue';

const store = useWeatherStore();
const forecast = computed(() => store.openWeatherForecast || []);

// Agrupa por dia: PEGA O DIA DO JSON EXATAMENTE COMO VEM!
const groupedByDay = computed(() => {
  const res = {};
  forecast.value.forEach((item) => {
    const day = item.dt_txt?.slice(0, 10); // ex: '2025-07-26'
    if (!day) return;
    res[day] = res[day] || [];
    res[day].push(item);
  });
  return res;
});

const availableDays = computed(() => Object.keys(groupedByDay.value)
  .sort()
  .map((date) => ({ value: date })));

const mode = ref('detailed');
const selectedDay = ref('');
watchEffect(() => {
  if (!selectedDay.value && availableDays.value.length) selectedDay.value = availableDays.value[0].value;
});

// Helpers para formatar labels
function formatDayShort(dia) {
  // '2025-07-26' => '26/07'
  if (!dia || !/^\d{4}-\d{2}-\d{2}$/.test(dia)) return dia;
  // eslint-disable-next-line no-unused-vars
  const [yyyy, mm, dd] = dia.split('-');
  return `${dd}/${mm}`;
}
function formatDay(dia) {
  if (!dia || !/^\d{4}-\d{2}-\d{2}$/.test(dia)) return dia;
  const [yyyy, mm, dd] = dia.split('-');
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  const dow = new Date(`${yyyy}-${mm}-${dd}T12:00:00-03:00`).getDay();
  return `${dd}/${mm} (${dias[dow]})`;
}
function getWeekdayLabel(dia) {
  const m = formatDay(dia).match(/\((\w+)\)/);
  return m ? m[1] : '';
}

// Os dias para o modo compacto (um resumo por dia):
const days = computed(() => Object.entries(groupedByDay.value).map(([date, arr]) => ({
  date,
  arr,
})));
</script>

<style scoped>
/* Seus estilos dos cards e botões aqui (opcional) */
</style>
