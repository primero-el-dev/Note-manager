<template>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <router-link :to="{ name: 'index' }" class="navbar-brand">App</router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li v-if="isLoggedIn()" class="nav-item">
                        <router-link :to="{ name: 'index' }" class="nav-link" aria-current="page">Home</router-link>
                    </li>
                    <li v-if="isLoggedIn()" class="nav-item">
                        <router-link :to="{ name: 'profile' }" class="nav-link" aria-current="page">Profile</router-link>
                    </li>
                    <li v-if="!isLoggedIn()" class="nav-item">
                        <router-link :to="{ name: 'login' }" class="nav-link">Login</router-link>
                    </li>
                    <li v-if="!isLoggedIn()" class="nav-item">
                        <router-link :to="{ name: 'registration' }" class="nav-link">Registration</router-link>
                    </li>
                    <li v-if="isLoggedIn()" class="nav-item">
                        <a class="nav-link" style="cursor: pointer;" @click="logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
    import axios from 'axios'
    import router, { routes, backendRoutes, isLoggedIn } from '../router'
    import store, { persistentStore } from '../store'

    export default {
        props: ['links'],
        data: () => ({
            persistentStore: persistentStore,
            isLoggedIn: isLoggedIn,
        }),
        methods: {
            async logout(e) {
                e.preventDefault()

                try {
                    let method = backendRoutes.logout.method.toLowerCase()
                    let response = await axios[method](backendRoutes.logout.path)
                    
                    persistentStore.commit('invalidateSession')
                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                    router.push({ name: 'login' })
                }
                catch (error) {
                    console.log(error)
                    store.commit('addFlash', { message: error?.response?.data?.data?.message, type: 'failure' })
                }
            },
        },
    }
</script>