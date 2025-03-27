<template>
  <q-page class="q-pa-md">
    <q-slide-transition>
      <q-card flat bordered class="q-pa-lg">

        <!-- Hero / Header -->
        <div class="text-center q-mb-xl">
          <h1 class="text-h4 text-weight-bold">
            Pegoraro Consulting
          </h1>
          <p class="text-subtitle1">
            Global Solutions, Local Results
          </p>
        </div>

        <!-- About the Company -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">About the Company</h2>
          <!-- Imagem ilustrativa: About the Company -->
          <q-img
            src="~assets/About the Company.png"
            style="max-width: 450px;"
            class="q-my-md"
            spinner-color="primary"
          />
          <p>
            Pegoraro Consulting is a full-service marketing consultancy with
            deep expertise in Business Intelligence. We support global clients
            throughout the digital marketing cycle—from strategy and execution
            to performance optimization. Our data solutions, dashboards, and
            analytics are customized to each client's goals, helping businesses
            make informed decisions that drive real-world results.
          </p>
        </div>

        <!-- Data Driven Business -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">Data Driven Business</h2>
          <!-- Exemplo sem imagem específica: usar se tiver outra imagem -->
          <p>
            We use relevant data to evaluate strategies and design new
            growth paths. Our approach optimizes processes and drives
            performance with data-driven insights, tools, and technologies
            to turn raw data into smart strategies.
          </p>
        </div>

        <!-- Skills and Expertise -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">Skills & Expertise</h2>
          <q-img
            src="~assets/Skills&Expertise.png"
            style="max-width: 450px;"
            class="q-my-md"
            spinner-color="primary"
          />
          <ul>
            <li>
              <strong>ETL Process:</strong> Extracting, transforming, and
              loading data to ensure quality insights.
            </li>
            <li>
              <strong>Database:</strong> Processing and transforming data
              with SQL for structured storage.
            </li>
            <li>
              <strong>Dashboards:</strong> Visual tools for campaign
              reporting and optimization.
            </li>
            <li>
              <strong>Complete Media Funnel:</strong> End-to-end campaign
              performance tracking.
            </li>
          </ul>
        </div>

        <!-- Big Clients in Brazil -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">Big Clients in Brazil</h2>
          <q-img
            src="~assets/Rfill.png"
            style="max-width: 120px;"
            class="q-ma-sm"
            spinner-color="primary"
          />
          <q-img
            src="~assets/RGPilots.png"
            style="max-width: 120px;"
            class="q-ma-sm"
            spinner-color="primary"
          />
          <p class="q-my-md">
            <ul>
              <li>
                <strong>International Port of Rio Grande:</strong> Real-time
                tide and current predictions using ML models since 2020.
              </li>
              <li>
                <strong>Via Marte Ecommerce:</strong> Automated media data
                collection and dashboarding to boost sales since 2024.
              </li>
              <li>
                <strong>Agency Rfill:</strong> Marketing automation and lead
                analysis for luxury real estate since 2024.
              </li>
            </ul>
          </p>
        </div>

        <!-- Global Experiences -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">Global Experiences</h2>
          <!-- Exemplo de carrossel para logos internacionais -->
          <q-carousel
            v-model="slide"
            animated
            navigation
            navigation-position="bottom"
            control-type="flat"
            class="bg-grey-2 q-mb-md"
            style="max-width: 450px; margin: 0 auto;"
          >
            <q-carousel-slide name="1">
              <q-img
                src="~assets/AstraZeneca.png"
                spinner-color="primary"
                style="max-height: 220px;"
              />
            </q-carousel-slide>
            <q-carousel-slide name="2">
              <q-img
                src="~assets/Daiichi-Sankyo.png"
                spinner-color="primary"
                style="max-height: 220px;"
              />
            </q-carousel-slide>
            <q-carousel-slide name="3">
              <q-img
                src="~assets/embecta.png"
                spinner-color="primary"
                style="max-height: 220px;"
              />
            </q-carousel-slide>
            <q-carousel-slide name="4">
              <q-img
                src="~assets/GeneralElectric.png"
                spinner-color="primary"
                style="max-height: 220px;"
              />
            </q-carousel-slide>
          </q-carousel>

          <p>
            Managing business intelligence for clients in Brazil and
            Latin America, developing automations, CRM workflows, and
            dashboards for better marketing performance and lead
            conversion.
          </p>
        </div>

        <!-- Offered Services -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">Offered Services</h2>
          <q-img
            src="~assets/OfferedServices.png"
            style="max-width: 450px;"
            class="q-my-md"
            spinner-color="primary"
          />
          <p>
            End-to-end marketing performance services: ETL, data
            analysis, dashboards, CRM strategy, and performance
            campaigns.
          </p>
        </div>

        <!-- Team -->
        <q-separator spaced />
        <div>
          <h2 class="text-h5 text-primary">The Team</h2>
          <ul>
            <li>
              <strong>Kévi Pegoraro:</strong> Data Specialist (USA)
            </li>
            <li>
              <strong>Lorenzo de Oliveira:</strong> Media Specialist
              (Brazil)
            </li>
            <li>
              <strong>Carolina Schaefer:</strong> Planning Specialist
              (Brazil)
            </li>
            <li>
              <em>Design & creative outsourced</em>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <q-separator spaced />
        <div class="text-center q-mt-xl">
          <h2 class="text-h5 text-primary">Contact Us</h2>
          <q-img
            src="~assets/ContactUs.png"
            style="max-width: 200px;"
            class="q-my-md"
            spinner-color="primary"
          />
          <p>
            <strong>Kévi Pegoraro</strong>
          </p>
          <p>
            Email:
            <a href="mailto:kevipegoraro@hotmail.com">
              kevipegoraro@hotmail.com
            </a>
          </p>
          <p>Phone: +1 (256) 871-9730</p>
          <p class="q-mt-md text-italic">
            Thank you! We look forward to working with you.
          </p>
        </div>
      </q-card>
    </q-slide-transition>
  </q-page>
</template>

<script>
export default {
  name: 'PegoraroPortfolioPage',
  data() {
    return {
      slide: '1', // controla o carrossel nas Global Experiences
    };
  },
};
</script>

<style scoped>
h2 {
  margin-top: 24px;
  margin-bottom: 12px;
}
p,
ul {
  font-size: 16px;
  line-height: 1.6;
}
.bg-grey-2 {
  background: #f5f5f5;
}
</style>
