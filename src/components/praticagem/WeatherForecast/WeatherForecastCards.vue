<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
    <div
      v-for="item in rows"
      :key="item.dt_txt"
      class="weather-card"
      :class="rainCardColor(item)"
    >
      <div class="flex items-center gap-2">
        <img
          :src="`https://openweathermap.org/img/wn/${item.weather_icon}@2x.png`"
          :alt="item.weather_main"
          style="width: 42px; height: 42px"
        />
        <div>
          <div class="text-lg font-bold">{{ item.temp?.toFixed(0) }}°C</div>
          <div class="text-xs text-gray-600 capitalize">{{ item.weather_description }}</div>
        </div>
        <div class="ml-auto flex flex-col items-end text-right">
          <span class="flex items-center gap-1 text-sm">
            <q-icon name="access_time" size="16px" /> {{ item.dt_txt?.slice(11, 16) }}
            <!-- Botão para detalhes -->
            <q-btn
              size="xs"
              flat
              dense
              icon="format_quote"
              color="primary"
              @click="showDetails(item)"
              title="Ver todos os dados"
              class="ml-1"
            />
          </span>
          <span class="flex items-center gap-1 text-sm" v-if="typeof item.pop === 'number'">
            <q-icon name="water_drop" color="blue-5" size="16px"/>
            <span :class="item.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
              {{ Math.round(item.pop * 100) }}%
            </span>
            <span v-if="item.rain_3h" class="text-grey-7">({{ item.rain_3h }}mm)</span>
          </span>
        </div>
      </div>
      <div class="mt-2 flex flex-wrap gap-2 text-sm">
        <div class="chip">
          <WindMiniClock :deg="item.wind_deg" :speed="item.wind_speed" />
          <q-icon name="air" size="16px"/> {{ item.wind_speed ?? '--' }} km/h ({{ windDir(item.wind_deg) }})
        </div>
        <div class="chip">
          <q-icon name="water" size="16px"/> Umid: {{ item.humidity ?? '--' }}%
        </div>
        <div class="chip">
          <q-icon name="compress" size="16px"/> Pressão: {{ item.pressure ?? '--' }} hPa
        </div>
        <div class="chip" v-if="item.feels_like !== undefined">
          <q-icon name="thermostat" size="16px"/> Sens. {{ item.feels_like.toFixed(0) }}°
        </div>
        <div class="chip" v-if="item.clouds_all !== undefined">
          <q-icon name="filter_drama" size="16px"/> Nuvens: {{ item.clouds_all }}%
        </div>
      </div>
    </div>
    <!-- Modal Detalhes -->
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
import WindMiniClock from 'src/components/praticagem/WindMiniClock.vue';

// eslint-disable-next-line no-unused-vars
const props = defineProps({ rows: { type: Array, default: () => [] } });

function windDir(deg) {
  if (deg == null) return '';
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}
function rainCardColor(item) {
  if (!item.pop) return 'rain-none-card';
  if (item.pop >= 0.8) return 'rain-red-card';
  if (item.pop >= 0.5) return 'rain-yellow-card';
  if (item.pop >= 0.2) return 'rain-blue-card';
  return 'rain-none-card';
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
.weather-card {
  border-radius: 16px;
  min-height: 160px;
  background: white;
  transition: box-shadow .12s, background .25s;
  box-shadow: 0 3px 10px #9ec7ec11;
  border: 1.5px solid #e3eefc;
  position: relative;
  padding: 16px 14px 14px 16px;
  margin-bottom: 2px;
}
.weather-card:hover {
  box-shadow: 0 8px 24px #0e579e22;
  background: #eaf5fd;
}
.chip {
  background: #f2f8fe;
  border-radius: 8px;
  padding: 3px 10px 3px 7px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  color: #1867c0;
}
.rain-red-card { background: linear-gradient(90deg, #ffebee, #ffcccb 65%, #ff5252 100%) !important; }
.rain-yellow-card { background: linear-gradient(90deg, #fffde7, #fff9c4 65%, #ffe082 100%) !important; }
.rain-blue-card { background: linear-gradient(90deg, #e3f2fd, #bbdefb 60%, #4fc3f7 100%) !important; }
.rain-none-card { background: transparent !important; }
</style>
