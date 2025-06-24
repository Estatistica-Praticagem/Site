<template>
  <section id="section-hero" class="section-hero flex flex-center">
    <div class="hero-content" id="hero-content">
      <h1 class="text-h3 text-weight-bold text-white" id="titulo-hero">Horizonte BI</h1>
      <p class="text-subtitle1 text-white q-mt-sm" id="subtitulo-hero">
        Soluções Globais, Resultados Locais
      </p>
      <p class="q-mt-md text-white" id="descricao-hero">
        A Orizzontte BI é uma consultoria de marketing full-service com profunda experiência em Business Intelligence.<br>
        Atendemos clientes globais de diversos setores em todo o ciclo do marketing digital—da estratégia e execução à otimização de performance.<br><br>
        Também oferecemos soluções robustas de dados, dashboards inteligentes e análises completas—sempre personalizadas para o objetivo de cada cliente.<br>
        Nossos relatórios detalhados e recomendações práticas capacitam empresas a tomar decisões informadas que geram resultados reais e significativos.<br><br>
        <a href="#" @click.prevent="showCard = !showCard" class="leia-mais-link">
          {{ showCard ? 'Ocultar' : 'Leia mais' }}
        </a>
      </p>

      <transition name="fade-height">
        <div
          v-if="showCard"
          class="q-mt-md text-white text-body1 text-left leading extra-hero-content"
          id="conteudo-card"
        >
          Como uma empresa orientada por dados, capacitamos negócios aproveitando informações relevantes e confiáveis para avaliar estratégias atuais e desenhar novos caminhos de crescimento.<br><br>
          Usamos dados como recurso estratégico para impulsionar decisões informadas, otimizar processos, melhorar a performance e desbloquear potencial para um crescimento sustentável.<br><br>
          Nossa abordagem está fundamentada em uma cultura que valoriza insights orientados por dados em todos os ciclos do negócio.<br>
          Oferecemos as tecnologias, ferramentas e expertise necessárias para coletar, armazenar, analisar e interpretar grandes volumes de dados de forma eficiente, ajudando nossos clientes a transformar informação em estratégias acionáveis que entregam resultados.
        </div>
      </transition>
    </div>
  </section>
</template>

<script setup>
// eslint-disable-next-line import/newline-after-import
import { ref } from 'vue';
const showCard = ref(false);
</script>

<style scoped>
.section-hero {
  min-height: 60vh;
  background: linear-gradient(135deg, #0b9dd3 0%, #0b4f76 100%);
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
  padding: 32px 16px;
  /* padding reduzida em telas menores */
}

.leading {
  line-height: 1.72;
}

.leia-mais-link {
  display: inline-block;
  margin-top: 12px;
  font-weight: 500;
  color: #ffeb3b;
  text-decoration: underline;
  cursor: pointer;
  transition: color 0.3s ease;
}

.leia-mais-link:hover {
  color: #fff59d;
}

/* Transição de altura e fade */
.fade-height-enter-active,
.fade-height-leave-active {
  transition: opacity .35s cubic-bezier(.4,0,.2,1), max-height .45s cubic-bezier(.4,0,.2,1), margin .35s;
  overflow: hidden;
}
.fade-height-enter-from,
.fade-height-leave-to {
  opacity: 0;
  max-height: 0;
  margin-top: 0 !important;
}
.fade-height-enter-to,
.fade-height-leave-from {
  opacity: 1;
  max-height: 800px; /* pode ajustar conforme seu texto */
  margin-top: 18px;
}

/* Responsividade */
@media (max-width: 900px) {
  .hero-content {
    max-width: 95vw;
    padding: 28px 8px;
  }
  .section-hero {
    min-height: 50vh;
    padding: 0;
  }
  .text-h3 {
    font-size: 2.2rem;
  }
}
@media (max-width: 600px) {
  .hero-content {
    max-width: 100vw;
    padding: 18px 4px;
  }
  .text-h3 {
    font-size: 1.5rem;
  }
}
</style>
