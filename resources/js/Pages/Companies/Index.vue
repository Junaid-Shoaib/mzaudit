<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Companies
            </h2>
        </template>
        <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
            {{ $page.props.flash.success }}
        </div>
        <div class="relative mt-5 ml-7">
            <inertia-link class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" :href="route('companies.create')">Create
            </inertia-link>
        </div>        
        <div class="">
            <table class="shadow-lg border mt-4 ml-8 rounded-xl">
                <thead>
                    <tr class="bg-indigo-100">
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Address</th>
                        <th class="py-2 px-4 border">Year End</th>
                        <th class="py-2 px-4 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data" :key="item.id">
                        <td class="py-1 px-4 border">{{item.name}}</td>
                        <td class="py-1 px-4 border">{{item.address}}</td>
                        <td class="py-1 px-4 border">{{item.fiscal}}</td>
                        <td class="py-1 px-4 border">
                            <inertia-link class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" :href="route('companies.edit',item.id)">
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

        props: ['data'],

        data(){
            return {
            }
        },

        methods: {

            destroy(id) {
            this.$inertia.delete(route('companies.destroy', id))
            },

        },
    }
</script>
