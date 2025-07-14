<template>
  <q-page class="flex flex-center" style="min-height:100vh; background:#fff;">
    <div class="cond-root">
      <!-- 1. TÍTULO CENTRALIZADO -->
      <div class="cond-title-wrap">
        <span class="cond-title">Condições atuais:</span>
      </div>
      <div class="cond-content-row">
        <!-- 2. BLOCO TABELA -->
        <div class="cond-tabela-wrap">
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
        <!-- 3. BLOCO RELÓGIO -->
        <div class="cond-gauge-block">
          <div class="cond-gauge-outer">
            <GaugeRelogio
              :value="Number(meteo.direcao_3m) || 0"
              :intensidade="Number(meteo.intensidade_3m) || 0"
              :max="5"
              style="height:138px;width:138px;"
            />
          </div>
          <div class="cond-gauge-label">
            Corrente [3m]
            <!-- <span class="cond-gauge-knots">({{ meteo.intensidade_3m ?? '--' }} knots)</span> -->
          </div>
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
        sensacaotermica: row.sensacaotermica ?? row.sensacao_termica ?? row.sensacao,
        ventointensidade: row.ventointensidade,
        ventodirecao: row.ventodirecao ?? row.ventonum,
        pressao: row.pressao,
        umidade: row.umidade,
        leitura: row.timestamp_br?.date
          ? row.timestamp_br.date.replace(' 00:00:00.000000', '')
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
.cond-root {
  background: #fff;
  border-radius: 8px;
  min-width: 310px;
  max-width: 740px;
  box-shadow: 0 1px 8px 0 #e0e0e044;
  border: 1.5px solid #e0d4d6;
  padding-bottom: 10px;
  margin: 0 auto;
}
.cond-title-wrap {
  width: 100%;
  background: #e2b8c3;
  padding: 10px 0 8px 0;
  border-radius: 7px 7px 0 0;
  text-align: center;
  margin-bottom: 2px;
}
.cond-title {
  font-size: 1.25em;
  font-weight: 700;
  letter-spacing: .01em;
  color: #31161d;
  text-align: center;
}
.cond-content-row {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  gap: 22px;
  padding: 16px 18px 6px 18px;
  flex-wrap: wrap;
  justify-content: center;
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
  padding: 12px 19px 10px 19px;
  margin: 0 2px;
  min-width: 186px;
  width: 200px;
}
.cond-gauge-outer {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.cond-gauge-label {
  font-size: 1.10em;
  font-weight: 700;
  color: #555;
  margin-top: 7px;
  margin-right: 2px;
  letter-spacing: .02em;
  text-align: right;
  width: 100%;
}
.cond-gauge-knots {
  font-size: .98em;
  font-weight: 600;
  color: #444;
  margin-left: 8px;
  opacity: 0.9;
}
@media (max-width: 830px) {
  .cond-root { min-width: 99vw; max-width: 99vw; }
  .cond-content-row { flex-direction: column; gap: 8px; align-items: center; }
  .cond-tabela-wrap { width: 98vw; max-width: 500px; margin-bottom: 8px;}
  .cond-gauge-block { align-items: center; width: 100%; min-width: unset; margin: 0 auto;}
  .cond-gauge-label { text-align: center; margin-top: 8px;}
}
@media (max-width: 450px) {
  .cond-root { min-width: 100vw; max-width: 100vw; border: none; }
  .cond-tabela-wrap { max-width: 97vw; }
  .cond-gauge-block { padding: 8px 2vw 8px 2vw; }
}
</style>
