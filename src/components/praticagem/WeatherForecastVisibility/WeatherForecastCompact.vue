<template>
  <div class="forecast-cards-bg">
    <div class="forecast-cards-container">
      <div
        v-for="day in days"
        :key="day.date"
        class="forecast-card"
        :class="day.phenomenon.color"
      >
        <!-- Ícone e nome -->
        <div class="phenomenon-row">
          <span class="phenomenon-icon" v-html="day.phenomenon.svgIcon"></span>
          <span class="phenomenon-name">{{ day.phenomenon.name }}</span>
        </div>
        <!-- Data -->
        <div class="date-label">{{ day.dateLabel }}</div>

        <div class="values-grid">
          <div>
            <div class="label">Máxima</div>
            <div class="val-max">{{ day.temp_max }}°</div>
          </div>
          <div>
            <div class="label">Mínima</div>
            <div class="val-min">{{ day.temp_min }}°</div>
          </div>
          <div>
            <div class="label">Umidade média</div>
            <div class="val-humidity">{{ day.humidity_avg }}%</div>
          </div>
          <div>
            <div class="label">Prob. Nevoeiro</div>
            <div :class="['val-fog', fogProbColor(day.fogProb)]">{{ day.fogProb }}%</div>
          </div>
        </div>
        <div class="weather-id-row">
          <q-tooltip anchor="bottom middle">
            Código dominante: <b>{{ day.weather_id }}</b>
          </q-tooltip>
          <span class="weather-id">#{{ day.weather_id }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// eslint-disable-next-line no-unused-vars
const props = defineProps({
  days: { type: Array, required: true },
});
function fogProbColor(prob) {
  if (prob >= 80) return 'text-purple';
  if (prob >= 50) return 'text-purple-light';
  if (prob > 0) return 'text-blue';
  return 'text-grey';
}
</script>

<style scoped>
.forecast-cards-bg {
  background: linear-gradient(120deg, #f5f9ff 70%, #e3eaf6 100%);
  padding: 26px 0 20px 0;
  width: 100%;
}
.forecast-cards-container {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
  max-width: 1100px;
  margin: auto;
}
.forecast-card {
  min-width: 210px;
  max-width: 255px;
  min-height: 205px;
  border-radius: 2rem;
  box-shadow: 0 8px 32px #2d37481a, 0 2px 8px #1a202c10;
  border: 1.5px solid #e3eaf0;
  padding: 23px 23px 18px 23px;
  background: rgba(255,255,255,0.97);
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: box-shadow .15s, transform .13s;
  cursor: pointer;
}
.forecast-card:hover {
  box-shadow: 0 12px 46px #0072c21b, 0 2px 12px #1a202c13;
  transform: translateY(-3px) scale(1.035);
}

/* Fenômeno */
.phenomenon-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 5px;
}
.phenomenon-icon {
  font-size: 2.2rem;
  filter: drop-shadow(0 1px 3px #fff5);
}
.phenomenon-name {
  font-weight: bold;
  font-size: 1.15rem;
  line-height: 1.2;
}

/* Data */
.date-label {
  font-family: 'JetBrains Mono', 'Fira Mono', monospace;
  font-size: 0.93em;
  color: #465e7a;
  margin-bottom: 13px;
  font-weight: 600;
  letter-spacing: 0.01em;
}

/* Grid dos valores */
.values-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px 24px;
  width: 100%;
  margin-bottom: 5px;
}
.label {
  font-size: 0.80em;
  color: #6d7990;
  font-weight: 600;
  margin-bottom: 1px;
}
.val-max {
  color: #de1616;
  font-size: 1.15em;
  font-weight: bold;
  letter-spacing: .02em;
}
.val-min {
  color: #2283d5;
  font-size: 1.15em;
  font-weight: bold;
  letter-spacing: .02em;
}
.val-humidity {
  color: #2888a4;
  font-size: 1.10em;
  font-weight: 600;
}
.val-fog.text-purple      { color: #7c3aed; font-weight: 700; }
.val-fog.text-purple-light{ color: #b39ddb; font-weight: 700; }
.val-fog.text-blue        { color: #2483c9; font-weight: 700; }
.val-fog.text-grey        { color: #838a94; font-weight: 600; }

/* Código weather_id */
.weather-id-row {
  margin-top: 7px;
  font-size: 0.97em;
  display: flex;
  align-items: center;
  gap: 6px;
}
.weather-id {
  background: #fffefb99;
  border-radius: 7px;
  color: #485054;
  font-family: 'JetBrains Mono', 'Fira Mono', monospace;
  font-size: 0.94em;
  border: 1px solid #e3eaf0;
  padding: 3px 8px;
  margin-left: 2px;
}

/* Gradientes fenômeno (classes já existentes no seu projeto) */
.phenomenon-sun        { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-fewclouds  { background: linear-gradient(120deg, #e3f2fd 70%, #bbdefb 100%) !important; color: #0d47a1 !important; }
.phenomenon-scattered  { background: linear-gradient(120deg, #e3f2fd 70%, #90caf9 100%) !important; color: #1976d2 !important; }
.phenomenon-broken     { background: linear-gradient(120deg, #f5f5f5 70%, #90a4ae 100%) !important; color: #37474f !important; }
.phenomenon-overcast   { background: linear-gradient(120deg, #cfd8dc 70%, #78909c 100%) !important; color: #212121 !important; }
.phenomenon-fog        { background: linear-gradient(120deg, #eceff1 70%, #90a4ae 100%) !important; color: #333 !important; }
.phenomenon-mist       { background: linear-gradient(120deg, #e3eaf0 70%, #b0bec5 100%) !important; color: #333 !important; }
.phenomenon-haze       { background: linear-gradient(120deg, #fffde7 70%, #ffe082 100%) !important; color: #d19700 !important; }
.phenomenon-smoke      { background: linear-gradient(120deg, #fff9c4 70%, #ffe082 100%) !important; color: #ff7043 !important; }
.phenomenon-unknown    { background: #fff !important; color: #888 !important; }

@media (max-width: 750px) {
  .forecast-card {
    min-width: 150px;
    max-width: 99vw;
    padding: 13px 7px 12px 7px;
    font-size: 0.94em;
  }
  .forecast-cards-container {
    gap: 15px;
  }
}
</style>
