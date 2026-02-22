<template>
  <div id="app-container">
    <nav v-if="showNavbar" class="nav">
      <div class="nav-left">
        <h2 class="logo">AvisPro</h2>
        <div class="links">
          <router-link to="/">Accueil</router-link>
          <router-link to="/reviews">Avis</router-link>
          <router-link v-if="isUser" to="/add">Ajouter</router-link>
          <router-link v-if="isUser" to="/favorites">Favoris</router-link>
          <router-link v-if="isAdmin" to="/admin">Admin</router-link>
        </div>
      </div>

      <div v-if="isLogged" class="nav-right">
        <div class="account-wrapper">
          <div class="avatar-sm" @click.stop="toggleMenu">
            {{ auth.user?.name?.charAt(0).toUpperCase() }}
          </div>

          <Transition name="fade">
            <div v-if="isMenuOpen" class="google-menu" @click.stop>
              <div class="menu-inner">
                <div class="menu-header">
                  <div class="avatar-lg">
                    {{ auth.user?.name?.charAt(0).toUpperCase() }}
                  </div>
                  <div class="user-info">
                    <span class="user-name">{{ auth.user?.name }}</span>
                    <span class="user-email">{{ auth.user?.email }}</span>
                    <span :class="['role-badge', auth.user?.role]">
                      {{ auth.user?.role === 'entreprise' ? auth.user?.company_name : auth.user?.role }}
                    </span>
                  </div>
                </div>
                
                <div class="menu-footer">
                  <button class="btn-logout" @click="logout">
                    <span class="icon">🚪</span> Se déconnecter
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </nav>

    <main class="main-content" @click="isMenuOpen = false">
  <router-view />
</main>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "./stores/auth";
import { useRouter, useRoute } from "vue-router";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const isLogged = computed(() => auth.isLogged);
const isAdmin = computed(() => auth.user?.role === "admin");
const isUser = computed(() => auth.user?.role === "user");
const showNavbar = computed(() => !route.meta.guest);

const isMenuOpen = ref(false);
function toggleMenu() { isMenuOpen.value = !isMenuOpen.value; }

function logout() {
  auth.logout();
  isMenuOpen.value = false;
  router.push("/login");
}
</script>

<style scoped>
/* NAV STYLE */
.nav {
  display: flex; justify-content: space-between; align-items: center;
  background: white; padding: 10px 30px; border-bottom: 1px solid #e5e7eb;
  position: sticky; top: 0; z-index: 100;
}
.logo { color: #2563eb; font-weight: 800; margin: 0; }
.links a { text-decoration: none; color: #4b5563; margin-right: 20px; font-weight: 500; font-size: 0.9rem; }
.links a.router-link-active { color: #2563eb; font-weight: 700; }

/* AVATAR NAV */
.avatar-sm {
  width: 36px; height: 36px; background: #7c3aed; color: white;
  border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-weight: 700; cursor: pointer; transition: 0.2s;
}
.avatar-sm:hover { box-shadow: 0 0 0 4px #ddd6fe; }

/* MENU POPOVER */
.account-wrapper { position: relative; }

.google-menu {
  position: absolute; right: 0; top: 50px; width: 300px;
  background: #f0f4f9; border-radius: 24px; padding: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #d1d5db;
}

.menu-inner { background: white; border-radius: 20px; overflow: hidden; }

.menu-header {
  padding: 24px; display: flex; flex-direction: column; align-items: center;
  border-bottom: 1px solid #f3f4f6; text-align: center;
}

.avatar-lg {
  width: 72px; height: 72px; background: #7c3aed; color: white;
  font-size: 2rem; border-radius: 50%; display: flex;
  align-items: center; justify-content: center; margin-bottom: 12px;
}

.user-name { display: block; font-size: 1.1rem; font-weight: 600; color: #111827; }
.user-email { display: block; font-size: 0.85rem; color: #6b7280; margin-bottom: 10px; }

.role-badge {
  font-size: 0.65rem; text-transform: uppercase; font-weight: 800;
  padding: 4px 12px; border-radius: 12px; background: #f3f4f6; color: #374151;
}

.menu-footer { padding: 16px; display: flex; justify-content: center; }

.btn-logout {
  background: white; border: 1px solid #d1d5db; color: #374151;
  padding: 10px 24px; border-radius: 100px; font-size: 0.9rem; font-weight: 500;
  cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px;
}
.btn-logout:hover { background: #f9fafb; border-color: #9ca3af; }

/* ANIMATION */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-10px); }
</style>