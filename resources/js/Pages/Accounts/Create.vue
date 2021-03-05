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
                    <label class="w-28 inline-block text-right mr-4">Company:</label>
                    <input type="text" v-model="form.company_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="company_id"/>
                    <div v-if="errors.company_id">{{ errors.company_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Branch:</label>
                    <select v-model="form.branch_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="branch_id" placeholder="Enter type">
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{branch.name}} - {{branch.address}}</option>
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
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Create Branch</button>
                </div>
            </form>
        </div>

        <div class="panel-body"> 
            <button class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"  @click="addRow" >Add row</button>
            <table class="table border">
                <thead class="">
                    <tr>                            
                        <th>Company</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Currency</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for='(account, index) in accounts' :key="account.id">                            
                        <td>
                        <input  v-model="account.company_id"  type="text" />
                        </td>
                        <td>
                        <select v-model="account.branch_id" >
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{branch.name}} - {{branch.address}}</option>
                        </select>
                        </td>
                        <td>
                        <input v-model="account.name"  type="text"/>
                        </td>
                        <td>
                        <input v-model="account.type"  type="text"/>
                        </td>
                        <td>
                        <input v-model="account.currency"  type="text"/>
                        </td>
                        <td>
                        <button  @click="deleteRow(index)" class="border bg-indigo-300 rounded-xl px-4 py-2 m-4" >Delete</button>
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
            errors : Object,
            branches : Object,
            banks : Object,
        },

        data() {
            return {
                form: this.$inertia.form({
                    company_id: null,
                    branch_id: this.branches[0].id,
                    name: null,
                    type: null,
                    currency: null,
                }),

                accounts: [{
                    company_id: '',
                    branch_id: this.branches[0].id,
                    name: '',
                    type: '',
                    currency: '',
                }],
            }
        },

        methods: {

            submit() {
            this.$inertia.post(route('accounts.store'), this.accounts)
            },

            addRow() {      
                this.accounts.push({
                    company_id: '',
                    branch_id: this.branches[0].id,
                    name: '',
                    type: '',
                    currency: '',
                    })
            },

            deleteRow(index){    
                this.accounts.splice(index,1);             
            },
        },
    }
</script>
