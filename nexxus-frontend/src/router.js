import {createRouter, createWebHistory} from "vue-router";
import Login from "./pages/Login.vue";
import HomePage from "./pages/HomePage.vue";
import {setAuthToken} from "./auth.js";
import Register from "./pages/Register.vue";


const routes = [
    { path: '/', component: Login },
    {
        path: '/home',
        component: HomePage,
        beforeEnter: (to, from, next) => {
            const token = localStorage.getItem("token");
            if(token) {
                setAuthToken(token);
                next();
            } else {
                next('/');
            }
        },
    },
    {
        path: '/register',
        component: Register
    },
    {
        path: '/login',
        component: Login
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;