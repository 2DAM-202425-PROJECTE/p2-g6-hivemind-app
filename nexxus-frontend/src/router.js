// router/index.js
import { createRouter, createWebHistory } from "vue-router";
import { setAuthToken } from "./auth.js";

// Pages
import HomePage from "./pages/HomePage.vue";
import Login from "./pages/Login.vue";
import Register from "./pages/Register.vue";
import ProfilePage from "./pages/ProfilePage.vue";
import About from "./pages/About.vue";
import PrivacyPolicy from "./pages/PrivacyPolicyPage.vue";
import TermsOfService from "./pages/TermsOfServicePage.vue";
import ContactPage from "./pages/ContactPage.vue";
import ChatPage from "./pages/ChatPage.vue";
import ServerPage from "./pages/ServerPage.vue";
import ShopPage from "./pages/ShopPage.vue";
import PurchasePage from "./pages/PurchasePage.vue";
import AppSettingsPage from "./pages/AppSettingsPage.vue";
import AccountSettingsPage from "./pages/AccountSettingsPage.vue";
import CompleteProfilePage from "./pages/CompleteProfilePage.vue";
import UserPostsPage from "./components/Profile/UserPostsPage.vue"; // Verify this path
import VideosPage from "./pages/VideosPage.vue"; // Updated to match file name
import UserVideosPage from './components/Profile/UserVideosPage.vue';
import LiveStreamsPage from "./pages/LiveStreamsPage.vue"; // Adjust path as needed

export const routes = [
  {
    path: "/",
    redirect: (to) => {
      const isAuthenticated = !!localStorage.getItem("token");
      return isAuthenticated ? "/home" : "/login";
    },
  },
  { path: "/home", name: "Home", component: HomePage },
  { path: "/login", name: "Login", component: Login },
  { path: "/register", name: "Register", component: Register },
  { path: "/profile/:username", name: "Profile", component: ProfilePage, props: true },
  { path: "/about", name: "About", component: About },
  { path: "/privacy-policy", name: "PrivacyPolicy", component: PrivacyPolicy },
  { path: "/terms-of-service", name: "TermsOfService", component: TermsOfService },
  { path: "/contact", name: "Contact", component: ContactPage },
  { path: "/chat", name: "Chat", component: ChatPage },
  { path: "/servers", name: "Servers", component: ServerPage },
  { path: "/live-streams", name: "LiveStreams", component: (LiveStreamsPage) },
  { path: "/shop", name: "Shop", component: ShopPage },
  { path: "/purchase/:id", name: "Purchase", component: PurchasePage, props: true },
  { path: "/settings", name: "Settings", component: AppSettingsPage },
  { path: "/account-settings", name: "AccountSettings", component: AccountSettingsPage },
  { path: "/complete-profile", name: "CompleteProfile", component: CompleteProfilePage },
  {
    path: "/users/username/:username/posts",
    name: "UserPostsPage",
    component: UserPostsPage,
    props: true
  },
  {
    path: '/users/username/:username/videos',
    name: 'UserVideosPage',
    component: UserVideosPage,
    props: true
  },
  {
    path: '/videos',
    name: 'Videos',
    component: VideosPage
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("token");
  const publicRoutes = ["Login", "Register"];

  if (!isAuthenticated && !publicRoutes.includes(to.name)) {
    next("/login");
  } else {
    if (isAuthenticated) setAuthToken(localStorage.getItem("token"));
    next();
  }
});

export default router;
