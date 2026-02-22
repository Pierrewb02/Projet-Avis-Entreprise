import { createRouter, createWebHistory } from "vue-router";

import Login from "../views/auth/Login.vue";
import Register from "../views/auth/Register.vue";

import Home from "../views/user/Home.vue";
import Reviews from "../views/user/Reviews.vue";
import AddReview from "../views/user/AddReview.vue";
import Profile from "../views/user/Profile.vue";
import Favorites from "../views/user/Favorites.vue";
import ReviewDetail from "../views/user/ReviewDetail.vue";
import Admin from "../views/admin/Admin.vue";

import { useAuthStore } from "../stores/auth";

const routes = [

 {
  path: "/login",
  component: Login,
  meta: { guest: true }
},

{
  path: "/register",
  component: Register,
  meta: { guest: true }
},

  {
    path: "/",
    component: Home,
    meta: { auth: true }
  },

  {
    path: "/reviews",
    component: Reviews,
    meta: { auth: true }
  },

  {
  path: "/reviews/:id",
  component: () =>
    import("../views/user/ReviewDetail.vue"),
  meta: { auth: true }
  },

  {
  path: "/reviews/edit/:id",
  component: () =>
    import("../views/user/EditReview.vue"),
  meta: { auth: true }
 },


  {
    path: "/add",
    component: AddReview,
    meta: { auth: true }
  },

  {
    path: "/profile",
    component: Profile,
    meta: { auth: true }
  },

  {
    path: "/favorites",
    component: Favorites,
    meta: { auth: true }
  },

  {
    path: "/admin",
    component: Admin,
    meta: { auth: true, admin: true }
  },

  {
    path: "/:pathMatch(.*)*",
    redirect: "/"
  }

];



const router = createRouter({

  history: createWebHistory(),
  routes

});



router.beforeEach((to) => {
  const auth = useAuthStore();
  const isLoggedIn = !!auth.token || !!localStorage.getItem("token");

  // Si la page demande d'être connecté et qu'on ne l'est pas
  if (to.meta.auth && !isLoggedIn) {
    return "/login";
  }

  // Si on est déjà connecté et qu'on essaie d'aller sur Login/Register
  if (to.meta.guest && isLoggedIn) {
    return "/";
  }

  // Vérification du rôle admin
  if (to.meta.admin && auth.user?.role !== "admin") {
    return "/";
  }
});

export default router;
