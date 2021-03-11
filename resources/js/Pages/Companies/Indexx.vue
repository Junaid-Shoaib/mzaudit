<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Banks
            </h2>
        </template>

        <div class="">
                <div class="">
                    <div class="">
                        <div class="">
                            <label>Select Bank:</label>
                            <select class='' v-model='bank'>
                                <option value='0' >Select Bank</option>
                                <option v-for='data in banks' :value='data.id' :key='data.id'>{{ data.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Branch:</label>
                            <select class='' v-model='branch' @change="see($event)">
                                <option value='0' >Select Branch</option>
                                <option v-for='data in branches' :value='data.id' :key='data.id' :name='data.address'>{{ data.address }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
 
 export default {
        components: {
            AppLayout,
        },

        props: ['data', 'data2'],

        data(){
            return {
                bank: 0,
                banks: this.data,
                branch: 0,
                branches: this.data2
            }
        },

        watch: {
            bank: function() {
                this.$inertia.get(route('braches', this.bank))
            },
        },

        methods: {
            see(event){
                let a = this.data2.find(d => d.id == event.target.value)
                alert("You see me: "+ a.address)

            }
        },

    }
</script>
