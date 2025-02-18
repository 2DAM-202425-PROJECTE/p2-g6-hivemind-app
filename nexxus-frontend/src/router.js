import { createRouter, createWebHistory } from "vue-router";
import Login from "./pages/Login.vue";
import HomePage from "./pages/HomePage.vue";
import { setAuthToken } from "./auth.js";
import Register from "./pages/Register.vue";
import ProfilePage from "./pages/ProfilePage.vue";
import EditProfilePage from "./pages/EditProfilePage.vue";
import About from "./pages/About.vue";
import PrivacyPolicy from "./pages/PrivacyPolicyPage.vue";
import TermsOfService from "./pages/TermsOfServicePage.vue";
import ContactPage from "@/pages/ContactPage.vue";
import ChatPage from "@/pages/ChatPage.vue";
import ServerPage from "@/pages/ServerPage.vue"; // Ensure this import is correct

const routes = [
  { path: '/', component: Login },
  {
    path: '/home',
    component: HomePage,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem("token");
      if (token) {
        setAuthToken(token);
        next();
      } else {
        next('/');
      }
    },
  },
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  {
    path: '/contact',
    component: ContactPage,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem("token");
      if (token) {
        setAuthToken(token);
        next();
      } else {
        next('/');
      }
    },
  },
  {
    path: '/chat',
    component: ChatPage,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem("token");
      if (token) {
        setAuthToken(token);
        next();
      } else {
        next('/');
      }
    },
  },
  { path: '/chat', component: ChatPage },
  { path: '/servers', component: ServerPage },
  { path: '/profile', component: ProfilePage },
  { path: '/edit-profile', component: EditProfilePage },
  { path: '/about', component: About },
  { path: '/privacy-policy', component: PrivacyPolicy },
  { path: '/terms-of-service', component: TermsOfService },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
