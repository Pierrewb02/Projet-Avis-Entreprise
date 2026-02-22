<template>
  <div class="page">
    <header class="dashboard-header">
      <div>
        <h1 class="title">Tableau de Bord</h1>
        <p class="subtitle">Analyse pour <strong>{{ auth.user?.company_name || auth.user?.name || 'Utilisateur' }}</strong></p>
      </div>
      
      <div v-if="auth.user?.role === 'admin'" class="search-box">
        <input v-model="searchQuery" @input="filterStats" type="text" placeholder="Rechercher une entreprise..." class="input-pro" />
      </div>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-icon">📊</span>
        <div>
          <h3 class="stat-value">{{ stats.total || 0 }}</h3>
          <p class="stat-label">Total Avis</p>
        </div>
      </div>
      
      <div class="stat-card">
        <span class="stat-icon text-success">😊</span>
        <div>
          <h3 class="stat-value">{{ stats.positive || 0 }}</h3>
          <p class="stat-label">Positifs</p>
        </div>
      </div>

      <div class="stat-card">
        <span class="stat-icon text-danger">😞</span>
        <div>
          <h3 class="stat-value">{{ stats.negative || 0 }}</h3>
          <p class="stat-label">Négatifs</p>
        </div>
      </div>
    </div>

    <div class="chart-container card-ui">
      <h3>Indice de Satisfaction Global</h3>
      <div class="satisfaction-bar">
        <div class="fill" :style="{ width: ((stats.avg_score || 0) * 100) + '%' }"></div>
      </div>
      <div class="score-display">
        <strong>{{ Math.round((stats.avg_score || 0) * 100) }}%</strong> de satisfaction moyenne
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import api from "../../services/api";

const auth = useAuthStore();
const searchQuery = ref("");
const stats = ref({
  total: 0,
  positive: 0,
  negative: 0,
  avg_score: 0
});

async function loadStats() {
  try {
    const params = searchQuery.value ? { subject: searchQuery.value } : {};
    const response = await api.get("/stats", { params }); 
    stats.value = response.data;
  } catch (error) {
    console.error(error);
  }
}

onMounted(loadStats);
</script>

<style scoped>
/* Variables locales au cas où elles ne seraient pas dans App.vue */
.page { max-width: 1000px; margin: auto; padding: 20px; }
.dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.title { font-size: 2rem; color: #1e293b; margin: 0; }
.subtitle { color: #64748b; margin-top: 5px; }

.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px; }

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 20px;
}

.stat-icon { font-size: 2.5rem; }
.stat-value { font-size: 2rem; font-weight: 800; margin: 0; color: #2563eb; }
.stat-label { color: #64748b; font-weight: 500; margin: 0; }

.chart-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.satisfaction-bar { height: 16px; background: #f1f5f9; border-radius: 50px; margin: 25px 0; overflow: hidden; border: 1px solid #e2e8f0; }
.fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); transition: width 1s cubic-bezier(0.4, 0, 0.2, 1); }

.score-display { text-align: center; color: #334155; font-size: 1.1rem; }
.input-pro { padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; width: 250px; }
.text-success { color: #10b981; }
.text-danger { color: #ef4444; }
</style>