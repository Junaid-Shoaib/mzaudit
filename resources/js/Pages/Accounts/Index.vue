<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bank Accounts
            </h2>
        </template>
        <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
            {{ $page.props.flash.success }}
        </div>

        <div class="relative mt-5 ml-7 flex-row">
            <div class="flex-1 inline-block">
                <inertia-link class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" :href="route('accounts.create')">Create
                </inertia-link>
            </div>
            <div class="flex-1 inline-block">
                <select v-model="co_id" class="w-32 rounded-md" label="company_id" @change="coch">
                    <option v-for="company in companies" :key="company.id" :value="company.id">{{company.name}}</option>
                </select>
            </div>
        </div>        


        <div class="">
            <table class="shadow-lg border mt-4 ml-8 rounded-xl">
                <thead>
                    <tr class="bg-indigo-100">
                        <th class="py-2 px-4 border">Account #</th>
                        <th class="py-2 px-4 border">Type</th>
                        <th class="py-2 px-4 border">Currency</th>
                        <th class="py-2 px-4 border">Branch</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data" :key="item.id">
                        <td class="py-1 px-4 border">{{item.name}}</td>
                        <td class="py-1 px-4 border">{{item.type}}</td>
                        <td class="py-1 px-4 border">{{item.currency}}</td>
                        <td class="py-1 px-4 border">{{item.branch_id}}</td>
                        <td class="py-1 px-4 border">
                            <inertia-link class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" :href="route('accounts.edit',item.id)">
                                <span>Edit</span>
                            </inertia-link>        
                            <button class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" @click="destroy(item.id)">
                                <span>Delete</span>
                            </button>        
                        </td>
                    </tr>
                </tbody>
            </table>
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
            data : Object,
            companies : Object,
        },

        data(){
            return {
                co_id : this.$page.props.co_id,
            }
        },

        methods: {

            destroy(id) {
            this.$inertia.delete(route('accounts.destroy', id))
            },

            coch() {
            this.$inertia.get(route('companies.coch', this.co_id))
            },
        },
    }
</script>
