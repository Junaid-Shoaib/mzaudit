<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Companies
            </h2>
        </template>
        <div class="">
            <form @submit.prevent="submit">
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <inertia-link class="border bg-indigo-300 rounded-xl px-4 py-1 m-1" :href="route('companies')">Back
                    </inertia-link>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Name:
                    <input type="text" v-model="form.name" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="name"/>
                    <div v-if="errors.name">{{ errors.name }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Address:
                    <input type="text" v-model="form.address" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="address"/>
                    <div v-if="errors.address">{{ errors.address }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Email:
                    <input type="text" v-model="form.email" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="email"/>
                    <div v-if="errors.email">{{ errors.email }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Web:
                    <input type="text" v-model="form.web" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="web"/>
                    <div v-if="errors.web">{{ errors.web }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Phone:
                    <input type="text" v-model="form.phone" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="phone"/>
                    <div v-if="errors.phone">{{ errors.phone }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">Year End:
                    <select v-model="form.fiscal" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="fiscal">
                        <option value="March">March</option>
                        <option value="June">June</option>
                        <option value="September">September</option>
                        <option value="December">December</option>
                    </select>
                    <div v-if="errors.fiscal">{{ errors.fiscal }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">Incorporation:
                    <input type="text" v-model="form.incorp" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="incorp"/>
                    <div v-if="errors.incorp">{{ errors.incorp }}</div>
                </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Update Company</button>
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
            company : Object,
//            types : Object,  
        },

        data() {
            return {
                form: {
                    name: this.company.name,
                    address: this.company.address,
                    email: this.company.email,
                    web: this.company.web,
                    phone: this.company.phone,
                    fiscal: this.company.fiscal,
                    incorp: this.company.incorp,
                },
            }
        },

        methods: {
            submit() {
            this.$inertia.put(route('companies.update', this.company.id), this.form)
            }, 
        },

    }
</script>
