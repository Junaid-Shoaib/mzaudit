<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Years
            </h2>
        </template>
        <div class="">
            <form @submit.prevent="submit">
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Company:</label>
                    <input type="text" v-model="form.company_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="company_id"/>
                    <div v-if="errors.company_id">{{ errors.company_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Begin:</label>
                    <input type="text" v-model="form.begin" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="begin"/>
                    <div v-if="errors.begin">{{ errors.begin }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">End:</label>
                    <input type="text" v-model="form.end" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="end"/>
                    <div v-if="errors.end">{{ errors.end }}</div>
                </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Create Year</button>
                </div>
            </form>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'

    export default {
        components: {
            AppLayout,
        },

        props: {
            errors : Object,
        },

        data() {
            return {
                form: this.$inertia.form({
                    company_id: null,
                    begin: null,
                    end: null,
 //                   type: this.first.id,
                }),
            }
        },

        methods: {
            submit() {
            this.$inertia.post(route('years.store'), this.form)
            },
        },
    }
</script>
