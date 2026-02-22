<template>
  <div class="page">
    <h1 class="title">Modifier l'avis</h1>
    <div v-if="loading">Chargement...</div>
    <div v-else class="card-ui form-box">
      <form @submit.prevent="update">
        <textarea v-model="form.comment" class="input-ui" rows="5"></textarea>
        <button class="btn w-full">Enregistrer les modifications</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../../services/api";

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const form = reactive({ comment: "" });

onMounted(async () => {
  const res = await api.get(`/reviews`);
  const review = res.data.find(r => r.id == route.params.id);
  if (review) form.comment = review.comment;
  loading.value = false;
});

async function update() {
  try {
    loading.value = true;
    
    // 1. Envoie la modification au Backend via la route PUT
    await api.put(`/reviews/${route.params.id}`, {
      comment: form.comment,
      // On rajoute ces champs si ton Backend les attend
      subject: form.subject || "Sujet", 
      stars: form.stars || 5
    });

    // 2. Redirige si tout s'est bien passé
    router.push("/reviews");
  } catch (error) {
    console.error("Erreur lors de la mise à jour :", error);
    alert("Impossible de modifier l'avis. Vérifiez votre connexion.");
  } finally {
    loading.value = false;
  }
}
</script>