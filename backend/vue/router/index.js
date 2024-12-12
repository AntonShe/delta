import * as Router from 'vue-router'

const routes = [
    //region Admin Main
    {
        path: '/admin',
        name: 'AdminMain',
        component: () => import('../views/admin_page/Admin'),
    },
    //endregion
    //region Settings section
    {
        path: '/admin/login',
        name: 'Login',
        component: () => import('../views/login_page/Login'),
    },
    {
        path: '/admin/user',
        name: 'Users',
        component: () => import('../views/users_page/Users'),
    },
    //endregion
    //region Catalog section
    {
        path: '/admin/products',
        name: 'Products',
        props: {
            isNew: false
        },
        component: () => import('../views/products_page/Products'),
    },
    {
        path: '/admin/products-new',
        name: 'ProductsNew',
        props: {
            isNew: true
        },
        component: () => import('../views/products_page/Products'),
    },
    {
        path: '/admin/persons',
        name: 'Persons',
        component: () => import('../views/persons_page/Persons'),
    },
    {
        path: '/admin/publishers',
        name: 'Publishers',
        component: () => import('../views/publishers_page/Publishers'),
    },
    //endregion
    //region Orders sections
    {
        path: '/admin/orders',
        name: 'Orders',
        component: () => import('../views/order_page/Order'),
    },
    //endregion
    //region Marketing section
    {
        path: '/admin/marketing-banners',
        name: 'MarketingBanners',
        component: () => import('../views/marketing/banners_page/Banners'),
    },
    {
        path: '/admin/marketing-shelves',
        name: 'MarketingShelves',
        component: () => import('../views/marketing/shelves_page/Shelves'),
    },
    {
        path: '/admin/marketing-promotions',
        name: 'MarketingPromotions',
        component: () => import('../views/marketing/promotions_page/Promotions'),
    },
    {
        path: '/admin/marketing-genres',
        name: 'MarketingGenres',
        component: () => import('../views/marketing/genres_page/Genres'),
    },
    //endregion
]

const router = Router.createRouter({
    history: Router.createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (to.hash) {
            return {el: to.hash}
        } else if (savedPosition) {
            return savedPosition;
        } else {
            return {top: 0}
        }
    }
})

export default router