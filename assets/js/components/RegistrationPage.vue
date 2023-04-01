<template>
    <h1>Registration</h1>
    <custom-form ref="form" :errors="errors" :onSubmit="submit" submitText="Register" :formControls="formControls" />
</template>

<script>
    import { ref, onMounted } from 'vue'
    import CustomForm from './CustomForm'
    import axios from 'axios'
    import router, { backendRoutes } from '../router'
    import store from '../store'

    export default {
        components: {
            CustomForm,
        },
        data: () => ({
            formControls: [
                { name: 'email', label: 'Email', type: 'email', placeholder: 'Email address' },
                { name: 'password[first]', label: 'Password', type: 'password', placeholder: 'Password' },
                { name: 'password[second]', label: 'Repeat password', type: 'password', placeholder: 'Repeat password' },
            ],
            errors: {},
        }),
        methods: {
            async submit(e) {
                e.preventDefault()
                let formData = this.$refs.form.getFormData()

                try {
                    let method = backendRoutes.registration.method.toLowerCase()
                    let response = await axios[method](backendRoutes.registration.path, formData)

                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                    router.push({ name: 'index' })
                }
                catch (error) {
                    this.errors['email'] = error.response.data.data.errors.email
                    this.errors['password[first]'] = error.response.data.data.errors.password
                }
            },
        },
    }
</script>