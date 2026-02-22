<template>
  <div class="page">
    <h1>📝 Avis utilisateurs</h1>

    <div v-if="loading" class="loading">Chargement des avis...</div>

    <div v-else v-for="r in reviews" :key="r.id" class="card">
      <div class="top">
        <div class="info-block">
          <span class="author">👤 {{ r.user?.name || 'Utilisateur' }}</span>
          <div class="company-tag">
            🏢 Entreprise : <strong>{{ r.subject }}</strong>
          </div>
        </div>
        <span v-if="auth.user?.role === 'user'" class="heart" @click.stop="toggleFav(r.id)">
          {{ isFav(r.id) ? "❤️" : "🤍" }}
        </span>
      </div>

      <p class="content">"{{ r.comment || r.content }}"</p>
      
      <div class="tags">
        <span :class="r.sentiment" class="sentiment-badge">
          {{ translate(r.sentiment) }} ({{ Math.round((r.sentiment_score || 0.5) * 100) }}%)
        </span>
        <span v-for="topic in r.topics" :key="topic" class="tag theme">#{{ topic }}</span>
      </div>

      <div class="actions">
        <button v-if="canEdit(r)" @click.stop="edit(r.id)">✏️ Modifier</button>
        <button v-if="canDelete(r)" @click.stop="remove(r.id)">🗑 Supprimer</button>

        <button 
          v-if="auth.user?.role === 'entreprise' && r.subject === auth.user?.company_name" 
          @click.stop="toggleComment(r.id)"
          class="btn-reply"
        >
          💬 Répondre
        </button>
      </div>

      <div v-if="showComment === r.id" class="comment-box" @click.stop>
        <textarea v-model="commentText" placeholder="Votre réponse officielle..."></textarea>
        <button @click.stop="addComment(r.id)">Envoyer</button>
      </div>

      <div v-if="r.comments && r.comments.length" class="comments">
        <div v-for="c in r.comments" :key="c.id" class="comment">
          <b class="official-label">📢 Réponse de {{ c.user?.company_name || 'L\'équipe' }} :</b> 
          {{ c.content }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"; // J'ai enlevé computed pour simplifier
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import api from "../../services/api";

const router = useRouter();
const auth = useAuthStore();

const reviews = ref([]);
const loading = ref(true);
const showComment = ref(null);
const commentText = ref("");
const favs = ref(JSON.parse(localStorage.getItem('my_favs')) || []);

async function load() {
  loading.value = true;
  try {
    const res = await api.get("/reviews");
    reviews.value = res.data;
  } catch (e) {
    console.error("Erreur API", e);
  } finally {
    loading.value = false;
  }
}

function edit(id) { router.push(`/reviews/edit/${id}`); }
function canEdit(r) { return auth.user?.id === r.user_id; }
function canDelete(r) { return auth.user?.role === 'admin' || auth.user?.id === r.user_id; }

async function remove(id) {
  if (confirm("Supprimer ?")) { 
    await api.delete(`/reviews/${id}`); 
    load(); 
  }
}

function toggleComment(id) {
  showComment.value = showComment.value === id ? null : id;
  commentText.value = "";
}

async function addComment(reviewId) {
  if (!commentText.value) return;
  try {
    await api.post(`/reviews/${reviewId}/comments`, { content: commentText.value });
    await load();
    showComment.value = null;
  } catch (e) { 
    alert("Erreur lors de l'envoi"); 
  }
}

function translate(s) {
  const map = { positive: "Positif", negative: "Négatif", neutral: "Neutre" };
  return map[s] || s;
}

// Favoris simplifiés sans computed
function isFav(id) { return favs.value.includes(id); }
function toggleFav(id) {
  if (favs.value.includes(id)) {
    favs.value = favs.value.filter(f => f !== id);
  } else {
    favs.value.push(id);
  }
  localStorage.setItem('my_favs', JSON.stringify(favs.value));
}

onMounted(load);
</script>

<style scoped>
/* Remplace tes styles de Reviews.vue par ceux-ci */
.card {
  background: white;
  border: none;
  padding: 25px;
  margin-bottom: 25px;
  border-radius: var(--radius);
  box-shadow: var(--card-shadow);
  transition: transform 0.2s ease;
}

.card:hover { transform: translateY(-2px); }

.author { font-weight: 600; font-size: 1.1rem; }
.company-tag { font-size: 0.85rem; color: var(--primary); background: #eff6ff; padding: 4px 10px; border-radius: 20px; }

.content { font-size: 1.05rem; line-height: 1.6; color: #334155; margin: 15px 0; }

.sentiment-badge {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 700;
}

.comment {
  background: #f1f5f9;
  border-left: 4px solid var(--primary);
  padding: 15px;
  border-radius: 0 8px 8px 0;
  margin-top: 15px;
}
</style>