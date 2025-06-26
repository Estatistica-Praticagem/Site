<template>
  <section class="section-contacts q-pa-lg">
    <!-- Título -->
    <div class="text-center q-mb-lg">
      <h2 class="text-h5 text-primary text-weight-bold">Formulários Enviados</h2>
    </div>

    <!-- Barra de filtro -->
    <div class="row items-center q-col-gutter-md q-mb-md gt-sm">
      <q-input
        outlined dense debounce="300"
        v-model="searchEmail"
        placeholder="Buscar por e-mail…"
        clearable
        class="col"
      >
        <template #prepend><q-icon name="search" /></template>
      </q-input>

      <q-select
        outlined dense
        v-model="statusFilter"
        :options="statusOptionsLabel"
        label="Status"
        clearable
        class="col-3"
      />

      <q-btn-toggle
        v-model="orderNewest"
        spread dense rounded
        class="bg-grey-2"
        :options="[
          {value:true,  slot:'novo'},
          {value:false, slot:'antigo'}
        ]"
      >
        <template #novo><q-icon name="arrow_downward" /> + Novos</template>
        <template #antigo><q-icon name="arrow_upward" /> + Antigos</template>
      </q-btn-toggle>
    </div>

    <!-- Loading/Erro -->
    <div v-if="loading" class="text-center">
      <q-spinner-dots color="primary" size="40px" />
    </div>
    <q-banner v-if="erro" class="bg-red text-white q-mb-md" dense rounded>
      {{ erro }}
    </q-banner>

    <!-- Cards de contatos filtrados -->
    <div v-if="filteredContatos.length" class="cards-wrapper">
      <q-card
        v-for="contato in filteredContatos"
        :key="contato.id"
        class="q-mb-md shadow-3 card-contact"
        bordered
      >
        <!-- Dot de status -->
        <div class="status-dot-large" :style="{ background: statusColor(contato.status) }" />

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

        <!-- Ações -->
        <q-separator />
        <q-card-actions align="between" class="actions-bottom">
          <q-btn dense round flat icon="chat_bubble" color="teal" @click="abrirComentarios(contato)" />
          <div>
            <q-btn dense round flat icon="edit" color="primary" @click="abrirMenuStatus(contato)" />
            <q-btn dense round flat icon="delete" color="red" @click="excluirContato(contato.id)" />
          </div>
        </q-card-actions>

        <!-- Comentários -->
        <q-slide-transition>
          <div v-show="contato._mostrarComentarios" class="q-pa-md bg-grey-2">
            <div class="row items-center q-mb-sm">
              <span class="text-caption text-weight-bold">Comentários</span>
              <q-space />
              <q-btn size="sm" flat icon="close" @click="contato._mostrarComentarios = false" />
            </div>
            <div v-if="comentarios[contato.id]?.length" class="q-mb-sm">
              <div v-for="coment in comentarios[contato.id]" :key="coment.id" class="comment-box q-mb-xs">
                <div class="row items-center">
                  <q-avatar size="26px" color="grey-3" text-color="primary" class="q-mr-xs">
                    <template v-if="coment.image_url"><img :src="coment.image_url" /></template>
                    <template v-else>{{ userInitial(coment) }}</template>
                  </q-avatar>
                  <span class="text-caption text-weight-bold q-mr-sm">{{ formatarData(coment.created_at) }}</span>
                  <q-space />
                  <q-btn size="xs" dense flat icon="edit" color="primary" @click="editarComentario(contato.id, coment)" />
                </div>
                <div>{{ coment.comment }}</div>
              </div>
            </div>
            <div v-else class="text-caption text-grey">Nenhum comentário ainda.</div>
            <div class="row items-center q-mt-sm">
              <q-input
                dense outlined v-model="novoComentario[contato.id]" placeholder="Adicionar…"
                class="col"
              />
              <q-btn size="sm" color="teal" icon="send" @click="adicionarComentario(contato.id)" />
            </div>
          </div>
        </q-slide-transition>
      </q-card>
    </div>

    <div v-else-if="!loading" class="text-center q-mt-xl">
      <q-icon name="info" size="32px" class="q-mb-sm" />
      <div>Nenhum contato encontrado.</div>
    </div>

    <!-- Dialog para editar status -->
    <q-dialog v-model="menuStatus.visivel">
      <q-card>
        <q-card-section>
          <div class="text-h6">Alterar Status</div>
        </q-card-section>
        <q-separator />
        <q-list>
          <q-item
            v-for="op in statusOptions"
            :key="op.value"
            clickable
            @click="atualizarStatus(menuStatus.contato, op.value)"
          >
            <q-item-section avatar>
              <div :style="{ width: '22px', height: '22px', borderRadius: '50%', background: op.color }"></div>
            </q-item-section>
            <q-item-section>{{ op.label }}</q-item-section>
          </q-item>
        </q-list>
      </q-card>
    </q-dialog>

    <!-- Modal editar comentário -->
    <q-dialog v-model="editarModal">
      <q-card style="min-width:340px;max-width:95vw">
        <q-card-section>
          <div class="text-h6">Editar Comentário</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-input
            v-model="comentarioEditandoTexto"
            type="textarea"
            autogrow
            label="Comentário"
            dense
            autofocus
          />
        </q-card-section>
        <q-separator />
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn flat label="Salvar" color="primary" @click="salvarEdicaoComentario" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Notify } from 'quasar';

/* --- State --- */
const contatos = ref([]);
const erro = ref('');
const loading = ref(false);
const searchEmail = ref('');
const statusFilter = ref(null);
const orderNewest = ref(true);

/* --- Comentários --- */
const comentarios = ref({});
const novoComentario = ref({});
const editarModal = ref(false);
const comentarioEditando = ref(null);
const comentarioEditandoTexto = ref('');
const contatoEditandoComentario = ref(null);

/* --- Status dialog --- */
const menuStatus = ref({ visivel: false, contato: null });

/* --- Usuário logado --- */
const usuarioLogado = JSON.parse(localStorage.getItem('usuarioLogado') || '{}');

/* --- Status --- */
const statusOptions = [
  { value: 'Esperando Contato', label: 'Esperando Contato', color: '#757575' },
  { value: 'Contatado', label: 'Contatado', color: '#1976D2' },
  { value: 'Aguardando Resposta', label: 'Aguardando Resposta', color: '#FFB300' },
  { value: 'Em Negociação', label: 'Em Negociação', color: '#FF7043' },
  { value: 'Cliente Ativo', label: 'Cliente Ativo', color: '#43A047' },
];
const statusOptionsLabel = ['Todos', ...statusOptions.map((s) => s.label)];

/* --- Computed: filtro/sort --- */
const filteredContatos = computed(() => {
  let arr = [...contatos.value];
  if (searchEmail.value) {
    const t = searchEmail.value.toLowerCase();
    arr = arr.filter((c) => c.email.toLowerCase().includes(t));
  }
  if (statusFilter.value && statusFilter.value !== 'Todos') {
    arr = arr.filter((c) => c.status === statusFilter.value);
  }
  arr.sort((a, b) => {
    const da = new Date(a.created_at).getTime();
    const db = new Date(b.created_at).getTime();
    return orderNewest.value ? db - da : da - db;
  });
  return arr;
});

/* --- Funções utilitárias --- */
function statusColor(status) {
  const found = statusOptions.find((op) => op.value === status);
  return found ? found.color : '#757575';
}
function abrirMenuStatus(contato) {
  menuStatus.value = { visivel: true, contato };
}

function formatarData(dataStr) {
  const d = new Date(dataStr);
  return d.toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit',
  });
}
function userInitial(coment) {
  if (coment.user_name) return coment.user_name.charAt(0).toUpperCase();
  return 'U';
}

/* --- API URL --- */
const BASE = 'https://www.meusimulador.com/kevi/backend';

/* --- CRUD principal --- */
async function carregarContatos() {
  const userId = localStorage.getItem('user_id');
  if (!userId) { erro.value = 'Usuário não autenticado.'; return; }
  loading.value = true;
  try {
    const r = await fetch(`${BASE}/list_contacts.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId }),
    });
    const d = await r.json();
    if (d.success) {
      contatos.value = d.data.map((c) => ({ ...c, _mostrarComentarios: false }));
    } else erro.value = d.error || 'Erro ao carregar.';
  } catch (e) { erro.value = 'Falha de conexão.'; } finally { loading.value = false; }
}

async function excluirContato(id) {
  // eslint-disable-next-line no-restricted-globals
  if (!confirm('Tem certeza?')) return;
  const userId = localStorage.getItem('user_id');
  try {
    const r = await fetch(`${BASE}/delete_contact.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ contact_id: id, user_id: userId }),
    });
    const d = await r.json();
    Notify.create({ message: d.message || 'Removido', color: r.ok ? 'green' : 'red' });
    if (r.ok) await carregarContatos();
  } catch (e) { Notify.create({ message: 'Conexão falhou', color: 'red' }); }
}

/* --- Alterar status e LOGAR --- */
async function atualizarStatus(contato, novoStatus) {
  const userId = localStorage.getItem('user_id');
  const antigo = contato.status;
  menuStatus.value.visivel = false;
  try {
    // Atualiza status
    const r = await fetch(`${BASE}/update_status.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ contact_id: contato.id, status: novoStatus, user_id: userId }),
    });
    const d = await r.json();

    // LOG status change
    await fetch(`${BASE}/log_status_change.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        contact_id: contato.id,
        email: contato.email,
        old_status: antigo,
        new_status: novoStatus,
        user_id: userId,
      }),
    });

    Notify.create({ message: d.message || 'Status atualizado', color: r.ok ? 'green' : 'red' });
    if (r.ok) await carregarContatos();
  } catch (e) { Notify.create({ message: 'Falha de conexão', color: 'red' }); }
}

/* --- Comentários --- */
async function abrirComentarios(contato) {
  // eslint-disable-next-line no-underscore-dangle
  contato._mostrarComentarios = !contato._mostrarComentarios;
  // eslint-disable-next-line no-use-before-define, no-underscore-dangle
  if (contato._mostrarComentarios) await carregarComentarios(contato.id);
}
async function carregarComentarios(contactId) {
  const userId = localStorage.getItem('user_id');
  try {
    const response = await fetch(`${BASE}/get_comments.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ contact_id: contactId, user_id: userId }),
    });
    const data = await response.json();
    if (response.ok && data.data) {
      comentarios.value[contactId] = data.data;
    } else comentarios.value[contactId] = [];
  } catch (err) { comentarios.value[contactId] = []; }
}
async function adicionarComentario(contactId) {
  const userId = localStorage.getItem('user_id');
  const comment = novoComentario.value[contactId];
  if (!comment) return;
  const imageUrl = usuarioLogado.imageUrl || usuarioLogado.image_url || '';
  try {
    const response = await fetch(`${BASE}/add_comment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        contact_id: contactId,
        comment,
        user_id: userId,
        image_url: imageUrl,
      }),
    });
    const data = await response.json();
    if (response.ok) {
      Notify.create({ message: data.message || 'Comentário adicionado.', color: 'green' });
      novoComentario.value[contactId] = '';
      await carregarComentarios(contactId);
    } else {
      Notify.create({ message: data.message || 'Erro ao adicionar comentário.', color: 'red' });
    }
  } catch (err) {
    Notify.create({ message: 'Erro de conexão.', color: 'red' });
  }
}
function editarComentario(contactId, commentObj) {
  contatoEditandoComentario.value = contactId;
  comentarioEditando.value = commentObj;
  comentarioEditandoTexto.value = commentObj.comment;
  editarModal.value = true;
}
async function salvarEdicaoComentario() {
  const userId = localStorage.getItem('user_id');
  // eslint-disable-next-line camelcase
  const comment_id = comentarioEditando.value.id;
  const comment = comentarioEditandoTexto.value;
  try {
    const response = await fetch(`${BASE}/update_comment.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        // eslint-disable-next-line camelcase
        comment_id,
        comment,
        user_id: userId,
      }),
    });
    const data = await response.json();
    if (response.ok) {
      Notify.create({ message: data.message || 'Comentário editado.', color: 'green' });
      editarModal.value = false;
      await carregarComentarios(contatoEditandoComentario.value);
    } else {
      Notify.create({ message: data.message || 'Erro ao editar comentário.', color: 'red' });
    }
  } catch (err) {
    Notify.create({ message: 'Erro de conexão.', color: 'red' });
  }
}

/* --- Montagem --- */
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
  position: relative;
  padding-bottom: 8px;
}
.status-dot-large {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 1px 8px 0 rgba(60,60,60,0.18);
  z-index: 2;
}
.actions-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-left: 6px;
  padding-right: 6px;
  min-height: 48px;
}
.comment-box {
  background: #fff;
  border-radius: 8px;
  padding: 6px 10px;
  box-shadow: 0 0 3px #ddd;
}
</style>
