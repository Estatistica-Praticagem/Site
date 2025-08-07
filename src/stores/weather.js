// src/stores/weather.js
import { defineStore } from 'pinia';

export const useWeatherStore = defineStore('weather', {
  state: () => ({
    weatherLast: null,
    weatherHistory: [],
    weatherForecast: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchLast() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch('https://www.meusimulador.com/praticagem/backend/praticagem/get_table_mestre_5min_tratada_bq.php?limit=1');
        const json = await res.json();
        if (json.success && json.data && json.data.length) {
          // eslint-disable-next-line prefer-destructuring
          this.weatherLast = json.data[0];
        } else {
          this.weatherLast = null;
        }
      } catch (err) {
        this.error = 'Erro ao buscar dados.';
        this.weatherLast = null;
      }
      this.loading = false;
    },

    async fetchHistory() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch('https://www.meusimulador.com/praticagem/backend/praticagem/get_table_mestre_5min_tratada_bq.php?limit=1000');
        const json = await res.json();
        if (json.success && json.data) {
          this.weatherHistory = json.data;
        } else {
          this.weatherHistory = [];
        }
      } catch (err) {
        this.error = 'Erro ao buscar histórico.';
        this.weatherHistory = [];
      }
      this.loading = false;
    },

    async fetchForecast() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch('https://www.meusimulador.com/praticagem/backend/praticagem/get_prev_table_mestre_5min_tratada_bq.php');
        const json = await res.json();
        if (json.success && json.data) {
          this.weatherForecast = json.data;
        } else {
          this.weatherForecast = [];
        }
      } catch (err) {
        this.error = 'Erro ao buscar previsão.';
        this.weatherForecast = [];
      }
      this.loading = false;
    },
    async fetchOpenWeatherForecast() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch('https://www.meusimulador.com/praticagem/backend/praticagem/get_prev_openweather_forecast_bq.php');
        const json = await res.json();
        if (json.success && json.data) {
          this.openWeatherForecast = json.data;
        } else {
          this.openWeatherForecast = [];
        }
      } catch (err) {
        this.error = 'Erro ao buscar previsão OpenWeather.';
        this.openWeatherForecast = [];
      }
      this.loading = false;
    },

    /* ====================== NOVO: CORRENTEZA ====================== */
    // Helper genérico: chama o endpoint PHP unificado de correnteza
    async fetchCorrenteza({ tabela = '5min', limit = 1000 } = {}) {
      // eslint-disable-next-line no-undef
      const url = `${BQ_BASE}/get_prev_correnteza_forecast_bq.php?tabela=${encodeURIComponent(tabela)}&limit=${encodeURIComponent(limit)}`;
      const res = await fetch(url, { method: 'GET' });
      const json = await res.json();
      if (!json.success) {
        const msg = json.erro || 'Falha ao consultar correnteza.';
        throw new Error(msg);
      }
      return json.data || [];
    },

    // Último ponto 5-min
    async fetchCorrentezaLast5Min() {
      this.loading = true; this.error = null;
      try {
        const data = await this.fetchCorrenteza({ tabela: '5min', limit: 1 });
        this.correntezaLast5Min = data[0] || null;
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza 5min.';
        this.correntezaLast5Min = null;
      }
      this.loading = false;
    },

    // Histórico 5-min (ajuste o limit conforme a necessidade)
    async fetchCorrenteza5Min(limit = 1000) { // ~24h em 5min
      this.loading = true; this.error = null;
      try {
        this.correnteza5Min = await this.fetchCorrenteza({ tabela: '5min', limit });
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza 5min.';
        this.correnteza5Min = [];
      }
      this.loading = false;
    },

    // Série horária (ajuste o limit conforme a necessidade)
    async fetchCorrentezaHourly(limit = 164) { // ~7 dias
      this.loading = true; this.error = null;
      try {
        this.correntezaHourly = await this.fetchCorrenteza({ tabela: 'hora', limit });
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza horária.';
        this.correntezaHourly = [];
      }
      this.loading = false;
    },
  },
});
