<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bank Balances
            </h2>
        </template>

    <div class="">
        <form @submit.prevent="submit">
        <div class="panel-body"> 
            <button class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"  @click.prevent="addRow" >Add row</button>
            <div v-if="isError">{{ firstError }}</div>
            <table class="table border">
                <thead class="">
                    <tr>                            
                        <th>Ledger</th>
                        <th>Statement</th>
                        <th>Confirmation</th>
                        <th>Company</th>
                        <th>Account</th>
                        <th>Year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for='(balance, index) in data' :key="balance.id">
                        <td>
                        <input v-model="balance.ledger" type="text" class="rounded-md w-36"/>
                        </td>
                        <td>
                        <input v-model="balance.statement" type="text" class="rounded-md w-36"/>
                        </td>
                        <td>
                        <input v-model="balance.confirmation" type="text" class="rounded-md w-36"/>
                        </td>
                        <td>
                        <input  v-model="balance.company_id" type="text" class="rounded-md w-36"/>
                        </td>
                        <td>
                        <select v-model="balance.account_id" class="rounded-md w-36">
                            <option v-for="account in accounts" :key="account.id" :value="account.id">{{account.branch}}</option>
                        </select>
                        </td>
                        <td>
                        <input  v-model="balance.year_id" type="text" class="rounded-md w-36"/>
                        </td>
                        <td>
                        <button  @click.prevent="deleteRow(index)" class="border bg-indigo-300 rounded-xl px-4 py-2 m-4" >Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Create Balance</button>
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
            data : Object,
            accounts : Object,
        },

        data() {
            return {
                balances: this.data,
                isError: false,
                firstError: '',
            }
        },

        watch: {
            errors: function(){
                if(this.errors){
                    this.firstError = this.errors[Object.keys(this.errors)[0]];
                    this.isError = true;
                }
            },
            data: function(){
                this.balances = this.data;
            }
        },

        methods: {

            submit() {
            this.$inertia.put(route('balances.update', this.balances[0]), {balances:this.balances})
            },

            addRow() {      
                this.data.push({
                    ledger: '',
                    statement: '',
                    confirmation: '',
                    company_id: '',
                    account_id: this.accounts[0].id,
                    year_id: '',
                    })
            },

            deleteRow(index){    
                this.data.splice(index,1);             
            },
        },
    }
</script>
