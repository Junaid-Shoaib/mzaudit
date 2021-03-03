<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bank Branches
            </h2>
        </template>
        <div class="">
            <form @submit.prevent="submit">
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">ID:</label>
                    <input type="text" v-model="form.bank_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="bank_id"/>
                    <div v-if="errors.bank_id">{{ errors.bank_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Branch:</label>
                    <input type="text" v-model="form.address" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="address"/>
                    <div v-if="errors.address">{{ errors.address }}</div>
                </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Create Branch</button>
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
//            types : Object,  
//            first: Object,  
        },

        data() {
            return {
                form: this.$inertia.form({
                    bank_id: null,
                    address: null,
 //                   type: this.first.id,
                }),
            }
        },

        methods: {
            submit() {
            this.$inertia.post(route('branches.store'), this.form)
            },
        },
    }
</script>
