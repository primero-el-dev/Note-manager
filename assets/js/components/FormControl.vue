<template>
    <label class="mb-3 form-label w-100">
        {{ label }}
        <input v-if="type !== 'textarea'" v-bind="$attrs" :type="type" v-model="value" :name="name" :placeholder="placeholder" class="form-control">
        <textarea v-else v-bind="$attrs" v-model="value" :name="name" :placeholder="placeholder" class="form-control" rows="4">{{ value }}</textarea>
        <small v-if="error" class="text-danger">{{ error }}</small>
    </label>
</template>

<script>
    export default {
        emits: ['input'],
        props: {
            label: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: false,
                default: '',
            },
            type: {
                type: String,
                required: false,
                default: 'text',
            },
            placeholder: {
                type: String,
                required: false,
            },
            error: {
                type: String,
                required: false,
                default: null,
            },
            value: {
                required: false,
                default: '',
            },
        },
        data() {
            return {
                value: this.$props.value,
            }
        },
    }
</script>