import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import About from '../views/About.vue'
import Shop from '../views/Shop.vue'
import Broadcast from '../views/Broadcast.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import NotFound from '../views/NotFound.vue'
import Notification from '../views/Notification.vue'
import Ad from '../views/Ad.vue'
import Visit from '../views/Visit.vue'
import Search from '../views/Search.vue'
import Password from '../views/Password.vue'

Vue.use(VueRouter)

const routes = [{
        path: '*',
        component: NotFound
    },
    {
        path: '/about',
        name: 'about',
        component: About
    },
    // {
    //     path: '/shop',
    //     name: 'shop',
    //     component: Shop,
    //     beforeEnter: (to, from, next) => {
    //         axios.get('/api/authenticated').then(() => {
    //             next()
    //         }).catch(() => {
    //             return next('login')
    //         })
    //     }
    // },
    {
        path: '/shop/:page?',
        name: 'shop',
        props: true,
        component: Shop,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then(() => {
                next()
            }).catch(() => {
                return next('login')
            })
        }
    },
    // {
    //     path: '/broadcast',
    //     name: 'broadcast',
    //     component: Broadcast,
    //     beforeEnter: (to, from, next) => {
    //         axios.get('/api/authenticated').then(() => {
    //             next()
    //         }).catch(() => {
    //             return next('login')
    //         })
    //     }
    // },
    {
        path: '/broadcast/:page?/:status?',
        name: 'broadcast',
        props: true,
        component: Broadcast,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then(() => {
                next()
            }).catch(() => {
                return next('login')
            })
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then((res) => {
                console.log(res);
                if (res.data == 1) {
                    return next('/')
                }
                return next('/login')
            }).catch(() => {
                return next()
            });
        }

    },
    {
        path: '/logout',
        name: 'logout',
        beforeEnter: (to, from, next) => {
            axios.post('/api/logout').then((res) => {
                return $router.push('login');
            }).catch((err) => { console.log(err); });
        },
    },
    {
        path: '/create-ad',
        name: 'ad',
        component: Ad,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then(() => {
                next()
            }).catch(() => {
                return next('login')
            })
        }
    },
    {
        path: '/notifications',
        name: 'notifications',
        component: Notification,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then(() => {
                next()
            }).catch(() => {
                return next('login')
            })
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then((res) => {
                console.log(res);
                if (res.data == 1) {
                    return next('/')
                }
                return next()
            }).catch(() => {
                return next()
            });
        }
    },
    {
        path: '/password/:page?/:token?',
        name: 'password',
        component: Password,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then((res) => {
                return next('shop')
            }).catch(() => {
                return next()
            });
        }
    },
    {
        path: '/search',
        name: 'search',
        component: Search
    },
    {
        path: '',
        name: 'home',
        component: About,
    },
    {
        path: '/:username',
        name: 'visit',
        component: Visit,
        props: true,
        beforeEnter: (to, from, next) => {
            axios.get('/api/authenticated').then(() => {
                next()
            }).catch(() => {
                return next('login')
            })
        }

    },

    // {
    //     path: '*',
    //     component: NotFound
    // },
]

const router = new VueRouter({
    mode: 'history',
    linkActiveClass: 'active-l',
    routes,
    base: process.env.BASE_URL,
    scrollBehavior(to, from, savedPosition) {
        // return { x: 0, y: 0 }
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
})

export default router