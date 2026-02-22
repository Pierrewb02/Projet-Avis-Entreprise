<template>
  <div class="auth">
    <h2>Créer un compte</h2>

    <form @submit.prevent="handleRegister">
      <input v-model="name" type="text" placeholder="Nom" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Mot de passe" required />

      <label>Type de compte :</label>
      <select v-model="role">
        <option value="user">Utilisateur (Particulier)</option>
        <option value="entreprise">Entreprise (Professionnel)</option>
      </select>

      <div v-if="role === 'entreprise'" class="company-field">
        <input 
          v-model="company_name" 
          type="text" 
          placeholder="Nom de l'entreprise (ex: Netflix)" 
          required 
        />
        <small>Les avis concernant ce nom s'afficheront sur votre dashboard.</small>
      </div>

      <button :disabled="loading">
        {{ loading ? "Création..." : "Créer" }}
      </button>
    </form>

    <p>
      Déjà un compte ?
      <router-link to="/login">Connexion</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../../services/api";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const role = ref("user");
const company_name = ref(""); // On ajoute la référence pour le nom d'entreprise
const loading = ref(false);

async function handleRegister() {
  loading.value = true;
  try {
    const response = await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      role: role.value,
      // On envoie le nom d'entreprise seulement si c'est une entreprise
      company_name: role.value === 'entreprise' ? company_name.value : null
    });

    alert("Compte " + role.value + " créé avec succès !");
    router.push("/login");
  } catch (error) {
    console.error("Erreur backend :", error.response?.data);
    alert(error.response?.data?.error || "Erreur lors de l'inscription");
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth { max-width: 400px; margin: 80px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
input, select { width: 100%; margin-bottom: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem; }
.company-field { margin-bottom: 15px; padding: 10px; background: #f0f7ff; border-radius: 4px; }
small { color: #2563eb; font-size: 0.75rem; }
button { width: 100%; padding: 10px; background: #2563eb; color: white; border: none; cursor: pointer; border-radius: 4px; }
button:disabled { background: #94a3b8; }
</style>