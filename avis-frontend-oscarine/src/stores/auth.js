import { defineStore } from "pinia";
import api from "../services/api"; // On utilise ton fichier api.js que tu viens de me montrer

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
  }),

  getters: {
    // AJOUTE CECI :
    isLogged: (state) => !!state.token,
  },


  actions: {
    async login(credentials) {
      try {
        // 1. On envoie l'email et le password à Laravel
        const response = await api.post("/login", credentials);
        
        // 2. On récupère les données
        this.user = response.data.user;
        this.token = response.data.token;

        // 3. On sauvegarde dans le localStorage pour que api.js 
        // puisse lire le token au prochain rafraîchissement
        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user));

        return { success: true };
      } catch (error) {
        console.error("Erreur login:", error.response?.data?.message);
        return { 
          success: false, 
          message: error.response?.data?.message || "Erreur de connexion" 
        };
      }
    },

    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");
      localStorage.removeItem("user");
    },
  },
});