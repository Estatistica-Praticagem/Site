<template>
  <section id="footer" class="section-footer">
    <div class="footer-container">
      <h2 class="text-h4 text-center q-mb-md" style="color: aliceblue;">Parceiros</h2>
      <div class="footer-social row wrap items-center justify-center">
        <div v-for="logo in partnerLogos" :key="logo.alt" class="footer-card">
          <q-btn
            flat round dense tag="a"
            :href="logo.link || '#'"
            target="_blank"
            class="footer-card-btn"
            :title="logo.alt"
            :id="'logo-' + logo.alt.toLowerCase().replace(/\s+/g, '-')"
            :data-gtm="'logo-' + logo.alt.toLowerCase().replace(/\s+/g, '-')"
          >
            <q-img
              :src="logo.src"
              :alt="logo.alt"
              fit="contain"
              class="footer-social-logo"
              style="min-width: 120px; min-height: 60px;"
            />
          </q-btn>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// Importe as imagens usando import ou new URL
import DreamLandSrc from 'src/assets/clients/DreamLand.png';
import RfillSrc from 'src/assets/clients/fr-imoveis.png';
import RGPilotsSrc from 'src/assets/clients/logo-rgpilots.png';
import UnisinosSrc from 'src/assets/clients/unisinos.png';
import ViaMarteSrc from 'src/assets/clients/viamarte.png';
import EdenredSrc from 'src/assets/clients/edenred.png';
import OakberrySrc from 'src/assets/clients/oakberry.svg';
import StihlSrc from 'src/assets/clients/Stihl_logo.png';
import TKESrc from 'src/assets/clients/TKE-logo.png';

const partnerLogos = [
  { src: RGPilotsSrc, alt: 'RG Pilots', link: 'https://www.rgpilots.com.br' },
  { src: RfillSrc, alt: 'Rfill', link: 'https://www.rfill.co/' },
  { src: UnisinosSrc, alt: 'Unisinos', link: 'https://www.unisinos.br' },
  { src: ViaMarteSrc, alt: 'Via Marte', link: 'https://www.viamarte.com.br' },
  { src: DreamLandSrc, alt: 'DreamLand', link: 'https://www.dreamland.com.br/gramado' },
  { src: EdenredSrc, alt: 'Edenred', link: 'https://www.edenred.com' },
  { src: OakberrySrc, alt: 'Oakberry', link: 'https://www.oakberry.com' },
  { src: StihlSrc, alt: 'Stihl', link: 'https://loja.stihl.com.br' },
  { src: TKESrc, alt: 'TKE', link: 'https://www.tkelevator.com' },
];
</script>

<style scoped>
.section-footer {
  background: linear-gradient(135deg, #0b9dd3 0%, #0b4f76 100%);
  padding: 40px 16px;
}
.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
}
.footer-title {
  color: #fff;
  font-size: 1.6rem;
  margin-bottom: 24px;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.footer-social {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  justify-content: center;
}
.footer-card {
  background: transparent;
  padding: 16px;
  transition: background 0.3s, transform 0.3s;
}
.footer-card:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-4px);
}
.footer-card-btn {
  padding: 0;
}
.footer-social-logo {
  object-fit: contain;
}
@media (max-width: 600px) {
  .footer-social-logo {
    min-width: 80px;
    min-height: 40px;
  }
}
</style>
