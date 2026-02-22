<template>
  <div class="page">
    <div class="form-container card-ui">
      <header class="form-header">
        <h1>Partagez votre avis</h1>
        <p>Votre expérience aide les entreprises à s'améliorer grâce à notre IA.</p>
      </header>

      <form @submit.prevent="submit" class="styled-form">
        <div class="form-group">
          <label>Entreprise concernée</label>
          <input 
            v-model="form.subject" 
            type="text" 
            placeholder="Ex: Netflix, Tesla, Apple..." 
            class="input-pro"
            required
          />
        </div>

        <div class="form-group">
          <label>Votre note</label>
          <div class="star-rating">
            <span 
              v-for="star in 5" 
              :key="star" 
              class="star" 
              :class="{ filled: star <= form.stars }"
              @click="form.stars = star"
            >
              ★
            </span>
          </div>
        </div>

        <div class="form-group">
          <label>Votre commentaire</label>
          <textarea 
            v-model="form.comment" 
            placeholder="Racontez-nous votre expérience en détails..." 
            class="input-pro textarea-pro"
            required
          ></textarea>
        </div>

        <button type="submit" class="btn-submit" :disabled="loading">
          {{ loading ? 'Analyse en cours...' : 'Publier et Analyser' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';

const router = useRouter();
const loading = ref(false);

const form = ref({
  subject: '',
  stars: 5,
  comment: ''
});

async function submit() {
  loading.value = true;
  try {
    await api.post('/reviews', form.value);
    router.push('/reviews');
  } catch (e) {
    alert("Erreur lors de la publication.");
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.form-container {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  padding: 40px;
  border-radius: var(--radius);
  box-shadow: var(--card-shadow);
}

.form-header { text-align: center; margin-bottom: 30px; }
.form-header h1 { color: var(--primary); margin-bottom: 10px; }
.form-header p { color: var(--text-muted); font-size: 0.95rem; }

.styled-form { display: flex; flex-direction: column; gap: 25px; }

.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label { font-weight: 600; font-size: 0.9rem; color: var(--text-main); }

.input-pro {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.input-pro:focus {
  outline: none;
  border-color: var(--primary);
}

.textarea-pro { height: 150px; resize: vertical; }

/* Étoiles interactives */
.star-rating { display: flex; gap: 5px; font-size: 2.5rem; cursor: pointer; }
.star { 
  color: #cbd5e1; 
  transition: all 0.2s ease; 
}
.star:hover { 
  transform: scale(1.2); 
  color: #fbbf24;
}
.star.filled { color: #f59e0b; }

/* Correction du bouton "Invisible" */
.btn-submit {
  background: var(--primary);
  color: white !important; /* On force le blanc */
  border: none;
  padding: 16px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 10px;
  width: 100%;
}

.btn-submit:hover { 
  background: var(--primary-hover, #1d4ed8); 
  color: white !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-submit:disabled { 
  background: #94a3b8; 
  cursor: not-allowed; 
  transform: none;
  box-shadow: none;
}
</style>