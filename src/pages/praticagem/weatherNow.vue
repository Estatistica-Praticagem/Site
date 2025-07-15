<template>
  <q-page class="q-pa-md bg-grey-2" style="max-width:1200px;margin:auto;">
    <!-- Cabeçalho -->
    <div class="text-h4 text-weight-bold text-primary q-mb-xs">RG PILOTS</div>
    <div class="text-h6 text-weight-bold q-mb-md" style="color:#0267C1">Condições atuais</div>

    <!-- Resumo principal -->
    <q-card class="q-pa-md q-mb-lg bg-white shadow-3" style="border-radius:18px">
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
          {name:'Temperatura',label:'Temperatura',field:'Temperatura',align:'center'},
          {name:'Sensação Térmica',label:'Sens. Térmica',field:'Sensação Térmica',align:'center'},
          {name:'Pressão',label:'Pressão',field:'Pressão',align:'center'},
          {name:'Umidade',label:'Umidade',field:'Umidade',align:'center'},
          {name:'Maré Real',label:'Maré Real',field:'Maré Real',align:'center'},
          {name:'Status',label:'Status',field:'Status',align:'center'},
        ]"
        hide-bottom
        :pagination="{rowsPerPage:1}"
        class="bg-white no-shadow"
        style="border-radius: 12px"
      />
      <div class="row q-mt-sm items-center justify-center">
        <div class="col-auto text-center">
          <div class="text-caption text-primary" style="font-size:.98em">Vento Médio</div>
          <div class="text-h5 text-weight-bold" style="color:#0097A7">
            {{ row.ventointensidade ?? '--' }} m/s
            <span class="q-ml-xs" style="font-size:0.8em;color:#888;">({{ row.ventodirecao ?? '--' }})</span>
          </div>
        </div>
      </div>
    </q-card>

    <!-- ADCP níveis resumidos -->
    <div class="text-h6 text-weight-medium q-mb-xs" style="color:#0267C1">ADCP - Correntezas (m/s)</div>
    <div class="row relogio-grid">
      <div
        v-for="info in correnteCampos"
        :key="info.label"
        class="g-rel-col"
      >
        <q-card flat bordered class="relogio-card">
          <div class="text-bold text-primary q-mb-xs relogio-label">{{ info.label }}</div>
          <GaugeRelogio
            :value="parseFloat(row[info.dir] ?? 0)"
            :intensidade="parseFloat(row[info.int] ?? 0)"
            :max="2"
            :unidade="'m/s'"
            colorMain="#1976D2"
            colorSecondary="#43A047"
            colorBg="#E3F2FD"
            size="100"
          />
        </q-card>
      </div>
    </div>

    <!-- Info detalhada - status -->
    <q-card class="q-pa-md q-mt-lg bg-white shadow-2" style="border-radius:16px">
      <div class="text-h6 text-weight-bold text-primary">Situação</div>
      <div class="text-body1 q-mb-xs">
        <b>{{ row.status ?? '--' }}</b>
        <span v-if="row.motivo" class="q-ml-sm text-negative">
          ({{ row.motivo }})
        </span>
      </div>
      <div class="text-caption text-grey-8">Leitura: {{ row.timestamp_br?.date ?? '--' }}</div>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import GaugeRelogio from 'components/praticagem/GaugeRelogio.vue';

const row = ref({});
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

<style scoped>
/* Grid compacto, máximo de 5 por linha em telas grandes */
.relogio-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem 0.3rem;
  justify-content: flex-start;
  align-items: stretch;
  margin-bottom: 20px;
  max-width: 1100px;
}
.g-rel-col {
  flex: 1 1 120px;
  min-width: 110px;
  max-width: 155px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 2px;
  padding: 0 2px;
}
.relogio-card {
  border-radius: 13px;
  box-shadow: 0 1px 6px #e0e0e033;
  min-height: 164px;
  max-width: 145px;
  width: 100%;
  padding: 6px 2px 2px 2px;
  margin: 0;
}
.relogio-label {
  font-size: 1.08em;
  margin-top: 2px;
  margin-bottom: 0;
  color: #1565c0;
}
@media (max-width: 1100px) {
  .q-page {
    padding-left: 0.3em !important;
    padding-right: 0.3em !important;
  }
  .relogio-grid {
    max-width: 100vw;
  }
}
@media (max-width: 850px) {
  .relogio-grid { gap: 0.3rem 0.2rem; }
  .g-rel-col { min-width: 94px; max-width: 120px; }
  .relogio-card { min-height: 120px; max-width: 120px; }
}
@media (max-width: 700px) {
  .g-rel-col { min-width: 88px; max-width: 110px; }
  .relogio-card { min-height: 100px; max-width: 108px; }
}
</style>
