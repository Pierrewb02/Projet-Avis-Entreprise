<template>
  <header class="nav">

    <div class="nav-left">
      ⭐ AvisPro
    </div>

    <nav class="nav-links">

      <RouterLink to="/">Accueil</RouterLink>

      <RouterLink to="/reviews">Avis</RouterLink>

      <RouterLink to="/add">Ajouter</RouterLink>

      <RouterLink to="/favorites">Favoris</RouterLink>

      <RouterLink
        v-if="isAdmin"
        to="/admin"
      >
        Admin
      </RouterLink>

    </nav>

    <div class="nav-right">

      <span class="user">
        👤 {{ user.name }}
      </span>

      <button
        class="btn logout"
        @click="logout"
      >
        Déconnexion
      </button>

    </div>

  </header>
</template>

<script setup>

import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";
import { computed } from "vue";

const auth = useAuthStore();
const router = useRouter();

const user = auth.user;

const isAdmin = computed(() =>
  user?.role === "admin"
);

function logout() {

  auth.logout();
  router.push("/login");

}

</script>

<style scoped>

.nav {
  height: 60px;
  background: white;
  border-bottom: 1px solid var(--border);

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 20px;

  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-left {
  font-size: 20px;
  font-weight: 600;
  color: var(--primary);
}

.nav-links a {
  margin: 0 10px;
  text-decoration: none;
  color: var(--text);
  font-weight: 500;
}

.nav-links a.router-link-active {
  color: var(--primary);
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user {
  color: var(--muted);
}

.logout {
  background: #ef4444;
}

.logout:hover {
  background: #dc2626;
}

</style>
