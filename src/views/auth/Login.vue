<template>

<div class="auth">

  <h2>Connexion</h2>

  <form @submit.prevent="handleLogin">

    <input
      v-model="email"
      type="email"
      placeholder="Email"
      required
    />

    <input
      v-model="password"
      type="password"
      placeholder="Mot de passe"
      required
    />

    <button>Se connecter</button>

  </form>

  <!-- REGISTER LINK -->
  <p class="link">

    Pas encore de compte ?

    <router-link to="/register">
      Créer un compte
    </router-link>

  </p>

</div>

</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";

const email = ref("");
const password = ref("");

const router = useRouter();
const auth = useAuthStore();

async function handleLogin() {
  console.log("1. Début du clic");
  
  try {
    const result = await auth.login({ 
      email: email.value, 
      password: password.value 
    });

    console.log("2. Résultat du store reçu :", result);

    if (result && result.success) {
    alert("Connexion réussie !");
    
    // Ajoute ces logs pour vérifier ce que Vue "voit" au moment de partir
    console.log("Rôle utilisateur :", auth.user?.role);
    
    // On force la redirection
    if (auth.user?.role === "admin") {
        router.push("/admin");
    } else {
        router.push("/");
    }
}
  } catch (err) {
    console.error("3. Erreur critique capturée :", err);
    alert("Erreur réseau ou serveur : " + err.message);
  }
}
</script>

<style scoped>

.auth {
  max-width: 400px;
  margin: 80px auto;
  padding: 20px;

  border: 1px solid #ddd;
  border-radius: 8px;
  text-align: center;
}

input {
  width: 100%;
  margin-bottom: 10px;
  padding: 8px;
}

button {
  width: 100%;
  padding: 8px;

  background: #2563eb;
  color: white;
  border: none;
}

.link {
  margin-top: 15px;
}

.link a {
  color: #2563eb;
  font-weight: bold;
  text-decoration: none;
}

</style>
