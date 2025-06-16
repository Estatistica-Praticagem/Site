<template>
  <section class="section-hero flex flex-center">
    <div class="hero-content">
      <h1 class="text-h3 text-weight-bold text-white">Horizonte BI</h1>
      <p class="text-subtitle1 text-white q-mt-sm">
        Soluções Globais, Resultados Locais
      </p>
      <p class="q-mt-md text-white">
        A Orizzontte BI é uma consultoria de marketing full-service com profunda experiência em Business Intelligence.
        Atendemos clientes globais de diversos setores em todo o ciclo do marketing digital—da estratégia e execução à otimização de performance.<br><br>
        Também oferecemos soluções robustas de dados, dashboards inteligentes e análises completas—sempre personalizadas para o objetivo de cada cliente.
        Nossos relatórios detalhados e recomendações práticas capacitam empresas a tomar decisões informadas que geram resultados reais e significativos.
      </p>

      <div class="q-mt-xl">
        <q-btn
          color="primary"
          round
          size="lg"
          icon="add"
          label="Saiba mais"
          @click="showCard = !showCard"
          class="btn-saiba-mais"
        />
      </div>

      <transition name="fade">
        <q-card v-if="showCard" class="card-content q-mt-xl">
          <q-card-section>
            <h3 class="text-h5 text-primary q-mb-sm">
              Metodologia e o porquê: Data Driven Business
            </h3>
            <p>
              Como uma empresa orientada por dados, capacitamos negócios aproveitando informações relevantes e confiáveis para avaliar estratégias atuais e desenhar novos caminhos de crescimento.<br><br>
              Usamos dados como recurso estratégico para impulsionar decisões informadas, otimizar processos, melhorar a performance e desbloquear potencial para um crescimento sustentável.<br><br>
              Nossa abordagem está fundamentada em uma cultura que valoriza insights orientados por dados em todos os ciclos do negócio.
              Oferecemos as tecnologias, ferramentas e expertise necessárias para coletar, armazenar, analisar e interpretar grandes volumes de dados de forma eficiente, ajudando nossos clientes a transformar informação em estratégias acionáveis que entregam resultados.
            </p>
          </q-card-section>
        </q-card>
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

.card-content {
  max-width: 700px;
  margin: 0 auto;
  border-radius: 22px;
  box-shadow: 0 2px 18px 0 rgba(10,120,190,.08);
}

.btn-saiba-mais {
  margin-top: 12px;
  font-size: 1.4rem;
  box-shadow: 0 4px 20px #0b9dd344;
  transition: transform 0.18s cubic-bezier(.42,0,.58,1);
  font-weight: bold;
}

.btn-saiba-mais:hover {
  background: linear-gradient(90deg, #0b9dd3 0%, #0b4f76 100%);
  color: #fff;
  transform: scale(1.08);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .35s cubic-bezier(.4,0,.2,1);
}
.fade-enter, .fade-leave-to { opacity: 0; }
</style>
