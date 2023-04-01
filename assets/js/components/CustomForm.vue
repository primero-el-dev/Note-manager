<template>
    <form :action="action" :method="method" :onSubmit="onSubmit" class="my-3">
        <form-control 
            v-for="(fc, i) in formControls" 
              ref="values"
            :label="fc.label" 
            :type="fc.type" 
            :name="fc.name" 
            :error="(this.errors[fc.name] !== undefined) ? this.errors[fc.name] : null"
            :placeholder="fc.placeholder" />
        <button class="btn btn-success mt-2 w-100" type="submit">{{ submitText }}</button>
    </form>
</template>

<script>
    import FormControl from './FormControl'

    export default {
        components: {
            FormControl,
        },
        props: {
            action: {
                type: String,
                required: false,
                default: '',
            },
            method: {
                type: String,
                required: false,
                default: 'POST',
            },
            onSubmit: {
                type: Function,
                required: true,
            },
            submitText: {
                type: String,
                required: false,
                default: 'Send',
            },
            formControls: {
                type: Array,
                required: false,
                default: [],
            },
            errors: {
                type: Array,
                required: false,
                default: [],
            },
        },
        methods: {
            getFormData() {
                let values = {}
                for (let input of this.$refs.values) {
                    let match = input.name.match(/^(?<name>.*)\[(?<key>[\w\d\_]*)\]$/)
                    if (match !== undefined && match !== null) {
                        if (values[match.groups.name] === undefined) {
                            values[match.groups.name] = (match.groups.key === '') ? [] : {}
                        }
                        
                        if (match.groups.key === '') {
                            values[match.groups.name].push(input.value)
                        }
                        else {
                            values[match.groups.name][match.groups.key] = input.value
                        }
                    }
                    else {
                        values[input.name] = input.value
                    }
                }
                
                return values
            },
        },
    }
</script>