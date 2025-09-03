<template>
  <q-card
    class="q-pa-md"
    style="max-width:900px;margin:auto;"
    :class="windBgClass"
  >
    <div class="flex items-center justify-between">
      <div class="text-h6">OpenWeather – Dados Atuais (via backend)</div>
      <div>
        <q-btn flat dense icon="refresh" @click="fetchData" :loading="loading" />
        <q-btn flat dense icon="settings" @click="showConfig = true" class="q-ml-sm"/>
      </div>
    </div>
    <div v-if="loading" class="q-pa-lg flex flex-center">
      <q-spinner color="primary" size="2em" />
    </div>
    <div v-else-if="error" class="text-negative q-pa-md">
      {{ error }}
    </div>
    <div v-else-if="current">
      <div class="row q-col-gutter-md q-mb-md">
        <template v-for="col in visibleColumns" :key="col.name">
          <div>
            <div class="text-caption">{{ col.label }}</div>
            <div class="text-h6" :class="col.class || ''">{{ col.format(current) }}</div>
          </div>
        </template>
      </div>

      <!-- ROSA DOS VENTOS (usa as configs globais salvas no localStorage) -->
      <!-- <div v-if="showWindRose" class="q-mb-md flex flex-center">
        <WindRose
          :direction="current.wind_deg"
          :intensidade="msToKts(current.wind_speed)"
          :max="40"
          :unidade="'kts'"
          :size="settings.sizeVento"
          :lang="settings.siglaEN ? 'en' : 'pt'"
        />
      </div> -->

      <div class="text-caption q-mb-sm">
        Fonte: OpenWeather 3.0 (atualização: {{ timeBR(current.dt) }})
      </div>
      <q-btn flat dense color="primary" label="Ver todos os dados" @click="showAll = true" icon="list" />
    </div>
    <div v-else class="q-pa-md">
      <q-spinner color="primary" size="2em" />
    </div>

    <!-- Modal de configuração de campos -->
    <q-dialog v-model="showConfig">
      <q-card style="min-width:340px;">
        <q-card-section>
          <div class="text-h6">Campos exibidos</div>
        </q-card-section>
        <q-card-section>
          <q-option-group
            v-model="selectedCols"
            :options="columns.map(c => ({label: c.label, value: c.name}))"
            type="checkbox"
            color="primary"
            dense
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup/>
          <q-btn color="primary" label="OK" @click="saveConfig"/>
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Modal de visualização de TODOS os dados -->
    <q-dialog v-model="showAll" persistent>
      <q-card style="min-width:450px;max-width:90vw;">
        <q-card-section>
          <div class="text-h6">Todos os dados da API</div>
        </q-card-section>
        <q-card-section style="max-height:400px;overflow:auto;">
          <pre style="font-size:.95em;line-height:1.25em;white-space:pre-wrap;">{{ rawAll }}</pre>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="primary" label="Fechar" v-close-popup/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import WindRose from 'src/components/praticagem/watch/WindRose.vue' // Certifique-se do caminho!

const LAT = -32.188
const LON = -52.082

const loading = ref(false)
const error = ref('')
const current = ref(null)
const allData = ref({})
const showConfig = ref(false)
const showAll = ref(false)
const selectedCols = ref([])

const STORAGE_KEY = 'openweather_panel_cols'

// Config global do painel principal
const settings = ref({
  showVento: true,
  sizeVento: 90,
  siglaEN: false,
  // Você pode adicionar outros padrões
})

// Carrega as configs globais do painel principal
onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved) {
    selectedCols.value = JSON.parse(saved)
  } else {
    selectedCols.value = ['temp', 'feels_like', 'pressure', 'humidity', 'dew_point', 'wind_speed', 'wind_deg', 'wind_gust', 'uvi', 'clouds', 'visibility', 'rain_1h', 'weather', 'dt', 'sunrise', 'sunset']
  }
  // Carrega config global
  try {
    const data = JSON.parse(localStorage.getItem('weatherPanelConfig'))
    if (data) Object.assign(settings.value, data)
  } catch {}
  fetchData()
})

function saveConfig() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedCols.value))
  showConfig.value = false
}

const columns = [
  { name: 'temp', label: 'Temperatura', class: 'text-primary', format: c => (c.temp !== undefined ? `${c.temp}°C` : '--') },
  { name: 'feels_like', label: 'Sensação Térmica', class: '', format: c => (c.feels_like !== undefined ? `${c.feels_like}°C` : '--') },
  { name: 'pressure', label: 'Pressão', class: '', format: c => (c.pressure !== undefined ? `${c.pressure} hPa` : '--') },
  { name: 'humidity', label: 'Umidade', class: '', format: c => (c.humidity !== undefined ? `${c.humidity}%` : '--') },
  { name: 'dew_point', label: 'Ponto de Orvalho', class: '', format: c => (c.dew_point !== undefined ? `${c.dew_point}°C` : '--') },
  { name: 'wind_speed', label: 'Vento', class: '', format: c => (c.wind_speed !== undefined ? `${msToKts(c.wind_speed)} kts` : '--') },
  { name: 'wind_deg', label: 'Direção do Vento', class: '', format: c => (c.wind_deg !== undefined ? `${c.wind_deg}°` : '--') },
  { name: 'wind_gust', label: 'Rajada', class: '', format: c => (c.wind_gust !== undefined ? `${msToKts(c.wind_gust)} kts` : '--') },
  { name: 'uvi', label: 'Índice UV', class: '', format: c => (c.uvi !== undefined ? c.uvi : '--') },
  { name: 'clouds', label: 'Nuvens', class: '', format: c => (c.clouds !== undefined ? `${c.clouds}%` : '--') },
  { name: 'visibility', label: 'Visibilidade', class: '', format: c => (c.visibility !== undefined ? `${(c.visibility / 1000).toFixed(1)} km` : '--') },
  { name: 'rain_1h', label: 'Chuva (1h)', class: '', format: c => (c.rain && c.rain['1h'] !== undefined ? `${c.rain['1h']} mm` : '--') },
  { name: 'weather', label: 'Condição', class: '', format: c => c.weather?.[0]?.description || '--' },
  { name: 'dt', label: 'Data/Hora (BR)', class: '', format: c => timeBRshort(c.dt) },
  { name: 'sunrise', label: 'Nascer do Sol', class: '', format: c => timeBRshort(c.sunrise) },
  { name: 'sunset', label: 'Pôr do Sol', class: '', format: c => timeBRshort(c.sunset) },
]

// Mostra só as colunas selecionadas
const visibleColumns = computed(() => {
  return columns.filter(col => selectedCols.value.includes(col.name))
})

async function fetchData() {
  loading.value = true
  error.value = ''
  try {
    const url = `/kevi/backend/praticagem/openweather_proxy.php?lat=${LAT}&lon=${LON}`
    const resp = await fetch(url)
    if (!resp.ok) throw new Error('Erro ao buscar dados do backend')
    const json = await resp.json()
    if (!json.current) throw new Error('Dados ausentes ou formato inesperado')
    allData.value = json
    current.value = {
      ...json.current,
      rain: json.current.rain || {},
      weather: json.current.weather || [],
    }
  } catch (e) {
    error.value = e.message
    current.value = null
  } finally {
    loading.value = false
  }
}

// Helpers de formatação de data/hora para Brasília
function timeBR(epoch) {
  if (!epoch) return '--'
  return new Date(epoch * 1000).toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' })
}
function timeBRshort(epoch) {
  if (!epoch) return '--'
  const d = new Date(epoch * 1000)
  return `${d.toLocaleDateString('pt-BR', { timeZone: 'America/Sao_Paulo' })}, ${
    d.toLocaleTimeString('pt-BR', { hour12: false, timeZone: 'America/Sao_Paulo' })}`
}
function msToKts(ms) {
  if (ms == null) return 0
  return +(ms * 1.943844).toFixed(2)
}

// Exibe a rosa dos ventos só se for habilitada
const showWindRose = computed(() => !!settings.value.showVento && !!current.value && current.value.wind_deg !== undefined)

// Classe de cor de fundo baseada na força do vento (em kts)
const windBgClass = computed(() => {
  if (!current.value || current.value.wind_speed == null) return 'wind-bg-calm'
  const knots = msToKts(current.value.wind_speed)
  if (knots < 8) return 'wind-bg-calm'
  if (knots < 15) return 'wind-bg-moderate'
  if (knots < 25) return 'wind-bg-strong'
  return 'wind-bg-extreme'
})

// Para mostrar todos os dados "raw" na modal
const rawAll = computed(() => JSON.stringify(allData.value, null, 2))
</script>

<style scoped>
.wind-bg-calm    { background: #e3f2fd !important; }
.wind-bg-moderate{ background: #fff8e1 !important; }
.wind-bg-strong  { background: #ffe0b2 !important; }
.wind-bg-extreme { background: #ffebee !important; }
</style>
