<template>
    <navbar />

    <div class="container-fluid">
        <div v-for="flash in store.state.flashes" :class="`alert alert-${flash.type === 'failure' ? 'danger' : flash.type} alert-dismissible my-2`" role="alert">
            {{ flash.message }}
            <button type="button" class="btn-close" aria-label="Close" @click="store.commit('deleteFlash', flash.id)"></button>
        </div>

        <div class="mt-4">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import Navbar from './Navbar'
    import store from '../store'

    export default {
        components: {
            Navbar,
            store,
        },
        data() {
            return {
                store: store,
            }
        }
    }
</script>