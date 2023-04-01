import * as VueRouter from 'vue-router'
import RegistrationPage from './components/RegistrationPage'
import LoginPage from './components/LoginPage'
import HomePage from './components/HomePage'
import ProfilePage from './components/ProfilePage'
import { persistentStore } from './store'

const Home = { template: '<div>Home</div>' }

export const backendRoutes = {
    login: { path: '/api/login', method: 'POST' },
    logout: { path: '/api/logout', method: 'POST' },
    registration: { path: '/api/registration', method: 'POST' },
    getNotes: { path: '/api/note', method: 'GET' },
    createNote: { path: '/api/note', method: 'POST' },
    updateNote: { path: '/api/note/:id', method: 'PUT' },
    deleteNote: { path: '/api/note/:id', method: 'DELETE' },
    deleteProfile: { path: '/api/profile', method: 'DELETE' },
}

export const prepareBackendRoute = (name, params) => {
    let path = backendRoutes[name].path
    for (let key in params) {
        path = path.replace(':'+key, params[key])
    }
    return path
}

export const isLoggedIn = () =>  persistentStore.state.sessionExpiryInSeconds * 1000 >= (new Date()).getTime()

const autheticatedGuard = (to, from, next) => {
    isLoggedIn() ? next() : next({ name: 'login' })
}

const anonymousGuard = (to, from, next) => {
    isLoggedIn() ? next({ name: 'index' }) : next()
}

export const routes = [
    { path: '/', name: 'index', component: HomePage, beforeEnter: autheticatedGuard },
    { path: '/profile', name: 'profile', component: ProfilePage, beforeEnter: autheticatedGuard },
    { path: '/login', name: 'login', component: LoginPage, beforeEnter: anonymousGuard },
    { path: '/registration', name: 'registration', component: RegistrationPage, beforeEnter: anonymousGuard },
]

export default VueRouter.createRouter({
    mode: 'history',
    history: VueRouter.createWebHistory(),
    routes,
})

