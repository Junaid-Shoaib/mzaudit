<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Banks Confirmations
            </h2>
        </template>
        <div class="">
            <form @submit.prevent="submit">
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Sent:</label>
                    <input type="text" v-model="form.sent" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="sent"/>
                    <div v-if="errors.sent">{{ errors.sent }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Reminder 1:</label>
                    <input type="text" v-model="form.remind_first" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="remind_first"/>
                    <div v-if="errors.remind_first">{{ errors.remind_first }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Reminder 2:</label>
                    <input type="text" v-model="form.remind_second" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="remind_second"/>
                    <div v-if="errors.remind_second">{{ errors.remind_second }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Received:</label>
                    <input type="text" v-model="form.received" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="received"/>
                    <div v-if="errors.received">{{ errors.received }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Company:</label>
                    <input type="text" v-model="form.company_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="company_id"/>
                    <div v-if="errors.company_id">{{ errors.company_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Bank:</label>
                    <select v-model="form.branch_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md" label="branch_id" placeholder="Enter type">
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{branch.name}}</option>
                    </select>
                    <div v-if="errors.branch_id">{{ errors.branch_id }}</div>
                </div>
                <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
                    <label class="w-28 inline-block text-right mr-4">Year:</label>
                    <input type="text" v-model="form.year_id" class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight" label="year_id"/>
                    <div v-if="errors.year_id">{{ errors.year_id }}</div>
                </div>
                <div class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center">
                    <button class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4" type="submit">Update Confirmation</button>
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
            confirmation : Object,
            branches : Object,  
        },

        data() {
            return {
                form: {
                    sent: this.confirmation.sent,
                    remind_first: this.confirmation.remind_first,
                    remind_second: this.confirmation.remind_second,
                    received: this.confirmation.received,
                    company_id: this.confirmation.company_id,
                    branch_id: this.confirmation.branch_id,
                    year_id: this.confirmation.year_id,
                },
            }
        },

        methods: {
            submit() {
            this.$inertia.put(route('confirmations.update', this.confirmation.id), this.form)
            }, 
        },

    }
</script>
