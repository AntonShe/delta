import * as Router from 'vue-router'

const routes = [
    {
        path: '/delivery',
        name: 'Delivery',
        component: () => import('../views/delivery_page/DeliveryPage.vue'),
    },
    {
        path: '/cart',
        name: 'Cart',
        component: () => import('../views/cart_page/CartPage.vue'),
    },
    {
        path: '/profile/:page?/:orderNumber?',
        name: 'Profile',
        component: () => import('../views/profile_page/ProfilePage.vue'),
    },
    {
        path: '/order/:page?',
        name: 'Order',
        component: () => import('../views/order_page/OrderPage.vue'),
    },
]

const router = Router.createRouter({
    history: Router.createWebHistory(),
    routes,
    scrollBehavior (to, from, savedPosition) {
        if (to.hash) {
            return { el: to.hash }
        } else if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 }
        }
    }
})

export default router