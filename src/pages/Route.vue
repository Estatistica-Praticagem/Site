<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

const router = useRouter();

onMounted(() => {
  // Se estiver no hash #/weatherNow, redireciona para a rota declarada no Vue Router
  if (window.location.hash === '#/weatherNow') {
    router.replace('/weatherNow');
    return;
  }

  // Pega o idioma do navegador, default para 'en' se não existir
  const idiomaRaw = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
  const idioma = idiomaRaw.slice(0, 2); // 'pt', 'en', etc

  // Sempre atualiza o idioma salvo no localStorage
  localStorage.setItem('lang', idioma);

  // Salva os parâmetros UTM, se houver
  const queryParams = new URLSearchParams(window.location.search);
  const queryObject = Object.fromEntries(queryParams.entries());

  // Redireciona se estiver na raiz
  if (
    window.location.pathname === '/'
    || window.location.pathname === '/index.html'
  ) {
    const caminho = idioma === 'pt' ? '/br' : '/en';
    router.replace({ path: caminho, query: queryObject });
  }
});
</script>
