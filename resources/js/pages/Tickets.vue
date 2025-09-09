<template>
  <div class="ticket-list">
    

    <div class="ticket-list__controls">
      <input class="input" v-model="search" placeholder="Search subject/body..." />
      <select class="input" v-model="filterStatus">
        <option value="">All statuses</option>
        <option>open</option><option>in_progress</option><option>closed</option>
      </select>
      <select class="input" v-model="filterCategory">
        <option value="">All categories</option>
        <option>billing</option><option>technical</option><option>account</option><option>general</option>
      </select>
      <button class="btn" @click="exportCsv">Export CSV</button>
      <button class="btn btn--primary" @click="showNew = true">New Ticket</button>
    </div>

    <div v-for="t in pagedTickets" :key="t.id"
         :class="['ticket-list__item', {'ticket-list__item--has-note': !!t.note}]">
      <h3 style="margin:0 0 6px 0;">
        <router-link :to="`/tickets/${t.id}`">{{ t.subject }}</router-link>
      </h3>
      <div style="font-size:13px;margin-bottom:6px;">Status: {{ t.status }}</div>
      <div style="display:flex;gap:8px;align-items:center;">
        <span>Category: <strong>{{ t.category || '—' }}</strong></span>
        <span>Conf: {{ t.confidence?.toFixed?.(2) ?? '—' }}</span>
        <span class="badge" v-if="t.explanation" :title="t.explanation">?</span>
        <span class="badge badge--note" v-if="t.note">note</span>
        <button class="btn" @click="classify(t)" :disabled="loadingId===t.id">
          <span v-if="loadingId===t.id">Classifying…</span><span v-else>Classify</span>
        </button>
      </div>
    </div>

    <div style="display:flex;gap:8px;align-items:center;margin-top:10px;">
      <button class="btn" @click="prev" :disabled="page===1">Prev</button>
      <div>Page {{ page }}</div>
      <button class="btn" @click="next" :disabled="endIndex>=filtered.length">Next</button>
    </div>

    <div v-if="showNew" style="position:fixed;inset:0;background:#0005;display:flex;align-items:center;justify-content:center;">
      <div style="background:#fff;padding:16px;border-radius:8px;min-width:360px;">
        <h3>New Ticket</h3>
        <input class="input" v-model="newSubject" placeholder="Subject" style="width:100%;margin-bottom:8px;">
        <textarea class="input" v-model="newBody" placeholder="Body" rows="5" style="width:100%;"></textarea>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:10px;">
          <button class="btn" @click="showNew=false">Cancel</button>
          <button class="btn btn--primary" @click="createTicket">Create</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data() {
    return { all: [], search:'', filterStatus:'', filterCategory:'', showNew:false,
             newSubject:'', newBody:'', loadingId:null, page:1, pageSize:8 }
  },
  computed:{
    filtered(){
      return this.all.filter(t=>{
        const s = this.search.toLowerCase()
        const matchText = (t.subject+' '+t.body).toLowerCase().includes(s)
        const matchStatus = this.filterStatus ? t.status===this.filterStatus : true
        const matchCat = this.filterCategory ? t.category===this.filterCategory : true
        return matchText && matchStatus && matchCat
      })
    },
    startIndex(){ return (this.page-1)*this.pageSize },
    endIndex(){ return this.startIndex + this.pageSize },
    pagedTickets(){ return this.filtered.slice(this.startIndex, this.endIndex) }
  },
  methods:{
    async load(){ const r = await axios.get('/api/tickets?page=1&per_page=100'); this.all = r.data.data || r.data },
    async createTicket(){
      if(!this.newSubject || !this.newBody) return
      await axios.post('/api/tickets', {subject:this.newSubject, body:this.newBody})
      this.showNew=false; this.newSubject=''; this.newBody=''; await this.load()
    },
    async classify(t){
  try {
    this.loadingId = t.id
    await axios.post(`/api/tickets/${t.id}/classify`)
    await new Promise(r => setTimeout(r, 900))
    await this.load()
  } catch (e) {
    console.error('Classify failed:', e?.response?.data || e.message)
    alert('Classify failed. Check queue worker terminal for errors.')
  } finally {
    this.loadingId = null
  }
}
,
    exportCsv(){
      const rows=[['id','subject','status','category','confidence']]
      this.filtered.forEach(t=>rows.push([t.id,t.subject,t.status,t.category||'',t.confidence??'']))
      const csv = rows.map(r=>r.map(x=>`"${String(x).replaceAll('"','""')}"`).join(',')).join('\n')
      const blob = new Blob([csv], {type:'text/csv'})
      const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='tickets.csv'; a.click()
    },
    next(){ if(this.endIndex<this.filtered.length) this.page++ },
    prev(){ if(this.page>1) this.page-- }
  },
  mounted(){ this.load() }
}
</script>
