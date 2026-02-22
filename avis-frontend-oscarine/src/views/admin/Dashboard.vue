<template>
  <div class="admin-dashboard">
    <h1 class="title">Analyse Automatique & Modération</h1>

    <div class="stats-container" v-if="stats">
      <div class="stats-cards">
        <div class="stat-card">Total: {{ stats.total }}</div>
        <div class="stat-card positive">Positifs: {{ stats.positive }}</div>
        <div class="stat-card negative">Négatifs: {{ stats.negative }}</div>
      </div>
      
      <div class="chart-box">
        <Pie v-if="chartData" :data="chartData" :options="chartOptions" />
      </div>
    </div>

    <div class="card-ui table-box">
      <table>
        <thead>
          <tr>
            <th>Avis client</th>
            <th>Sentiment IA</th>
            <th>Note</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in reviews" :key="r.id">
            <td class="comment-cell">"{{ r.comment.substring(0, 50) }}..."</td>
            <td>
              <span :class="['badge', r.sentiment]">
                {{ r.sentiment === 'positive' ? '😊' : r.sentiment === 'negative' ? '😞' : '😐' }} 
                {{ r.sentiment }}
              </span>
            </td>
            <td>{{ r.stars }}/5</td>
            <td>
              <button class="btn danger" @click="remove(r.id)">Supprimer</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../services/api";
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const reviews = ref([]);
const stats = ref(null);
const chartData = ref(null);
const chartOptions = { responsive: true, maintainAspectRatio: false };

const loadData = async () => {
  try {
    // 1. Récupérer les avis pour le tableau
    const resReviews = await api.get("/reviews");
    reviews.value = resReviews.data;

    // 2. Récupérer les stats pour le graphique
    const resStats = await api.get("/reviews/stats");
    stats.value = resStats.data;

    // Configuration du graphique
    chartData.value = {
      labels: ['Positifs', 'Négatifs', 'Neutres'],
      datasets: [{
        backgroundColor: ['#22c55e', '#ef4444', '#94a3b8'],
        data: [stats.value.positive, stats.value.negative, stats.value.neutral]
      }]
    };
  } catch (error) {
    console.error("Erreur de chargement", error);
  }
};

const remove = async (id) => {
  if (!confirm("Supprimer cet avis ?")) return;
  try {
    await api.delete(`/reviews/${id}`);
    reviews.value = reviews.value.filter(r => r.id !== id);
    loadData(); // On recharge les stats pour mettre à jour le graphique !
  } catch (e) { alert("Erreur lors de la suppression"); }
};

onMounted(loadData);
</script>

<style scoped>
.stats-container { display: flex; gap: 20px; margin-bottom: 30px; align-items: center; }
.stats-cards { flex: 1; display: grid; gap: 10px; }
.stat-card { padding: 15px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: bold; }
.positive { color: #166534; border-left: 5px solid #22c55e; }
.negative { color: #991b1b; border-left: 5px solid #ef4444; }
.chart-box { flex: 1; height: 200px; background: white; padding: 10px; border-radius: 8px; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
.comment-cell { font-style: italic; color: #555; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
.positive { background: #dcfce7; color: #166534; }
.negative { background: #fee2e2; color: #991b1b; }
.btn.danger { background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
</style>