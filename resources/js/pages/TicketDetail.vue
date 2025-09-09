<template>
  <div style="padding:16px;">
    <div class="header"><div class="header__nav">
      <router-link to="/tickets">← Back</router-link>
    </div></div>

    <div v-if="t">
      <h2>{{ t.subject }}</h2>
      <p style="white-space:pre-wrap">{{ t.body }}</p>

      <div style="display:flex;gap:10px;align-items:center;margin:10px 0;">
        <label>Category:</label>
        <select class="input" v-model="editCategory" @change="savePatch">
          <option value="">—</option>
          <option>billing</option><option>technical</option><option>account</option><option>general</option>
        </select>

        <label>Status:</label>
        <select class="input" v-model="editStatus" @change="savePatch">
          <option>open</option><option>in_progress</option><option>closed</option>
        </select>
      </div>

      <div>
        <label>Internal note</label>
        <textarea class="input" rows="4" style="width:100%;" v-model="editNote" @blur="savePatch"></textarea>
      </div>

      <div style="margin-top:10px;font-size:14px;">
        <div>Confidence: <strong>{{ t.confidence?.toFixed?.(2) ?? '—' }}</strong></div>
        <div>Explanation: <em>{{ t.explanation || '—' }}</em></div>
      </div>

      <div style="margin-top:12px;">
        <button class="btn" @click="runClassify" :disabled="loading">
          <span v-if="loading">Classifying…</span><span v-else>Run Classification</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data(){ return { t:null, editCategory:'', editNote:'', editStatus:'open', loading:false } },
  methods:{
    async load(){
      const id=this.$route.params.id
      const r=await axios.get(`/api/tickets/${id}`)
      this.t=r.data
      this.editCategory=this.t.category||''
      this.editNote=this.t.note||''
      this.editStatus=this.t.status
    },
    async savePatch(){
      const id=this.$route.params.id
      await axios.patch(`/api/tickets/${id}`, {
        category:this.editCategory||null, note:this.editNote||null, status:this.editStatus
      })
      await this.load()
    },
    async runClassify(){
      const id=this.$route.params.id
      this.loading=true
      try { await axios.post(`/api/tickets/${id}/classify`); setTimeout(()=>this.load(),600) }
      finally { this.loading=false }
    }
  },
  mounted(){ this.load() }
}
</script>
