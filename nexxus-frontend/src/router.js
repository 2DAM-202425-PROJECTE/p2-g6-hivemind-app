// router/index.js
import { createRouter, createWebHistory } from "vue-router";
import apiClient from './axios.js';

// Pages
// Auth Pages
import Login from "./pages/Auth/Login.vue";
import Register from "./pages/Auth/Register.vue";
import VerifyEmail from "./pages/Auth/VerifyEmail.vue";
import ForgotPassword from "./pages/Auth/ForgotPassword.vue";
import ResetPassword from "./pages/Auth/ResetPassword.vue";
import CheckEmail from "@/pages/Auth/CheckEmail.vue";
import HomePage from "./pages/HomePage.vue";
import ProfilePage from "./pages/ProfilePage.vue";
import About from "./pages/Footer/About.vue";
import PrivacyPolicy from "./pages/Footer/PrivacyPolicyPage.vue";
import TermsOfService from "./pages/Footer/TermsOfServicePage.vue";
import ContactPage from "./pages/Footer/ContactPage.vue";
import ChatPage from "./pages/ChatPage.vue";
import ServerPage from "./pages/ServerPage.vue";
import ShopPage from "./pages/ShopPage.vue";
import PurchasePage from "./pages/PurchasePage.vue";
import AppSettingsPage from "./pages/AppSettingsPage.vue";
import AccountSettingsPage from "./pages/AccountSettingsPage.vue";
import CompleteProfilePage from "./components/Profile/CompleteProfilePage.vue";
import UserMediaPage from "./components/Profile/UserMediaPage.vue";
import VideosPage from "./pages/VideosPage.vue";
import LiveStreamsPage from "./pages/LiveStreamsPage.vue";
import NotFoundPage from "./pages/errors/NotFoundPage.vue";
import FollowersPage from './components/Profile/FollowersPage.vue';
import FollowingPage from './components/Profile/FollowingPage.vue';

export const routes = [
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFoundPage
  },
  {
    path: "/",
    redirect: (to) => {
      const isAuthenticated = !!localStorage.getItem("token");
      return isAuthenticated ? "/home" : "/auth/login";
    },
  },
  // Auth Routes
  { path: "/auth/login", name: "Login", component: Login },
  { path: "/auth/register", name: "Register", component: Register },
  { path: '/auth/check-email', name: 'CheckEmail', component: CheckEmail, props: route => ({ email: route.query.email })},
  { path: '/auth/verify-email', name: 'VerifyEmail', component: VerifyEmail, props: route => ({ token: route.query.token, email: route.query.email }) },
  { path: "/auth/forgot-password", name: "ForgotPassword", component: ForgotPassword },
  { path: "/auth/reset-password/:token", name: "ResetPassword", component: ResetPassword },


  // Other app Routes
  { path: "/home", name: "Home", component: HomePage },
  { path: "/profile/:username", name: "Profile", component: ProfilePage, props: true },
  { path: "/about", name: "About", component: About },
  { path: "/privacy-policy", name: "PrivacyPolicy", component: PrivacyPolicy },
  { path: "/terms-of-service", name: "TermsOfService", component: TermsOfService },
  { path: "/contact", name: "Contact", component: ContactPage },
  { path: "/chat", name: "Chat", component: ChatPage },
  { path: "/servers", name: "Servers", component: ServerPage },
  { path: "/live-streams", name: "LiveStreams", component: LiveStreamsPage },
  { path: "/shop", name: "Shop", component: ShopPage },
  { path: "/purchase/:id", name: "Purchase", component: PurchasePage, props: true },
  { path: "/settings", name: "Settings", component: AppSettingsPage },
  { path: "/account-settings", name: "AccountSettings", component: AccountSettingsPage },
  { path: "/complete-profile", name: "CompleteProfile", component: CompleteProfilePage },
  { path: '/profile/:username/followers', name: 'Followers', component: FollowersPage, props: true },
  { path: '/profile/:username/following', name: 'Following', component: FollowingPage, props: true },
  {
    path: "/users/:username/media",
    name: "UserMediaPage",
    component: UserMediaPage,
    props: true
  },
  {
    path: "/videos",
    name: "Videos",
    component: VideosPage
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  // Rutas públicas que no requieren autenticación
  const publicRoutes = ['Login', 'Register', 'CheckEmail', 'VerifyEmail', 'ForgotPassword', 'ResetPassword'];

  // Rutas que requieren autenticación pero no validación de is_profile_completed
  const authOnlyRoutes = ['PrivacyPolicy', 'TermsOfService', 'CompleteProfile'];

  // Si la ruta es pública, permitir acceso sin verificación
  if (publicRoutes.includes(to.name)) {
    return next();
  }

  try {
    const response = await apiClient.get('/api/check-auth');
    console.log('Authentication check:', response.data);

    if (response.data.authenticated) {
      // Si la ruta está en authOnlyRoutes, permitir acceso sin verificar is_profile_completed
      if (authOnlyRoutes.includes(to.name)) {
        return next();
      }

      // Verificar is_profile_completed para otras rutas
      if (!response.data.user || !response.data.user.is_profile_completed) {
        if (to.name !== 'CompleteProfile') {
          return next('/complete-profile');
        }
      }
      return next();
    }
    return next('/auth/login');
  } catch (error) {
    console.warn('Authentication failed:', error.response?.status);
    console.log('Error details:', error.response?.data);
    return next('/auth/login');
  }
});

export default router;
