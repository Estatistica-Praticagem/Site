<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

const router = useRouter();

onMounted(() => {
  // Pega o idioma do navegador, default para 'en' se não existir
  const idiomaRaw = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
  const idioma = idiomaRaw.slice(0, 2); // 'pt', 'en', etc

  // Sempre atualiza o idioma salvo no localStorage
  localStorage.setItem('lang', idioma);

  // Salva os parâmetros UTM, se houver
  const queryParams = new URLSearchParams(window.location.search);
  const queryObject = Object.fromEntries(queryParams.entries());

  // if (!localStorage.getItem('utm_full_url') && window.location.search) {
  //   localStorage.setItem('utm_full_url', window.location.href);
  // }

  // Se estiver na página inicial, redireciona com os parâmetros preservados
  if (
    window.location.pathname === '/'
    || window.location.pathname === '/index.html'
  ) {
    const caminho = idioma === 'pt' ? '/br' : '/en';
    router.replace({ path: caminho, query: queryObject });
  }
});
</script>
