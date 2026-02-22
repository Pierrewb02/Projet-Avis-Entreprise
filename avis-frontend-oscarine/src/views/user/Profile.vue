<template>
  <div class="page">
    <h1 class="title">Mon Profil</h1>
    
    <div v-if="user" class="card-ui profile-card">
      <div class="profile-header">
        <div class="avatar">{{ user.name.charAt(0) }}</div>
        <h2>{{ user.name }}</h2>
      </div>
      <div class="profile-info">
        <p><strong>Email :</strong> {{ user.email }}</p>
        <p><strong>Statut :</strong> {{ user.role === 'admin' ? '🛡️ Administrateur' : '👤 Utilisateur' }}</p>
      </div>
      <button @click="logout" class="btn danger">Se déconnecter</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../../services/api";
import { useAuthStore } from "../../stores/auth";

const user = ref(null);
const router = useRouter();
const auth = useAuthStore();

onMounted(async () => {
  try {
    const res = await api.get("/user"); //
    user.value = res.data;
  } catch (e) {
    router.push("/login");
  }
});

const logout = async () => {
  await api.post("/logout");
  auth.token = null;
  localStorage.removeItem("token");
  router.push("/login");
};
</script>

<style scoped>
.profile-card { max-width: 500px; margin: auto; text-align: center; padding: 40px; }
.avatar { width: 80px; height: 80px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; }
.profile-info { margin: 20px 0; text-align: left; }
.danger { background: #dc2626; margin-top: 20px; }
</style>