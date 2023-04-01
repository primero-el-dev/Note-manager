<template>
    <h1>Login</h1>
    <custom-form ref="form" :errors="errors" :onSubmit="submit" submitText="Login" :formControls="formControls" />
</template>

<script>
    import CustomForm from './CustomForm'
    import axios from 'axios'
    import router, { backendRoutes } from '../router'
    import store, { updateSessionFromResponse } from '../store'

    export default {
        components: {
            CustomForm,
        },
        data: () => ({
            formControls: [
                { name: 'email', label: 'Email', type: 'email', placeholder: 'Email address' },
                { name: 'password', label: 'Password', type: 'password', placeholder: 'Password' },
            ],
            errors: {},
        }),
        methods: {
            async submit(e) {
                e.preventDefault()
                let formData = this.$refs.form.getFormData()

                try {
                    let method = backendRoutes.login.method.toLowerCase()
                    let response = await axios[method](backendRoutes.login.path, formData)

                    updateSessionFromResponse(response)
                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                    router.push({ name: 'index' })
                }
                catch (error) {
                    updateSessionFromResponse(error.response)
                    this.errors['password'] = error.response.data.data.error
                }
            },
        }
    }
</script>