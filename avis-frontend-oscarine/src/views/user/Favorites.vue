<template>
  <div class="page">
    <h1 class="title">❤️ Le Top des Avis</h1>
    <p>Ces avis ont été automatiquement sélectionnés pour leur sentiment positif.</p>

    <div v-if="loading" class="loading">Filtrage des meilleurs avis...</div>

    <div v-else class="reviews-grid">
      <div v-for="r in favReviews" :key="r.id" class="card-ui review-card fav">
        <div class="review-header">
          <span class="subject">{{ r.subject }}</span>
          <span class="badge positive">😊 </span>
        </div>
        <p class="comment">"{{ r.comment }}"</p>
        <div class="review-footer">
          <small>Analysé avec un score de {{ r.sentiment_score * 100}}%</small>
        </div>
      </div>
    </div>

    <p v-if="!loading && !favReviews.length" class="empty">Aucun avis "Top" détecté pour le moment.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "../../services/api";

const allReviews = ref([]);
const loading = ref(true);

// Filtrage automatique basé sur l'analyse de sentiment de Laravel
const favReviews = computed(() => {
  return allReviews.value.filter(r => r.sentiment === 'positive');
});

onMounted(async () => {
  try {
    const res = await api.get("/reviews"); //
    allReviews.value = res.data;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.fav { border-left: 6px solid #22c55e; background: #f0fdf4; }
.empty { text-align: center; margin-top: 40px; color: #666; }
</style>