<template>
	<button class="btn btn-danger btn-block w-100" @click="deleteAccount">Delete account</button>
</template>

<script>
    import axios from 'axios'
    import router, { backendRoutes } from '../router'
    import store, { persistentStore } from '../store'

	export default {
		methods: {
			async deleteAccount() {
				if (!confirm('Are you sure you want to delete account? This action is irreversible.')) {
					return
				}
				// persistentStore.commit('invalidateSession')

				try {
                    let method = backendRoutes.deleteProfile.method.toLowerCase()
                    let response = await axios[method](backendRoutes.deleteProfile.path)

                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                    persistentStore.commit('invalidateSession')
                    router.push({ name: 'registration' })
                }
                catch (error) {
                    store.commit('addFlash', { message: error.response.data.data.message, type: 'failure' })
                }
			}
		},
	}
</script>