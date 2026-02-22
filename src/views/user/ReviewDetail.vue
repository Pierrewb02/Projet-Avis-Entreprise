<template>
  <div class="page" v-if="review">
    <h1 class="title">📝 Avis détaillé </h1>

    <div class="card-ui detail-card">
      <div class="review-meta-top">
        <span class="author">👤 Posté par : <strong>{{ review.user?.name || 'Cia' }}</strong></span>
        <span class="date">{{ formatDate(review.created_at) }}</span>
      </div>

      <hr>

      <div class="sentiment-box">
        <span :class="['badge', review.sentiment]">
          {{ review.sentiment === 'positive' ? 'Avis positif 😊' : 'Avis négatif 😞 ' }}
        </span>
        <span class="stars">{{ "⭐".repeat(review.stars) }}</span>
      </div>
      
      <p class="full-comment">"{{ review.comment }}"</p>
      
      <div class="analysis-footer">
        <p><strong>Confiance de l'IA :</strong> {{ review.sentiment_score }}%</p>
        <p><strong>Sujet détecté :</strong> {{ review.subject }}</p>
      </div>

      <div class="actions-bottom">
        <button @click="$router.back()" class="btn-back">
          ← Retour à la liste
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../../services/api";

const route = useRoute();
const review = ref(null);

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString("fr-FR", {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

onMounted(async () => {
  try {
    const res = await api.get("/reviews");
    review.value = res.data.find(r => r.id == route.params.id);
  } catch (e) {
    console.error("Erreur détaillée:", e);
  }
});
</script>

<style scoped>
.detail-card {
  max-width: 600px;
  margin: 20px auto;
  padding: 30px;
}

.review-meta-top {
  display: flex;
  justify-content: space-between;
  color: #64748b;
  font-size: 0.95rem;
}

.sentiment-box {
  margin: 20px 0;
  display: flex;
  align-items: center;
  gap: 15px;
}

.full-comment {
  font-size: 1.3rem;
  line-height: 1.6;
  font-style: italic;
  margin: 25px 0;
  color: #1e293b;
}

.analysis-footer {
  background: #f1f5f9;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 30px;
}

.analysis-footer p {
  margin: 8px 0;
}

/* Style du bouton en bas */
.actions-bottom {
  border-top: 1px solid #e2e8f0;
  padding-top: 20px;
  text-align: center;
}

.btn-back {
  background: #2563eb;
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-back:hover {
  background: #1d4ed8;
}
</style>