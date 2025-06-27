<template>
  <section id="services" class="section-services">
    <div class="section-content">
      <!-- Rows with the 3 cards -->
      <div class="row items-start justify-between cards-wrapper services-high">
        <!-- CARD 1: Topics with scroll -->
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

        <!-- CARD 2: Description (wider, same background as external block)-->
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
        <!-- IMAGE CARD 3 REMOVED -->
      </div>

      <!-- BUTTON BELOW THE CARDS, INSIDE THE CONTAINER -->
      <div class="q-mt-xl text-right">
        <q-btn
          id="btn-fale-conosco"
          data-gtm="btn-fale-conosco"
          color="primary"
          label="Contact us"
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
    titulo: 'Complete tracking of the customer journey',
    descricao: `We implement and configure tools such as Google Tag Manager, pixels, and tags to track user behavior at each stage of navigation.
We map buttons, clicks, and custom events to ensure accurate and actionable data from the first access to conversion.`,
    imagem: new URL('src/assets/services/ETLProcess.png', import.meta.url).href,
  },
  {
    titulo: 'Creation of websites, onepages and landing pages',
    descricao: `We develop responsive websites, single pages, and landing pages optimized for conversion.
All projects already include the tracking structure configured, ensuring that each interaction is captured correctly.`,
    imagem: new URL('src/assets/services/analytc.png', import.meta.url).href,
  },
  {
    titulo: 'Data Warehouse Construction',
    descricao: `We create centralized and integrated data environments, allowing different sources (such as CRM, media, website, and sales) to interact with each other.
The result is a solid and reliable foundation for analysis and strategic decisions.`,
    imagem: new URL('src/assets/services/sites.png', import.meta.url).href,
  },
  {
    titulo: 'Data-driven visualization',
    descricao: `We develop interactive and automated dashboards that translate data into clear insights.
The visualizations are connected to up-to-date sources and designed to facilitate decision-making for areas such as marketing, sales, and management.`,
    imagem: new URL('src/assets/services/data_driver.png', import.meta.url).href,
  },
  {
    titulo: 'Automated analyses with Artificial Intelligence',
    descricao: `We use AI algorithms to identify patterns, predict behaviors, and automate complex analyses.
This enables quick and actionable insights based on large volumes of data.`,
    imagem: new URL('src/assets/services/ia.png', import.meta.url).href,
  },
  {
    titulo: 'Strategic planning for campaigns and clients',
    descricao: `We help define goals, KPIs, and personalized tactics for campaigns and businesses.
Based on data, we create strategies aligned with brand objectives, optimizing investments and results.`,
    imagem: new URL('src/assets/services/midea_social.jpg', import.meta.url).href,
  },
  {
    titulo: 'Social Media Planning',
    descricao: 'We develop calendars, topics, and strategic guidelines for social networks, always aligned with the brand positioning and the behavior of the target audience.',
    imagem: new URL('src/assets/services/midia_online.png', import.meta.url).href,
  },
  {
    titulo: 'Online Media (Google, Meta)',
    descricao: `We plan, operate, and optimize campaigns on platforms such as Google Ads, Meta Ads, and others.
We monitor metrics such as CPC, CTR, and conversions to maximize return on investment.`,
    imagem: new URL('src/assets/services/google_meta.png', import.meta.url).href,
  },
  {
    titulo: 'CRM (Customer Relationship Management)',
    descricao: `We support the implementation, organization, and use of CRM tools to capture, nurture, and qualify leads.
This ensures a more efficient relationship with the customer throughout the funnel.`,
    imagem: new URL('src/assets/services/crm.png', import.meta.url).href,
  },
  {
    titulo: 'Other Services',
    descricao: 'We have specialized partners to develop creative pieces such as videos, artwork, texts, and other visual content, ensuring quality and alignment with the marketing strategy.',
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
/* Services block +40% height */
.services-high {
  min-height: 546px; /* 390px * 1.4 = 546px */
}

/* SIDE CARD */
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

/* DESCRIPTION CARD, same background as section-services */
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

/* Highlighted text (rounded gray background on text) */
.highlight-bg {
  background: linear-gradient(to top, rgba(235, 233, 233, 0.95) 60%, transparent 100%);
  border-radius: 6px;
  padding: 0 4px;
  box-decoration-break: clone;
  -webkit-box-decoration-break: clone;
  display: inline;
}

/* Remove third column on desktop */
@media (min-width: 1025px) {
  .col-md-9 {
    flex: 0 0 74.5%;
    max-width: 74.5%;
  }
}

/* FINAL CTA BUTTON */
.final-cta {
  max-width: 280px;
  width: 100%;
  font-size: 1rem;
  font-weight: 600;
}
.text-right {
  text-align: right;
}

/* MOBILE: center button */
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
