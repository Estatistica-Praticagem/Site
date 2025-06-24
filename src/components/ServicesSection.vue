<template>
  <section id="services" class="section-services">
    <div class="section-content">
      <!-- Linhas com os 3 cards -->
      <div class="row items-start justify-between cards-wrapper services-high">
        <!-- CARD 1: Tópicos com rolagem -->
        <div class="col-12 col-md-3">
          <q-card flat class="topics-scroll glass-card q-pa-sm">
            <q-list separator>
              <q-item
                v-for="(s, i) in services"
                :key="s.titulo"
                clickable
                :active="selected === i"
                active-class="selected-service"
                @click="selected = i"
                class="service-item"
              >
                <q-item-section>{{ s.titulo }}</q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>

        <!-- CARD 2: Descrição (mais largo, fundo igual ao bloco externo)-->
        <div class="col-12 col-md-9">
          <q-card class="desc-card desc-card-expanded q-pa-xl shadow-1 rounded-borders">
            <q-card-section>
              <h4 class="text-h6 text-primary q-mb-sm">{{ services[selected].titulo }}</h4>
              <p class="text-body1 q-mt-sm description-text">
                <span class="highlight-bg">
                  {{ services[selected].descricao }}
                </span>
              </p>
            </q-card-section>
          </q-card>
        </div>
        <!-- REMOVEU O CARD 3 DE IMAGEM -->
      </div>

      <!-- BOTÃO ABAIXO DOS CARDS, DENTRO DO CONTAINER -->
      <div class="q-mt-xl text-right">
        <q-btn
          id="btn-fale-conosco"
          data-gtm="btn-fale-conosco"
          color="primary"
          label="Fale com a gente"
          icon="send"
          rounded
          @click="scrollToContato"
          class="final-cta"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const selected = ref(0);

const services = [
  {
    titulo: 'Traqueamento completo da jornada do cliente',
    descricao: `Implementamos e configuramos ferramentas como Google Tag Manager, pixels e tags para rastrear o comportamento dos usuários em cada etapa da navegação.
Fazemos o mapeamento de botões, cliques e eventos personalizados para garantir dados precisos e acionáveis desde o primeiro acesso até a conversão.`,
    imagem: new URL('src/assets/services/ETLProcess.png', import.meta.url).href,
  },
  {
    titulo: 'Criação de sites, onepages e páginas de conversão',
    descricao: `Desenvolvemos sites responsivos, páginas únicas e landing pages otimizadas para conversão.
Todos os projetos já incluem a estrutura de trackeamento configurada, garantindo que cada interação seja captada corretamente.`,
    imagem: new URL('src/assets/services/analytc.png', import.meta.url).href,
  },
  {
    titulo: 'Construção de Data Warehouse',
    descricao: `Criamos ambientes de dados centralizados e integrados, permitindo que diferentes fontes (como CRM, mídia, site e vendas) conversem entre si.
O resultado é uma base sólida e confiável para análises e decisões estratégicas.`,
    imagem: new URL('src/assets/services/sites.png', import.meta.url).href,
  },
  {
    titulo: 'Visualização orientada por dados',
    descricao: `Desenvolvemos dashboards interativos e automatizados que traduzem dados em insights claros.
As visualizações são conectadas a fontes atualizadas e pensadas para facilitar a tomada de decisão por áreas como marketing, vendas e gestão.`,
    imagem: new URL('src/assets/services/data_driver.png', import.meta.url).href,
  },
  {
    titulo: 'Análises automatizadas com Inteligência Artificial',
    descricao: `Utilizamos algoritmos de IA para identificar padrões, prever comportamentos e automatizar análises complexas.
Isso permite insights rápidos e acionáveis com base em grandes volumes de dados.`,
    imagem: new URL('src/assets/services/ia.png', import.meta.url).href,
  },
  {
    titulo: 'Planejamento estratégico de campanhas e clientes',
    descricao: `Ajudamos a definir metas, KPIs e táticas personalizadas para campanhas e negócios.
Com base em dados, criamos estratégias alinhadas aos objetivos da marca, otimizando investimentos e resultados.`,
    imagem: new URL('src/assets/services/midea_social.jpg', import.meta.url).href,
  },
  {
    titulo: 'Planejamento de Redes Sociais',
    descricao: 'Desenvolvemos calendários, pautas e diretrizes estratégicas para redes sociais, sempre alinhadas ao posicionamento da marca e ao comportamento do público-alvo.',
    imagem: new URL('src/assets/services/midia_online.png', import.meta.url).href,
  },
  {
    titulo: 'Mídia Online (Google, Meta)',
    descricao: `Planejamos, operamos e otimizamos campanhas em plataformas como Google Ads, Meta Ads e outras.
Acompanhamos métricas como CPC, CTR e conversões para maximizar o retorno sobre o investimento.`,
    imagem: new URL('src/assets/services/google_meta.png', import.meta.url).href,
  },
  {
    titulo: 'CRM (Gestão de Relacionamento com o Cliente)',
    descricao: `Apoiamos a implementação, organização e uso de ferramentas de CRM para capturar, nutrir e qualificar leads.
Isso garante um relacionamento mais eficiente com o cliente ao longo do funil.`,
    imagem: new URL('src/assets/services/crm.png', import.meta.url).href,
  },
  {
    titulo: 'Demais Serviços',
    descricao: 'Contamos com parceiros especializados para desenvolver peças criativas como vídeos, artes, textos e outros conteúdos visuais, garantindo qualidade e alinhamento com a estratégia de marketing.',
    imagem: new URL('src/assets/services/create.png', import.meta.url).href,
  },
];

function scrollToContato() {
  const el = document.getElementById('contact');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.section-services {
  margin-bottom: -40px;
  margin-top: 80px;
  background: transparent;
  padding: 60px 16px;
  border: none !important;
  box-shadow: none !important;
}
.section-content {
  max-width: 1280px;
  margin: 0 auto;
}
.cards-wrapper {
  gap: 12px;
  display: flex;
  flex-wrap: nowrap;
}
/* Bloco de serviços +40% altura */
.services-high {
  min-height: 546px; /* 390px * 1.4 = 546px */
}

/* CARD LATERAL */
.topics-scroll {
  max-height: 446px;
  max-width: 620px;
  overflow-y: auto;
  border-radius: 12px;
  outline: none !important;
  box-shadow: none !important;
  backdrop-filter: blur(8px);
  background-color: rgba(241, 239, 239, 0.4);
}

/* CARD DE DESCRIÇÃO, fundo igual ao section-services */
.desc-card,
.desc-card-expanded {
  background-color: transparent !important;
  /* border-radius: 18px; */
  border: none;
  width: 100%;
}
.desc-card-expanded {
  max-width: 100%;
  min-height: 430px;
  outline: none !important;
  box-shadow: none !important;
}

.description-text {
  line-height: 1.7;
  font-size: 1.2rem;
  white-space: pre-line;
  max-width: 100%;
  outline: none !important;
  box-shadow: none !important;
}

/* Marca-texto (fundo cinza arredondado no texto) */
.highlight-bg {
  background: linear-gradient(to top, rgba(235, 233, 233, 0.95) 60%, transparent 100%);
  border-radius: 6px;
  padding: 0 4px;
  box-decoration-break: clone;
  -webkit-box-decoration-break: clone;
  display: inline;
}

/* Remove terceira coluna no desktop */
@media (min-width: 1025px) {
  .col-md-9 {
    flex: 0 0 74.5%;
    max-width: 74.5%;
  }
}

/* BOTÃO FINAL CTA */
.final-cta {
  max-width: 280px;
  width: 100%;
  font-size: 1rem;
  font-weight: 600;
}
.text-right {
  text-align: right;
}

/* MOBILE: centraliza botão */
@media (max-width: 1024px) {
  .text-right {
    text-align: center;
  }
  .final-cta {
    margin: 32px auto 0;
  }
  .desc-card-expanded {
    min-height: 200px;
    padding: 1.2em;
  }
}
@media (max-width: 1024px) {
  .cards-wrapper {
    flex-direction: column;
    gap: 24px;
  }
  .topics-scroll {
    max-height: none;
  }
}

</style>
