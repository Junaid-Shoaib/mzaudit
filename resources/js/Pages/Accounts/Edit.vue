<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bank Accounts
            </h2>
        </template>
        <div class="">
            <form @submit.prevent="submit">
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Branch:</label>
                    <select v-model="form.branch_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="branch_id" placeholder="Enter type">
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{branch.address}}</option>
                    </select>
                    <div v-if="errors.branch_id">{{ errors.branch_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Number:</label>
                    <input type="text" v-model="form.name" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="name"/>
                    <div v-if="errors.name">{{ errors.name }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Type:</label>
                    <input type="text" v-model="form.type" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="type"/>
                    <div v-if="errors.type">{{ errors.type }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Currency:</label>
                    <input type="text" v-model="form.currency" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="currency"/>
                    <div v-if="errors.currency">{{ errors.currency }}</div>
                </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Update Account</button>
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
            account : Object,
            branches : Object,  
 //           companies : Object,
        },

        data() {
            return {
                form: {
                    branch_id: this.account.branch_id,
   //                 company_id: this.account.company_id,
                    name: this.account.name,
                    type: this.account.type,
                    currency: this.account.currency,
                },
            }
        },

        methods: {
            submit() {
            this.$inertia.put(route('accounts.update', this.account.id), this.form)
            }, 
        },

    }
</script>
