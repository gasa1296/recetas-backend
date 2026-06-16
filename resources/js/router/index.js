import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import AppLayout from '../layouts/AppLayout.vue'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('../views/Dashboard.vue'),
            },
            {
                path: 'profile',
                name: 'profile',
                component: () => import('../views/Profile.vue'),
            },
            {
                path: 'rooms',
                name: 'rooms.index',
                component: () => import('../views/rooms/Index.vue'),
            },
            {
                path: 'rooms/create',
                name: 'rooms.create',
                component: () => import('../views/rooms/Form.vue'),
            },
            {
                path: 'rooms/:id/edit',
                name: 'rooms.edit',
                component: () => import('../views/rooms/Form.vue'),
            },
            {
                path: 'specialties',
                name: 'specialties.index',
                component: () => import('../views/specialties/Index.vue'),
            },
            {
                path: 'specialties/create',
                name: 'specialties.create',
                component: () => import('../views/specialties/Form.vue'),
            },
            {
                path: 'specialties/:id/edit',
                name: 'specialties.edit',
                component: () => import('../views/specialties/Form.vue'),
            },
            {
                path: 'patients',
                name: 'patients.index',
                component: () => import('../views/patients/Index.vue'),
            },
            {
                path: 'patients/create',
                name: 'patients.create',
                component: () => import('../views/patients/Form.vue'),
            },
            {
                path: 'patients/:id/edit',
                name: 'patients.edit',
                component: () => import('../views/patients/Form.vue'),
            },
            {
                path: 'prescriptions',
                name: 'prescriptions.index',
                component: () => import('../views/prescriptions/Index.vue'),
            },
            {
                path: 'prescriptions/create',
                name: 'prescriptions.create',
                component: () => import('../views/prescriptions/Form.vue'),
            },
            {
                path: 'prescriptions/:id/edit',
                name: 'prescriptions.edit',
                component: () => import('../views/prescriptions/Form.vue'),
            },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next({ name: 'login' })
    } else if (to.meta.guest && auth.isAuthenticated) {
        next({ name: 'dashboard' })
    } else {
        next()
    }
})

export default router
