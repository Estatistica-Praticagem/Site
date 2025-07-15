<template>
  <q-page class="flex flex-center" style="min-height: 100vh;">
    <q-card
      class="row no-wrap q-pa-none shadow-2"
      style="background:#f8e3e7;min-height:150px;max-width:750px;width:100%;"
      v-if="!!meteo"
    >
      <!-- Coluna de dados -->
      <div class="col q-pa-md">
        <div class="text-h6 q-mb-xs">
          <q-icon name="cloud" class="q-mr-xs" />Condições atuais:
        </div>
        <div class="text-caption text-grey-7 q-mb-xs">ESTAÇÃO METEOROLÓGICA</div>
        <div class="row q-gutter-md">
          <div>
            <div class="text-caption">TEMPERATURA</div>
            <div class="text-bold">{{ meteo.temperatura ?? '--' }}°</div>
          </div>
          <div>
            <div class="text-caption">SENSAÇÃO TÉRMICA</div>
            <div class="text-bold">{{ meteo.sensacao ?? '--' }}°</div>
          </div>
          <div>
            <div class="text-caption">VENTO</div>
            <div class="text-bold">
              {{ meteo.vento_int ?? '--' }} kt {{ meteo.vento_dir ?? '--' }}
            </div>
          </div>
          <div>
            <div class="text-caption">PRESSÃO</div>
            <div class="text-bold">{{ meteo.pressao ?? '--' }} mb</div>
          </div>
        </div>
        <div class="row q-mt-sm">
          <div class="q-mr-md">
            <div class="text-caption">UMIDADE</div>
            <div class="text-bold">{{ meteo.umidade ?? '--' }}%</div>
          </div>
          <div>
            <div class="text-caption">LEITURA</div>
            <div class="text-bold">{{ meteo.leitura ?? '--' }}</div>
          </div>
        </div>
      </div>
      <!-- Corrente 3m Gauge -->
      <div class="col-auto flex flex-center bg-white q-px-lg" style="min-width:220px;">
        <div class="text-subtitle2 text-grey-7">Corrente [3m]</div>
        <div>
          <GaugeRelogio
            :value="Number(meteo.corrente_dir) || 0"
            :intensidade="Number(meteo.corrente_int) || 0"
            :max="5"
            style="height:110px; width:110px;"
          />
          <div class="row justify-between items-center q-mt-sm" style="font-size:1.1em;">
            <div>
              <div class="text-caption">Dir09 (deg)</div>
              <div>{{ meteo.corrente_dir ?? '--' }}</div>
            </div>
            <div class="q-ml-lg">
              <div class="text-caption">Int09 (knots)</div>
              <div>{{ meteo.corrente_int ?? '--' }}</div>
            </div>
          </div>
        </div>
      </div>
    </q-card>
    <q-inner-loading :showing="loading" color="pink-5" />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import GaugeRelogio from 'components/praticagem/GaugeRelogio.vue';

const $q = useQuasar();
const loading = ref(true);
const meteo = ref(null);

// AJUSTE para seu endpoint real! Pegando só o registro mais recente (limit=1).
const ENDPOINT = '/kevi/backend/praticagem/get_table_mestre__tratada_bq.php?limit=1';

onMounted(async () => {
  loading.value = true;
  try {
    const res = await fetch(ENDPOINT);
    const json = await res.json();
    if (json.success && json.data && json.data.length) {
      const row = json.data[0];
      // Ajuste os campos de acordo com o que o seu backend retorna!
      meteo.value = {
        temperatura: row.temperatura,
        sensacao: row.sensacao_termica ?? row.sensacao, // tente as duas
        vento_int: row.ventointensidade ?? row.vento_int,
        vento_dir: row.ventodir ?? row.ventonum ?? row.vento_dir,
        pressao: row.pressao,
        umidade: row.umidade,
        leitura: row.timestamp_br
          ? new Date(row.timestamp_br).toLocaleString('pt-BR')
          : (row.leitura ?? '--'),
        corrente_dir: row.direcao_3m ?? row.corrente_dir,
        corrente_int: row.intensidade_3m ?? row.corrente_int,
      };
    } else {
      meteo.value = null;
      $q.notify({ type: 'warning', message: 'Dados não disponíveis.' });
    }
  } catch (e) {
    $q.notify({ type: 'negative', message: 'Erro ao buscar condições atuais.' });
    meteo.value = null;
    // eslint-disable-next-line
    console.error(e)
  }
  loading.value = false;
});
</script>

<style scoped>
.text-bold { font-weight: bold; }
</style>
