import VueRouter from 'vue-router';
import store from '../store/store';
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import Home from "../components/Home.vue";

import AllUsuarios from "../components/usuario/AllUsuarios.vue";
import Actividad from "../components/actividad/Actividad.vue";
import ActividadNew from "../components/actividad/ActividadNew.vue";
//REPORTES

import ReporteResumen from "../components/reportes/ReporteResumen.vue";

const router = new VueRouter({
    mode: "history",
    routes: [
        { path: "*", redirect: "/" },
        {
            name: "home",
            path: "/home",
            component: Home,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: "/register",
            name: "register",
            component: Register,
            meta: {
                requiresAuth: false
            }
        },
        {
            path: "/login",
            name: "login",
            component: Login,
            meta: {
                requiresAuth: false
            }
        },
        {
            name: "usuarios",
            path: "/usuarios",
            component: AllUsuarios,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "actividad",
            path: "/actividad",
            component: Actividad,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "actividad-new",
            path: "/actividad-new",
            component: ActividadNew,
            meta: {
                requiresAuth: true
            }
        },
        //REPORTES

        {
            name: "reporte-resumen",
            path: "/reporte-resumen",
            component: ReporteResumen,
            meta: {
                requiresAuth: false
            }
        }
    ]
});

router.beforeEach( (to, from, next) => {
    if (  to.matched.some(record => record.meta.requiresAuth)) {

            if ( store.state.auth) {
                return next()
            } else
            next('/login')
        } else {
            next()
        }
    })

export default router
