import VueRouter from 'vue-router';
import store from '../store/store';
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";
import Home from "../components/Home.vue";

import AllUsuarios from "../components/usuario/AllUsuarios.vue";
import Actividad from "../components/actividad/Actividad.vue";
import MiActividad from "../components/actividad/MiActividad.vue";
import ActividadCalendar from "../components/actividad/ActividadCalendar.vue";
import MiActividadCalendar from "../components/actividad/MiActividadCalendar.vue";
import ActividadNew from "../components/actividad/ActividadNew.vue";
import ActividadEdit from "../components/actividad/ActividadEdit.vue";
import AllAreas from "../components/area/AllAreas.vue";
import Clasificacion from "../components/clasificacion/Clasificacion.vue";
import Pais from "../components/pais/Pais.vue";
import PerfilPuesto from "../components/perfilPuesto/PerfilPuesto.vue";
import Horarios from "../components/horario/AllHorarios.vue";
import TipoActividad from "../components/tipoActividad/AllTipoActividad.vue";
//REPORTES

import ReporteDetalle from "../components/reportes/ReporteResumen.vue";
import ReporteContable from "../components/reportes/Reporte.vue";
import Dashboard from "../components/dashboard/Dashboard.vue";

//PROACTIVANET
import cierreTickets from "../components/panet/cierreTickets.vue";
import verificarTickets from "../components/panet/verificarTickets.vue";


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
            name: "mi-actividad",
            path: "/mi-actividad",
            component: MiActividad,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "actividad-calendar",
            path: "/actividad-calendar",
            component: ActividadCalendar,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "mi-actividad-calendar",
            path: "/mi-actividad-calendar",
            component: MiActividadCalendar,
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
            name: "actividad-edit",
            path: "/actividad-edit/:id",
            component: ActividadEdit,
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
            name: "clasificacion",
            path: "/clasificacion",
            component: Clasificacion,
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
            name: "reporte-detalle",
            path: "/reporte-detalle",
            component: ReporteDetalle,
            meta: {
                requiresAuth: true
            }
        },
        {
            name: "reporte-contable",
            path: "/reporte-contable",
            component: ReporteContable,
            meta: {
                requiresAuth: true
            }
        }
        //DASHBOARD
        ,
        {
            name: "dashboard",
            path: "/dashboard",
            component: Dashboard,
            meta: {
                requiresAuth: true
            }
        }
        ,
        {
            name: "cierre-tickets",
            path: "/cierre-tickets",
            component: cierreTickets,
            meta: {
                requiresAuth: true
            }
        }
        ,
        {
            name: "verificar-tickets",
            path: "/verificar-tickets",
            component: verificarTickets,
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
