// src/stores/weather.js
import { defineStore } from 'pinia';

export const useWeatherStore = defineStore('weather', {
  state: () => ({
    weatherLast: null,
    weatherHistory: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchLast() {
      this.loading = true;
      this.error = null;
      try {
        const res = await fetch('/kevi/backend/praticagem/get_table_mestre_5min_tratada_bq.php?limit=1');
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
        const res = await fetch('/kevi/backend/praticagem/get_table_mestre_5min_tratada_bq.php?limit=100');
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
    // Pode adicionar outras actions se precisar...
  },
});
