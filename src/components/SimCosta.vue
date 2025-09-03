<template>
  <q-card class="q-pa-md" style="max-width:900px;margin:auto;">
    <div class="flex items-center justify-between">
      <div class="text-h6">SiMCosta Rio Grande 1 - Nível do Mar</div>
      <q-btn flat dense icon="more_horiz" @click="showConfig = true" />
    </div>

    <q-table
      flat
      dense
      :rows="filteredRows"
      :columns="visibleColumns"
      row-key="id"
      class="q-mt-md"
      style="font-size:1em"
      :pagination="pagination"
    >
      <template v-slot:top>
        <span class="text-caption text-grey-7">Fonte: SiMCosta (boia 301) — sempre mostrando o dado mais recente</span>
      </template>
    </q-table>

    <!-- Painel de configuração -->
    <q-dialog v-model="showConfig">
      <q-card style="min-width:330px;">
        <q-card-section>
          <div class="text-h6">Configurar colunas visíveis</div>
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
          <q-btn flat label="Cancelar" color="grey-8" v-close-popup/>
          <q-btn label="OK" color="primary" @click="saveConfig" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

// Recebe do pai: true quando o painel SiMCosta está visível/selecionado
const props = defineProps({
  active: { type: Boolean, default: false }
})

// Colunas conhecidas dessa API
const columns = [
  { name: 'date', label: 'Data/Hora', field: row => row.date, align: 'left' },
  { name: 'water_l1', label: 'Nível instantâneo (cm)', field: 'water_l1', align: 'right' },
  { name: 'avg_water_l1', label: 'Nível médio (cm)', field: 'avg_water_l1', align: 'right' },
]

const rows = ref([])
const showConfig = ref(false)
const selectedCols = ref([])
const pagination = ref({ rowsPerPage: 1 }) // mostra só o último registro

function getSimCostaUrl() {
  const now = Math.floor(Date.now() / 1000)
  const sevenDaysAgo = now - (7 * 24 * 60 * 60)
  return `https://simcosta.furg.br/api/intrans_data?boiaID=301&type=csv&time1=${sevenDaysAgo}&time2=${now}&params=water_l1,avg_water_l1`
}

// Busca e parse dos dados da API
async function fetchData() {
  try {
    const resp = await fetch(getSimCostaUrl())
    const text = await resp.text()
    const lines = text.trim().split('\n')
    if (!lines[1]) {
      rows.value = [] // Sem dados
      return
    }
    rows.value = lines.slice(1).map((line, idx) => {
      const values = line.split(',')
      const dateStr = `${values[0]}-${values[1].padStart(2, '0')}-${values[2].padStart(2, '0')} ${values[3].padStart(2, '0')}:${values[4].padStart(2, '0')}:${values[5].padStart(2, '0')}`
      return {
        id: idx,
        date: dateStr,
        water_l1: values[6],
        avg_water_l1: values[7] !== undefined && values[7] !== 'NULL' ? values[7] : '-',
      }
    }).reverse() // Ordena do mais recente pro mais antigo
  } catch (e) {
    rows.value = []
  }
}

// Busca config no localStorage e busca os dados se estiver ativo ao montar
onMounted(() => {
  const saved = localStorage.getItem('simcosta_panel_cols')
  if (saved) {
    selectedCols.value = JSON.parse(saved)
  } else {
    selectedCols.value = columns.map(c => c.name)
  }
  if (props.active) fetchData()
})

// Sempre que "active" vira true, busca de novo!
watch(() => props.active, (val) => {
  if (val) fetchData()
})

// Colunas visíveis
const visibleColumns = computed(() => {
  return columns.filter(col => selectedCols.value.includes(col.name))
})

// Salva a configuração escolhida pelo usuário
function saveConfig() {
  localStorage.setItem('simcosta_panel_cols', JSON.stringify(selectedCols.value))
  showConfig.value = false
}

// Mostra só o registro mais atual
const filteredRows = computed(() => {
  return rows.value.length ? [rows.value[0]] : []
})
</script>
