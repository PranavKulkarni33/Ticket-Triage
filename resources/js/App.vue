<template>
  <div>
    <div class="header">
      <div class="header__nav">
        <router-link to="/tickets">Tickets</router-link>
        <router-link to="/dashboard">Dashboard</router-link>
      </div>

      <div class="header__right">
        <button class="btn" @click="toggleTheme">
          {{ theme === 'dark' ? 'Light mode' : 'Dark mode' }}
        </button>
      </div>
    </div>

    <router-view />
  </div>
</template>

<script>
export default {
  data(){ return { theme:'light' } },
  mounted(){
    // load saved preference or OS preference
    const saved = localStorage.getItem('theme')
    if (saved) this.theme = saved
    else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) this.theme = 'dark'
    this.applyTheme()
  },
  methods:{
    toggleTheme(){
      this.theme = this.theme === 'dark' ? 'light' : 'dark'
      localStorage.setItem('theme', this.theme)
      this.applyTheme()
    },
    applyTheme(){
      document.documentElement.classList.toggle('dark', this.theme === 'dark')
    }
  }
}
</script>
