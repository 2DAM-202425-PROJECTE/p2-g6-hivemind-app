import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../pages/HomePage.vue';
import Login from '../pages/Login.vue';
import ProfilePage from '../pages/ProfilePage.vue';
import EditProfilePage from '../pages/EditProfilePage.vue';
import Register from '../pages/Register.vue';
import About from '../pages/About.vue';
import PrivacyPolicy from '../pages/PrivacyPolicyPage.vue';
import TermsOfService from '../pages/TermsOfServicePage.vue';
import ShopPage from '../pages/ShopPage.vue'
import PurchasePage from '../pages/PurchasePage.vue'


const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfilePage,
  },
  {
    path: '/edit-profile',
    name: 'EditProfile',
    component: EditProfilePage,
  },
  {
    path: '/Register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/About',
    name: 'About',
    component: About,
  },
  {
    path:'/PrivacyPolicy',
    name:'PrivacyPolicy',
    component: PrivacyPolicy,
  },
  {
    path:'/TermsOfService',
    name: 'TermsOfService',
    component: TermsOfService,
  },
  {
    path: '/shop',
    name: 'ShopPage',
    component: ShopPage
  },
  {
    path: '/purchase/:id',
    name: 'PurchasePage',
    component: PurchasePage,
    props: route => ({
      subscriptionTiers: JSON.parse(route.query.subscriptionTiers || '[]'),
      creditPacks: JSON.parse(route.query.creditPacks || '[]'),
      cosmeticCategories: JSON.parse(route.query.cosmeticCategories || '[]')
    })
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// Workaround for https://github.com/vitejs/vite/issues/11804
router.onError((err, to) => {
  if (err?.message?.includes?.('Failed to fetch dynamically imported module')) {
    if (!localStorage.getItem('vuetify:dynamic-reload')) {
      console.log('Reloading page to fix dynamic import error');
      localStorage.setItem('vuetify:dynamic-reload', 'true');
      location.assign(to.fullPath);
    } else {
      console.error('Dynamic import error, reloading page did not fix it', err);
    }
  } else {
    console.error(err);
  }
});

router.isReady().then(() => {
  localStorage.removeItem('vuetify:dynamic-reload');
});

export default router;
