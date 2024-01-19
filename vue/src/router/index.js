import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import DefaultLayout from "../components/DefaultLayout.vue";
import Surveys from "../views/Surveys.vue";
import AuthLayout from "../components/AuthLayout.vue";
import SurveyView from "../views/SurveyView.vue";
import SurveyPublicView from "../views/SurveyPublicView.vue";
import store from "../store";

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
        component: DefaultLayout,
        meta: {requiresAuth: true},//to check whether the route requires authentication
        children: [
            {
                path: '/dashboard',
                name: 'Dashboard',
                component: Dashboard
            },
            {
                path: '/surveys',
                name: 'Surveys',
                component: Surveys
            },
            {
                path: '/surveys/create',
                name: 'SurveyCreate',
                component: SurveyView
            },
            {
                path: '/surveys/:id',
                name: 'SurveyView',
                component: SurveyView
            },
        ]
    },
    {
        path: '/view/survey/:slug',
        name: 'SurveyPublicView',
        component: SurveyPublicView
    },
    {
        path: '/auth',
        redirect: '/login',
        name: 'Auth',
        meta: {isGuest: true},//to identify routes that are accessible to guests 
        component: AuthLayout,
        children: [
            {
                path: '/login',
                name: 'Login',
                component: Login
            },
            {
                path: '/register',
                name: 'Register',
                component: Register
            },
        ]
    },
    
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

//route guard 
router.beforeEach((to, from, next) => {
    if(to.meta.requiresAuth && !store.state.user.token){
        next({name: 'Login'});//to redirect user to login page due to lack of token
    }else if(store.state.user.token && to.meta.isGuest){
        next({name: 'Dashboard'});//to redirect authorized user back to dashboard if they try to jump over to login or register page
    }else{
        next();
    }
})

export default router;