<template>
  <div class="scroll-x-bar">
    <div
      class="hour-row"
      v-for="item in rows"
      :key="item.dt_txt"
      :class="rainBarColor(item)"
    >
      <div class="flex items-center gap-2 flex-1 px-2">
        <q-icon name="access_time" size="18px" />
        <!-- Ajuste para mostrar hora UTC-3 -->
        {{ formatHourBR(item.dt_txt) }}
        <WindMiniClock :deg="item.wind_deg" :speed="item.wind_speed" class="ml-2" />
      </div>
      <div class="flex-1 flex items-center gap-2">
        <img
          :src="`https://openweathermap.org/img/wn/${item.weather_icon}@2x.png`"
          :alt="item.weather_main"
          style="width: 32px; height: 32px"
        />
        <span class="text-lg font-bold">{{ item.temp?.toFixed(0) }}°C</span>
        <span class="text-sm text-grey-7 ml-2">{{ item.weather_description }}</span>
      </div>
      <div class="flex-1 flex gap-2 items-center justify-end">
        <q-icon name="water_drop" size="16px" />
        <span class="font-bold" :class="item.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
          {{ Math.round(item.pop * 100) }}%
        </span>
        <span v-if="item.rain_3h" class="text-grey-7 ml-1">({{ item.rain_3h }}mm)</span>
        <q-icon name="air" size="16px" class="ml-3" />
        <span>{{ item.wind_speed ?? '--' }} km/h</span>
        <!-- Botão para detalhes completos -->
        <q-btn
          dense
          flat
          icon="format_quote"
          color="primary"
          @click="showDetails(item)"
          title="Ver todos os dados"
          class="ml-2"
        />
      </div>
    </div>
    <!-- Modal de detalhes -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width:340px;max-width:99vw;">
        <q-card-section>
          <div class="text-base font-bold mb-2">Dados completos</div>
          <pre class="text-xs" style="max-width: 420px; max-height: 65vh; overflow: auto;">
{{ details }}
          </pre>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="primary" flat label="Fechar" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import WindMiniClock from 'src/components/praticagem/WeatherForecast/WindMiniSeta.vue';

// eslint-disable-next-line no-unused-vars
const props = defineProps({ rows: { type: Array, default: () => [] } });

// Função para mostrar a hora ajustada para UTC-3
// eslint-disable-next-line camelcase
function formatHourBR(dt_txt) {
  // eslint-disable-next-line camelcase
  if (!dt_txt) return '--';
  // eslint-disable-next-line camelcase
  const utcDate = new Date(`${dt_txt.replace(' ', 'T')}Z`);
  const brDate = new Date(utcDate.getTime() - 3 * 60 * 60 * 1000);
  return brDate.toISOString().slice(11, 16);
}

function rainBarColor(item) {
  if (!item.pop) return 'rain-none-bar';
  if (item.pop >= 0.8) return 'rain-red-bar';
  if (item.pop >= 0.5) return 'rain-yellow-bar';
  if (item.pop >= 0.2) return 'rain-blue-bar';
  return 'rain-none-bar';
}

// Modal detalhes
const dialog = ref(false);
const details = ref('');
function showDetails(item) {
  details.value = JSON.stringify(item, null, 2);
  dialog.value = true;
}
</script>

<style scoped>
.scroll-x-bar {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.hour-row {
  display: flex;
  align-items: center;
  min-height: 56px;
  border-radius: 12px;
  margin-bottom: 2px;
  padding: 5px 0 5px 12px;
  box-shadow: 0 1px 6px #72aeea11;
  font-size: 1.03em;
}
.hour-row > * {
  flex: 1;
}
.rain-red-bar { background: linear-gradient(90deg, #ffebee, #ffcccb 65%, #ff5252 100%) !important; }
.rain-yellow-bar { background: linear-gradient(90deg, #fffde7, #fff9c4 65%, #ffe082 100%) !important; }
.rain-blue-bar { background: linear-gradient(90deg, #e3f2fd, #bbdefb 60%, #4fc3f7 100%) !important; }
.rain-none-bar { background: transparent !important; }
</style>
