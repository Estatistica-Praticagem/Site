<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const idioma = navigator.language || navigator.userLanguage; // ex: 'pt-BR', 'en-US'

if (window.location.pathname === '/' || window.location.pathname === '/index.html') {
  const caminho = idioma.startsWith('pt') ? '/br' : '/en';
  router.replace(caminho);
}
</script>
