<template>
  <div class="page">
    <div class="header-actions">
      <h1 class="title">Analyses des avis clients</h1>
      <router-link to="/add-review" class="btn">Ajouter un avis</router-link>
    </div>

    <div v-if="loading" class="loading">Chargement des analyses...</div>

    <div v-else class="reviews-grid">
      <div v-for="review in reviews" :key="review.id" class="card-ui review-card">
        <div class="review-header">
          <span class="subject">{{ review.subject }}</span>
          <span :class="['badge', review.sentiment]">
            {{ formatSentiment(review.sentiment) }}
          </span>
        </div>

        <p class="comment">"{{ review.comment }}"</p>

        <div class="review-footer">
          <span class="stars">{{ "⭐".repeat(review.stars) }}</span>
          <span class="date">{{ formatDate(review.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../services/api";

const reviews = ref([]);
const loading = ref(true);

const fetchReviews = async () => {
  try {
    const response = await api.get("/reviews");
    reviews.value = response.data;
  } catch (error) {
    console.error("Erreur de chargement:", error);
  } finally {
    loading.value = false;
  }
};

const formatSentiment = (s) => {
  const labels = { positive: "😊 Positif", negative: "😞 Négatif", neutral: "😐 Neutre" };
  return labels[s] || "Non analysé";
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("fr-FR");
};

onMounted(fetchReviews);
</script>

<style scoped>
.reviews-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}
.review-card {
  padding: 15px;
  border-left: 5px solid #ddd;
}
.review-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
.subject { font-weight: bold; color: #1e293b; }
.comment { font-style: italic; color: #475569; margin: 10px 0; }

/* Couleurs des badges d'analyse */
.badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
.positive { background: #dcfce7; color: #166534; border-color: #166534; }
.negative { background: #fee2e2; color: #991b1b; border-color: #991b1b; }
.neutral { background: #f1f5f9; color: #475569; }

.review-card:has(.positive) { border-left-color: #22c55e; }
.review-card:has(.negative) { border-left-color: #ef4444; }
</style>