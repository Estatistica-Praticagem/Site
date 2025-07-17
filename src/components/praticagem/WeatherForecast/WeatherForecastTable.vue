<template>
  <q-table
    dense
    flat
    :rows="rows"
    :columns="columns"
    row-key="dt_txt"
    hide-bottom
    no-data-label="Nenhum dado disponível"
    class="bg-white/90 rounded-xl"
    style="font-size: 0.98em;"
  >
    <template #body="props">
      <q-tr :props="props" :class="rainRowColor(props.row)">
        <q-td key="hour" class="flex items-center gap-2">
          <q-icon name="access_time" color="grey-7" size="18px"/>
          {{ props.row.dt_txt?.slice(11, 16) }}
        </q-td>
        <q-td key="icon">
          <img
            :src="`https://openweathermap.org/img/wn/${props.row.weather_icon}@2x.png`"
            :alt="props.row.weather_main"
            style="width: 32px; height: 32px"
          />
        </q-td>
        <q-td key="prob">
          <span v-if="typeof props.row.pop === 'number'">
            <q-icon name="water_drop" color="blue-6" size="18px"/>
            <span :class="props.row.pop >= 0.7 ? 'text-red' : 'text-blue-8'">
              {{ Math.round(props.row.pop * 100) }}%
            </span>
            <span v-if="props.row.rain_3h" class="text-grey-7">({{ props.row.rain_3h }}mm)</span>
          </span>
          <span v-else>--</span>
        </q-td>
        <q-td key="temp">
          <span class="text-lg font-bold">{{ props.row.temp?.toFixed(0) }}°</span>
          <span class="text-grey-6 text-xs block">Sens. {{ props.row.feels_like?.toFixed(0) }}°</span>
        </q-td>
        <q-td key="desc">
          <span style="text-transform: capitalize">{{ props.row.weather_description }}</span>
        </q-td>
        <q-td key="vento">
          <span class="flex items-center gap-2">
            <WindMiniClock :deg="props.row.wind_deg" :speed="props.row.wind_speed" />
            {{ windDir(props.row.wind_deg) }}
            <span class="text-blue-8 text-weight-medium">{{ props.row.wind_speed }} km/h</span>
          </span>
        </q-td>
        <q-td key="fps">
          <span :class="props.row.cod > 2 ? 'text-red-6' : 'text-blue-8'">
            <q-icon name="wb_sunny" size="18px" />
            {{ getFPS(props.row.cod) }}
          </span>
        </q-td>
        <q-td key="more">
          <q-btn dense flat icon="format_quote" color="primary" @click="showDetails(props.row)" />
        </q-td>
      </q-tr>
    </template>
  </q-table>

  <!-- MODAL DE DETALHES -->
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
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import WindMiniClock from 'src/components/praticagem/WeatherForecast/WindMiniClock.vue';

// Props: recebe os dados já filtrados do dia escolhido
// eslint-disable-next-line no-unused-vars
const props = defineProps({ rows: { type: Array, default: () => [] } });

// Debug: Loga sempre que rows mudar!
watchEffect(() => {
  // Mostra quantos dados chegaram, lista os horários disponíveis:
  // (você pode remover depois do debug)
  console.log('[WeatherForecastTable] rows length:', props.rows.length);
  console.log('[WeatherForecastTable] horários:', props.rows.map((x) => x.dt_txt));
});

const columns = [
  {
    name: 'hour', label: 'Hora', field: 'dt_txt', align: 'center', sortable: false,
  },
  {
    name: 'icon', label: '', field: 'weather_icon', align: 'center', sortable: false,
  },
  {
    name: 'prob', label: 'Chuva', field: 'pop', align: 'center', sortable: false,
  },
  {
    name: 'temp', label: 'Temp.', field: 'temp', align: 'center', sortable: false,
  },
  {
    name: 'desc', label: 'Descrição', field: 'weather_description', align: 'left', sortable: false,
  },
  {
    name: 'vento', label: 'Vento', field: 'wind_speed', align: 'center', sortable: false,
  },
  {
    name: 'fps', label: 'Sol/FPS', field: 'cod', align: 'center', sortable: false,
  },
  {
    name: 'more', label: '', field: '', align: 'center', sortable: false,
  },
];

function windDir(deg) {
  if (deg == null) return '';
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
  return dirs[Math.round((deg % 360) / 45)];
}
function getFPS(cod) {
  if (cod > 1) return '2 Baixo';
  return '1 Baixo';
}
function rainRowColor(row) {
  if (!row.pop) return '';
  if (row.pop >= 0.8) return 'rain-red';
  if (row.pop >= 0.5) return 'rain-yellow';
  if (row.pop >= 0.2) return 'rain-blue';
  return 'rain-none';
}

// Modal de detalhes
const dialog = ref(false);
const details = ref('');
function showDetails(row) {
  details.value = JSON.stringify(row, null, 2);
  dialog.value = true;
}
</script>

<style scoped>
.rain-red { background: linear-gradient(90deg, #ffebee, #ffcccb 65%, #ff5252 100%) !important; }
.rain-yellow { background: linear-gradient(90deg, #fffde7, #fff9c4 65%, #ffe082 100%) !important; }
.rain-blue { background: linear-gradient(90deg, #e3f2fd, #bbdefb 60%, #4fc3f7 100%) !important; }
.rain-none { background: transparent !important; }
</style>
