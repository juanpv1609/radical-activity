import VueRouter from 'vue-router';
import store from '../store/store';
import Register from "../components/auth/Register.vue";
import Login from "../components/auth/Login.vue";

import AllUsuarios from "../components/usuario/AllUsuarios.vue";
import Home from "../components/Home.vue";
import Pais from "../components/pais/Pais.vue";
import Personal from "../components/personal/Personal.vue";
import PersonalNew from "../components/personal/PersonalNew.vue";
import PersonalEdit from "../components/personal/PersonalEdit.vue";
import Certificaciones from "../components/certificaciones/Certificaciones.vue";
import NivelEstudio from "../components/nivelEstudio/NivelEstudio.vue";

const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: "*", redirect: "/" },
        {
            name: "home",
            path: "/",
            component: Personal,
            meta: {
                requiresAuth: false,
              },
        },
        {
            path: "/register",
            name: "register",
            component: Register,
            meta: {
          requiresAuth: false,
        },
        },
        {
            path: "/login",
            name: "login",
            component: Login,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "usuarios",
            path: "/usuarios",
            component: AllUsuarios,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "paises",
            path: "/paises",
            component: Pais,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "personal",
            path: "/personal",
            component: Personal,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "personal-new",
            path: "/personal-new",
            component: PersonalNew,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "personal-edit",
            path: "/personal/edit/:id",
            component: PersonalEdit,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "certificaciones",
            path: "/certificaciones",
            component: Certificaciones,
            meta: {
          requiresAuth: false,
        },
        },
        {
            name: "nivel-estudio",
            path: "/nivel-estudio",
            component: NivelEstudio,
            meta: {
          requiresAuth: false,
        },
        },
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
