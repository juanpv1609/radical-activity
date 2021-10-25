import VueRouter from 'vue-router';
import store from '../store/store';
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import Home from "../components/Home.vue";

import AllUsuarios from "../components/usuario/AllUsuarios.vue";
import Actividad from "../components/actividad/Actividad.vue";
import ActividadNew from "../components/actividad/ActividadNew.vue";
import AllAreas from "../components/area/AllAreas.vue";
import Pais from "../components/pais/Pais.vue";
import PerfilPuesto from "../components/perfilPuesto/PerfilPuesto.vue";
import Horarios from "../components/horario/AllHorarios.vue";
import TipoActividad from "../components/tipoActividad/AllTipoActividad.vue";
//REPORTES

import ReporteResumen from "../components/reportes/ReporteResumen.vue";
import Reporte from "../components/reportes/Reporte.vue";

const router = new VueRouter({
    mode: "history",
    routes: [
        { path: "*", redirect: "/" },
        {
            name: "home",
            path: "/",
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
        {
            name: "areas",
            path: "/areas",
            component: AllAreas,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "paises",
            path: "/paises",
            component: Pais,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "perfil-puesto",
            path: "/perfil-puesto",
            component: PerfilPuesto,
            meta: {
                requiresAuth: true,
            },
        },
        {
            name: "horarios",
            path: "/horarios",
            component: Horarios,
            meta: {
                requiresAuth: true,
            },
        },
        {
            name: "tipo-actividad",
            path: "/tipo-actividad",
            component: TipoActividad,
            meta: {
                requiresAuth: true,
            },
        },
        //REPORTES

        {
            name: "reporte-resumen",
            path: "/reporte-resumen",
            component: ReporteResumen,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "reporte",
            path: "/reporte",
            component: Reporte,
            meta: {
                requiresAuth: true
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
