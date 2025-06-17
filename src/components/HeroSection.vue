<template>
  <section id="section-hero" class="section-hero flex flex-center">
    <div class="hero-content" id="hero-content">
      <h1 class="text-h3 text-weight-bold text-white" id="titulo-hero">Horizonte BI</h1>
      <p class="text-subtitle1 text-white q-mt-sm" id="subtitulo-hero">
        Soluções Globais, Resultados Locais
      </p>
      <p class="q-mt-md text-white" id="descricao-hero">
        A Orizzontte BI é uma consultoria de marketing full-service com profunda experiência em Business Intelligence.
        Atendemos clientes globais de diversos setores em todo o ciclo do marketing digital—da estratégia e execução à otimização de performance.<br><br>
        Também oferecemos soluções robustas de dados, dashboards inteligentes e análises completas—sempre personalizadas para o objetivo de cada cliente.
        Nossos relatórios detalhados e recomendações práticas capacitam empresas a tomar decisões informadas que geram resultados reais e significativos.
        <br><br>
        <a href="#" @click.prevent="showCard = !showCard" class="leia-mais-link">
          {{ showCard ? 'Ocultar' : 'Leia mais' }}
        </a>
      </p>

      <transition name="fade">
        <div v-if="showCard" class="q-mt-md text-white text-body1 text-left leading" id="conteudo-card">
          Como uma empresa orientada por dados, capacitamos negócios aproveitando informações relevantes e confiáveis para avaliar estratégias atuais e desenhar novos caminhos de crescimento.<br><br>
          Usamos dados como recurso estratégico para impulsionar decisões informadas, otimizar processos, melhorar a performance e desbloquear potencial para um crescimento sustentável.<br><br>
          Nossa abordagem está fundamentada em uma cultura que valoriza insights orientados por dados em todos os ciclos do negócio.
          Oferecemos as tecnologias, ferramentas e expertise necessárias para coletar, armazenar, analisar e interpretar grandes volumes de dados de forma eficiente, ajudando nossos clientes a transformar informação em estratégias acionáveis que entregam resultados.
        </div>
      </transition>
    </div>
  </section>
</template>

<script setup>
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
  padding: 32px 0;
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

.fade-enter-active, .fade-leave-active {
  transition: opacity .35s cubic-bezier(.4,0,.2,1);
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
