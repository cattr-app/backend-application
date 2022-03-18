import {createRouter, createWebHistory, RouterOptions} from 'vue-router';

const routes = [
    {
        path: '/auth/login',
        name: 'auth.login',
        meta: {
            auth: false,
            layout: 'auth'
        },
        component: () => import('@/scripts/views/auth/login')
    },
    {
        path: '/auth/email',
        name: 'auth.email',
        meta: {
            auth: false,
            layout: 'auth'
        },
        component: () => import('@/scripts/views/auth/login')
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
} as RouterOptions);

export default router;
