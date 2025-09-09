<template>
  <div style="padding:16px;">
    <div style="display:flex;gap:8px;margin:12px 0;">
      <div>Open: {{ stats.status?.open ?? 0 }}</div>
      <div>In Progress: {{ stats.status?.in_progress ?? 0 }}</div>
      <div>Closed: {{ stats.status?.closed ?? 0 }}</div>
    </div>
    <div ref="chartBox" class="chart-box"></div>
  </div>
</template>

<script>
import axios from 'axios'
import { Chart } from 'chart.js/auto'

export default {
  data(){ return { stats:{}, chart:null } },
  async mounted(){
    const r = await axios.get('/api/stats')
    this.stats = r.data
    this.draw()
  },
  beforeUnmount(){ if (this.chart) this.chart.destroy() },
  methods:{
    draw(){
      this.$refs.chartBox.innerHTML = ''
      const canvas = document.createElement('canvas')
      this.$refs.chartBox.appendChild(canvas)
      if (this.chart) this.chart.destroy()

      const labels = Object.keys(this.stats.category || {})
      const data   = Object.values(this.stats.category || {})

      this.chart = new Chart(canvas, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Tickets per category', data }] },
        options: { responsive:true, maintainAspectRatio:false, animation:false }
      })
    }
  }
}
</script>
