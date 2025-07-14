<template>
  <q-page class="flex flex-center" style="min-height:100vh; background:#fff;">
    <div class="cond-card-root">
      <!-- Faixa rosa superior com título -->
      <div class="cond-header">
        <span class="cond-title">Condições atuais:</span>
      </div>
      <div class="cond-content-row">
        <!-- Esquerda: tabela de dados -->
        <div class="cond-tabela-wrap">
          <!-- Tarja preta -->
          <div class="cond-tarja">
            <img src="/icons/station.svg" height="22" style="margin-right:7px;" />
            ESTAÇÃO METEOROLÓGICA
          </div>
          <table class="cond-table">
            <tr>
              <th>TEMPERATURA</th>
              <th>SENSAÇÃO TÉRMICA</th>
            </tr>
            <tr>
              <td>{{ meteo.temperatura ?? '--' }}</td>
              <td>{{ meteo.sensacaotermica ?? '--' }}</td>
            </tr>
            <tr>
              <th>VENTO</th>
              <th>PRESSÃO</th>
            </tr>
            <tr>
              <td>{{ meteo.ventointensidade ?? '--' }} kt {{ meteo.ventodirecao ?? '--' }}</td>
              <td>{{ meteo.pressao ?? '--' }} mb</td>
            </tr>
            <tr>
              <th>UMIDADE</th>
              <th>LEITURA</th>
            </tr>
            <tr>
              <td>{{ meteo.umidade ?? '--' }}%</td>
              <td>{{ meteo.leitura ?? '--' }}</td>
            </tr>
          </table>
        </div>
        <!-- Centro: Gauge maior -->
        <div class="cond-gauge-block">
          <GaugeRelogio
            :value="Number(meteo.direcao_3m) || 0"
            :intensidade="Number(meteo.intensidade_3m) || 0"
            :max="5"
            style="height:142px;width:142px;"
          />
          <div class="cond-gauge-label">Corrente [3m]</div>
        </div>
      </div>
    </div>
    <q-inner-loading :showing="loading" color="pink-5" />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import GaugeRelogio from 'components/praticagem/GaugeRelogio.vue';

const $q = useQuasar();
const loading = ref(true);
const meteo = ref({});

// Pegue só o dado mais recente!
const ENDPOINT = '/kevi/backend/praticagem/get_table_mestre_hour_tratada_bq.php?limit=1';

onMounted(async () => {
  loading.value = true;
  try {
    const res = await fetch(ENDPOINT);
    const json = await res.json();
    if (json.success && json.data && json.data.length) {
      const row = json.data[0];
      meteo.value = {
        temperatura: row.temperatura,
        sensacaotermica: row.sensacaotermica ?? row.sensacao_termica ?? row.sensacao, // diferentes nomes possíveis
        ventointensidade: row.ventointensidade,
        ventodirecao: row.ventodirecao ?? row.ventonum,
        pressao: row.pressao,
        umidade: row.umidade,
        leitura: row.timestamp_br?.date
          ? row.timestamp_br.date.replace(' 00:00:00.000000', '') // limpa microsegundos
          : (row.leitura ?? '--'),
        direcao_3m: row.direcao_3m,
        intensidade_3m: row.intensidade_3m,
      };
    } else {
      $q.notify({ type: 'warning', message: 'Dados não disponíveis.' });
    }
  } catch (e) {
    $q.notify({ type: 'negative', message: 'Erro ao buscar condições atuais.' });
    // eslint-disable-next-line
    console.error(e)
  }
  loading.value = false;
});
</script>

<style scoped>
.cond-card-root {
  background: #fff;
  border-radius: 8px;
  min-width: 610px;
  max-width: 740px;
  box-shadow: 0 1px 8px 0 #e0e0e044;
  border: 1.5px solid #e0d4d6;
  padding-bottom: 10px;
}
.cond-header {
  width: 100%;
  background: #e2b8c3;
  padding: 8px 18px 4px 13px;
  border-radius: 6px 6px 0 0;
  font-size: 1.25em;
  font-weight: 600;
  letter-spacing: .01em;
  color: #31161d;
}
.cond-title {
  font-size: 1.22em;
}
.cond-content-row {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  gap: 16px;
  padding: 8px 12px 2px 13px;
}
.cond-tabela-wrap {
  width: 255px;
  flex-shrink: 0;
}
.cond-tarja {
  background: #111;
  color: #fff;
  font-size: 1em;
  font-weight: 600;
  padding: 2.5px 12px 2.5px 2px;
  border-radius: 3px;
  margin-bottom: 5px;
  display: flex;
  align-items: center;
  letter-spacing: .04em;
}
.cond-table {
  border-collapse: collapse;
  width: 100%;
  font-size: 1.02em;
  background: #fff;
}
.cond-table th, .cond-table td {
  border: 1px solid #c4c3c2;
  padding: 3.5px 9px;
  text-align: center;
  font-weight: 500;
}
.cond-table th {
  background: #f6f6f6;
  color: #222;
  font-size: .99em;
  font-weight: 700;
}
.cond-gauge-block {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: flex-start;
  background: #fafbfa;
  border-radius: 12px;
  box-shadow: 0 1px 5px 0 #bcd6e255;
  padding: 6px 15px 8px 15px;
  margin: 0 2px;
  min-width: 170px;
}
.cond-gauge-label {
  font-size: 1.08em;
  font-weight: 700;
  color: #555;
  margin-top: 5px;
  margin-right: 2px;
  letter-spacing: .03em;
  text-align: right;
}
@media (max-width: 830px) {
  .cond-card-root { min-width: 98vw; max-width: 99vw; }
  .cond-content-row { flex-direction: column; gap: 2px; }
  .cond-gauge-block { margin: 14px auto 5px; }
}
</style>
