<template>
  <div class="page">
    <h1 class="title">Console d'Administration</h1>
    
    <div v-if="loading" class="loading">Chargement de la base de données globale...</div>

    <div v-else>
      <div class="card-ui table-container">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Utilisateur</th>
              <th>Avis</th>
              <th>Sentiment</th>
              <th>Score IA</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="review in allReviews" :key="review.id">
              <td>
                <strong>{{ review.user?.name }}</strong><br>
                <small>{{ review.user?.email }}</small>
              </td>
              <td class="comment-cell">"{{ review.comment }}"</td>
              <td>
                <span :class="['badge', review.sentiment]">
                  {{ review.sentiment }}
                </span>
              </td>
              <td>{{ review.sentiment_score }}%</td>
              <td>
                <button @click="deleteReview(review.id)" class="btn-delete">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../services/api";

const allReviews = ref([]);
const loading = ref(true);

const fetchAll = async () => {
  try {
    // Comme l'utilisateur est admin, le contrôleur Laravel 
    // renverra tous les avis (grâce à la modif qu'on a faite avant)
    const res = await api.get("/reviews");
    allReviews.value = res.data;
  } catch (e) {
    console.error("Erreur admin:", e);
  } finally {
    loading.value = false;
  }
};

const deleteReview = async (id) => {
  if (confirm("Supprimer définitivement cet avis ?")) {
    try {
      await api.delete(`/reviews/${id}`);
      allReviews.value = allReviews.value.filter(r => r.id !== id);
    } catch (e) {
      alert("Erreur lors de la suppression");
    }
  }
};

onMounted(fetchAll);
</script>

<style scoped>
.admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
.comment-cell { max-width: 300px; font-style: italic; font-size: 0.9rem; }
.btn-delete { background: #fee2e2; color: #dc2626; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
.btn-delete:hover { background: #fecaca; }
</style>