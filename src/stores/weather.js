// src/stores/weather.js
import { defineStore } from 'pinia';

const HOST = 'https://www.meusimulador.com/kevi';
const API_BASE = `${HOST}/backend/praticagem`;

// Endpoints centralizados (ajuste aqui e vale pro app todo)
const EP = {
  mestre5min:              `${API_BASE}/get_table_mestre_5min_tratada_bq.php`,
  mestre5minPrev:          `${API_BASE}/get_prev_table_mestre_5min_tratada_bq.php`,
  openWeatherForecast:     `${API_BASE}/get_prev_openweather_forecast_bq.php`,
  correntezaUnified:       `${API_BASE}/get_prev_correnteza_forecast_bq.php`,
};
// Helper pra montar querystring
const q = (base, params = {}) => {
  const qs = new URLSearchParams(params);
  const sep = base.includes('?') ? '&' : '?';
  return Object.keys(params).length ? `${base}${sep}${qs.toString()}` : base;
};

export const useWeatherStore = defineStore('weather', {
  state: () => ({
    weatherLast: null,
    weatherHistory: [],
    weatherForecast: [],
    openWeatherForecast: [],

    correntezaLast5Min: null,
    correnteza5Min: [],
    correntezaHourly: [],

    loading: false,
    error: null,
  }),

  actions: {
    async fetchLast() {
      this.loading = true; this.error = null;
      try {
        const res = await fetch(q(EP.mestre5min, { limit: 1 }));
        const json = await res.json();
        this.weatherLast = (json.success && json.data?.length) ? json.data[0] : null;
      } catch (err) {
        this.error = 'Erro ao buscar dados.';
        this.weatherLast = null;
      } finally { this.loading = false; }
    },

    async fetchHistory(limit = 1000) {
      this.loading = true; this.error = null;
      try {
        const res = await fetch(q(EP.mestre5min, { limit }));
        const json = await res.json();
        this.weatherHistory = (json.success && json.data) ? json.data : [];
      } catch (err) {
        this.error = 'Erro ao buscar histórico.';
        this.weatherHistory = [];
      } finally { this.loading = false; }
    },

    async fetchForecast() {
      this.loading = true; this.error = null;
      try {
        const res = await fetch(EP.mestre5minPrev);
        const json = await res.json();
        this.weatherForecast = (json.success && json.data) ? json.data : [];
      } catch (err) {
        this.error = 'Erro ao buscar previsão.';
        this.weatherForecast = [];
      } finally { this.loading = false; }
    },

    async fetchOpenWeatherForecast() {
      this.loading = true; this.error = null;
      try {
        const res = await fetch(EP.openWeatherForecast);
        const json = await res.json();
        this.openWeatherForecast = (json.success && json.data) ? json.data : [];
      } catch (err) {
        this.error = 'Erro ao buscar previsão OpenWeather.';
        this.openWeatherForecast = [];
      } finally { this.loading = false; }
    },

    /* ====================== CORRENTEZA ====================== */
    // Endpoint unificado (hora/5min) — ajuste 'tabela' e 'limit'
    async fetchCorrenteza({ tabela = '5min', limit = 4000 } = {}) {
      const url = q(EP.correntezaUnified, { tabela, limit });
      const res = await fetch(url, { method: 'GET' });
      const json = await res.json();
      if (!json.success) {
        throw new Error(json.erro || 'Falha ao consultar correnteza.');
      }
      return json.data || [];
    },

    async fetchCorrentezaLast5Min() {
      this.loading = true; this.error = null;
      try {
        const data = await this.fetchCorrenteza({ tabela: '5min', limit: 1 });
        this.correntezaLast5Min = data[0] || null;
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza 5min.';
        this.correntezaLast5Min = null;
      } finally { this.loading = false; }
    },

    async fetchCorrenteza5Min(limit = 4000) {
      this.loading = true; this.error = null;
      try {
        this.correnteza5Min = await this.fetchCorrenteza({ tabela: '5min', limit });
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza 5min.';
        this.correnteza5Min = [];
      } finally { this.loading = false; }
    },

    async fetchCorrentezaHourly(limit = 164) {
      this.loading = true; this.error = null;
      try {
        this.correntezaHourly = await this.fetchCorrenteza({ tabela: 'hora', limit });
      } catch (err) {
        this.error = String(err?.message || err) || 'Erro ao buscar correnteza horária.';
        this.correntezaHourly = [];
      } finally { this.loading = false; }
    },
  },
});
