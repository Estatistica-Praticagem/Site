<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

const router = useRouter();

onMounted(() => {
  // Pega o idioma do navegador, default para 'en' se não existir
  const idiomaRaw = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
  const idioma = idiomaRaw.slice(0, 2); // Fica 'pt', 'en', etc

  // Sempre atualiza a variável do localStorage!
  localStorage.setItem('lang', idioma);

  // Se está na página inicial, redireciona
  if (
    window.location.pathname === '/'
    || window.location.pathname === '/index.html'
  ) {
    const caminho = idioma === 'pt' ? '/br' : '/en';
    router.replace(caminho);
  }
});
</script>
