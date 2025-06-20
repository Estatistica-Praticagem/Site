<template>
  <section class="section-contacts q-pa-lg">
    <div class="text-center q-mb-lg">
      <h2 class="text-h5 text-primary text-weight-bold">Formulários Enviados</h2>
    </div>

    <div v-if="loading" class="text-center">
      <q-spinner-dots color="primary" size="40px" />
    </div>

    <q-banner
      v-if="erro"
      class="bg-red text-white q-mb-md"
      dense
      rounded
    >
      {{ erro }}
    </q-banner>

    <div v-if="contatos.length > 0" class="cards-wrapper">
      <q-card
        v-for="contato in contatos"
        :key="contato.id"
        class="q-mb-md shadow-3 card-contact"
        bordered
      >
        <q-card-section>
          <div class="row items-center q-gutter-sm">
            <q-avatar color="primary" text-color="white" icon="person" />
            <div>
              <div class="text-subtitle1 text-weight-bold">{{ contato.name }}</div>
              <div class="text-caption">{{ contato.email }}</div>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-section>
          <div><q-icon name="phone" size="16px" class="q-mr-sm" /> +{{ contato.country_code }} {{ contato.phone }}</div>
          <div><q-icon name="work_outline" size="16px" class="q-mr-sm" /> {{ contato.service }}</div>
          <div><q-icon name="message" size="16px" class="q-mr-sm" /> {{ contato.message }}</div>
          <div class="text-right text-caption text-grey">
            <q-icon name="event" size="16px" class="q-mr-xs" /> {{ formatarData(contato.created_at) }}
          </div>
        </q-card-section>
      </q-card>
    </div>

    <div v-else-if="!loading" class="text-center q-mt-xl">
      <q-icon name="info" size="32px" class="q-mb-sm" />
      <div>Nenhum contato encontrado.</div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const contatos = ref([]);
const erro = ref('');
const loading = ref(false);

function formatarData(dataStr) {
  const d = new Date(dataStr);
  return d.toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

async function carregarContatos() {
  const userId = localStorage.getItem('user_id');
  if (!userId) {
    erro.value = 'Usuário não autenticado.';
    return;
  }

  loading.value = true;
  try {
    const response = await fetch('https://www.meusimulador.com/kevi/backend/list_contacts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId }),
    });

    const data = await response.json();

    if (data.success) {
      contatos.value = data.data;
    } else {
      erro.value = data.error || 'Erro ao carregar contatos.';
    }
  } catch (err) {
    erro.value = 'Erro de conexão com o servidor.';
    console.error(err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  carregarContatos();
});
</script>

<style scoped>
.section-contacts {
  max-width: 900px;
  margin: auto;
  background: #fafafa;
  border-radius: 16px;
}

.cards-wrapper {
  display: flex;
  flex-direction: column;
}

.card-contact {
  border-radius: 16px;
  background: white;
}
</style>
