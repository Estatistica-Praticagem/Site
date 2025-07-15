<template>
  <q-page class="q-pa-md bg-grey-1">

    <!-- Cabeçalho -->
    <div class="text-h4 text-weight-bold q-mb-sm">RG PILOTS</div>
    <div class="text-h6 text-primary q-mb-md">Condições atuais:</div>

    <!-- Resumo principal -->
    <q-card class="q-pa-md q-mb-lg">
      <q-table
        dense flat
        :rows="[{
          Temperatura: `${row.temperatura ?? '--'}°C`,
          'Sensação Térmica': `${row.sensacaotermica ?? '--'}°C`,
          'Pressão': `${row.pressao ?? '--'} hPa`,
          'Umidade': `${row.umidade ?? '--'}%`,
          'Maré Real': `${row.altura_real_getmare ?? '--'} m`,
          'Status': row.status ?? '--'
        }]"
        :columns="[
          {name:'Temperatura',label:'Temp.',field:'Temperatura',align:'center'},
          {name:'Sensação Térmica',label:'Sensação',field:'Sensação Térmica',align:'center'},
          {name:'Pressão',label:'Pressão',field:'Pressão',align:'center'},
          {name:'Umidade',label:'Umidade',field:'Umidade',align:'center'},
          {name:'Maré Real',label:'Maré',field:'Maré Real',align:'center'},
          {name:'Status',label:'Status',field:'Status',align:'center'},
        ]"
        hide-bottom
        :pagination="{rowsPerPage:1}"
        class="bg-white"
      />
      <div class="row q-mt-md">
        <div class="col text-center">
          <div class="text-caption">Vento Médio</div>
          <div class="text-h6">{{ row.ventointensidade ?? '--' }} m/s ({{ row.ventodirecao ?? '--' }})</div>
        </div>
      </div>
    </q-card>

    <!-- ADCP níveis resumidos -->
    <div class="q-mb-md">
      <div class="text-h6 text-weight-medium q-mb-xs">ADCP (Correntezas - Vazante/Ajustada)</div>
      <div class="row q-col-gutter-md">
        <div
          v-for="info in correnteCampos"
          :key="info.label"
          class="col-6 col-sm-4 col-md-2"
        >
          <q-card flat class="text-center bg-white q-pa-sm">
            <div class="text-bold">{{ info.label }}</div>
            <GaugeRelogio
              :value="parseFloat(row[info.dir] ?? 0)"
              :intensidade="parseFloat(row[info.int] ?? 0)"
              :max="2"
              :unidade="'m/s'"
            />
            <div class="text-caption q-mt-xs">
              Dir: {{ parseFloat(row[info.dir] ?? 0).toFixed(1) }}°<br>
              Int: {{ parseFloat(row[info.int] ?? 0).toFixed(2) }} m/s
            </div>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Info detalhada - status -->
    <q-card class="q-pa-md q-mt-lg">
      <div class="text-h6 text-weight-medium">Situação:</div>
      <div class="text-body1">
        <b>{{ row.status ?? '--' }}</b>
        <span v-if="row.motivo" class="q-ml-sm text-negative">
          ({{ row.motivo }})
        </span>
      </div>
      <div class="text-caption text-grey-7">Leitura: {{ row.timestamp_br?.date ?? '--' }}</div>
    </q-card>

  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
// import GaugeRelogio from 'components/GaugeRelogio.vue' // Use seu gauge aqui!

const row = ref({}); // Receberá os dados únicos
const correnteCampos = [
  { label: '15m', int: 'intensidade_15m', dir: 'direcao_15m' },
  { label: '13.5m', int: 'intensidade_13_5m', dir: 'direcao_13_5m' },
  { label: '12m', int: 'intensidade_12m', dir: 'direcao_12m' },
  { label: '10.5m', int: 'intensidade_10_5m', dir: 'direcao_10_5m' },
  { label: '9m', int: 'intensidade_9m', dir: 'direcao_9m' },
  { label: '7.5m', int: 'intensidade_7_5m', dir: 'direcao_7_5m' },
  { label: '6m', int: 'intensidade_6m', dir: 'direcao_6m' },
  { label: '3m', int: 'intensidade_3m', dir: 'direcao_3m' },
  { label: '1.5m', int: 'intensidade_1_5m', dir: 'direcao_1_5m' },
  { label: 'Sup.', int: 'intensidade_superficie', dir: 'direcao_superficie' },
];

onMounted(async () => {
  try {
    const resp = await fetch('/kevi/backend/get_table_mestre_hour_tratada_bq.php?limit=1');
    const json = await resp.json();
    row.value = json.data[0] || {};
  } catch (e) {
    row.value = {};
  }
});
</script>
