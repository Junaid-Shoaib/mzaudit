<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Indexy
            </h2>
        </template>

        <div class="">
            <form @submit.prevent="submit">
            <div class="panel-body"> 
                <button class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"  @click.prevent="addRow" >Add row</button>
                <table class="table border">
                    <thead class="">
                        <tr>                            
                            <th>Dr</th>
                            <th>Cr</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='(balance, index) in form.balances' :key="balance.id">
                            <td>
                            <input v-model="balance.dr" type="text" @change="dchange(index)" class="rounded-md w-36"/>
                            </td>
                            <td>
                            <input v-model="balance.cr" type="text" @change="cchange(index)" class="rounded-md w-36"/>
                            </td>
                            <td>
                            <button  @click.prevent="deleteRow(index)" class="border bg-indigo-300 rounded-xl px-4 py-2 m-4" >Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <input v-model="outsiders.dr" type="text" class="rounded-md w-36"/>
                            </td>
                            <td>
                            <input v-model="outsiders.cr" type="text" class="rounded-md w-36"/>
                            </td>
                            <td>
                                ---
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


        data() {
            return {
                form: this.$inertia.form({
                    balances: [{
                        dr: '',
                        cr: '',
                    }],
                    dtotal : 0,
                    ctotal : 0,
                }),
                outsiders: [{
                    dr: '',
                    cr: '',
                }],
            }
        },

        watch : {
            form : {
                deep: true,
                handler(){
                console.log('hello')
                }
            }
        },

        methods: {

            submit() {
                var i;
                for(i=0;i<3;i++){
                    console.log(this.$refs.eldr_0.value)
                }
            },

            dchange(index){
                let inst = this.form.balances[index]
                inst.cr = 'I see you'
                console.log(inst.dr)
            },

            cchange(index){
                let inst = this.form.balances[index]
                inst.dr = 'I SEE YOU TOO!'
                console.log(inst.cr)
            },

            addRow() {      
                this.form.balances.push({
                    dr: '',
                    cr: '',
                    })
            },

            deleteRow(index){    
                this.form.balances.splice(index,1);             
            },
        },

    }
</script>
