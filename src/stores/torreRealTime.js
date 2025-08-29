import { defineStore } from 'pinia'
import axios from 'axios'

function gerarIntervaloTempoFormatado() {
  const now = new Date()
  const fim = now
  const inicio = new Date(now.getTime() - 60 * 60 * 1000)

  const pad = (n) => String(n).padStart(2, '0')
  const formatar = (date) => `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`

  return {
    de: encodeURIComponent(formatar(inicio)),
    ate: encodeURIComponent(formatar(fim))
  }
}

export const useTorreRealTimeStore = defineStore('torreRealTime', {
  state: () => ({
    controls: null,
    mare: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchDadosEmTempoReal() {
      const { de, ate } = gerarIntervaloTempoFormatado()
      this.loading = true
      this.error = null

      try {
        const [resControls, resMare] = await Promise.all([
          axios.get(`https://torre.rgpilots.com.br/svc/controls/?de=${de}&ate=${ate}&key=YnJwaWxvdHM6dG91Y2g0QUxM&action=getControls`),
          axios.get(`https://torre.rgpilots.com.br/svc/controls/?de=${de}&ate=${ate}&key=YnJwaWxvdHM6dG91Y2g0QUxM&action=getMare`)
        ])

        this.controls = resControls?.data?.resposta?.[0] || null
        this.mare = resMare?.data?.resposta?.[0] || null

      } catch (err) {
        console.error('[torreRealTime] Falha ao buscar dados:', err)
        this.error = 'Erro ao buscar dados em tempo real.'
        this.controls = null
        this.mare = null
      } finally {
        this.loading = false
      }
    }
  }
})
