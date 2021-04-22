<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Balances
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>

    <div class="relative mt-5 ml-7 flex-row">
      <div class="flex-1 inline-block">
        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('balances.create')"
          >Create
        </inertia-link>
      </div>
      <div class="flex-1 inline-block">
        <select
          v-model="co_id"
          class="max-w-md rounded-md"
          label="company_id"
          @change="coch"
        >
          <option
            v-for="company in companies"
            :key="company.id"
            :value="company.id"
          >
            {{ company.name }}
          </option>
        </select>
      </div>

      <!-- <div class="flex-1 inline-block">
        <select v-model="name" class="max-w-md rounded-md" label="name">
          <option
            v-for="company in account"
            :key="company.id"
            :value="company.id"
          >
            {{ company.name }}
          </option>
        </select>
      </div> -->

      <div class="flex-1 inline-block">
        <select
          class="max-w-md rounded-md"
          v-model="yr_id"
          @change="yrch"
          label="yr_id"
        >
          <option v-for="year in years" :key="year.id" :value="year.id">
            {{ year.end }}
          </option>
        </select>
      </div>
    </div>

    <div class="">
      <table class="shadow-lg border mt-4 ml-8 rounded-xl">
        <thead>
          <tr class="bg-indigo-100">
            <th class="py-2 px-4 border">Account</th>
            <th class="py-2 px-4 border">Ledger</th>
            <th class="py-2 px-4 border">Statement</th>
            <th class="py-2 px-4 border">Confirmation</th>
            <th class="py-2 px-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id">
            <td class="py-1 px-4 border">{{ item.number }}</td>
            <td class="py-1 px-4 border">{{ item.ledger }}</td>
            <td class="py-1 px-4 border">{{ item.statement }}</td>
            <td class="py-1 px-4 border">{{ item.confirmation }}</td>
            <td class="py-1 px-4 border">
              <inertia-link
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                :href="route('balances.edit', item.id)"
              >
                <span>Edit</span>
              </inertia-link>
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
              >
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
import AppLayout from "@/Layouts/AppLayout";
// import Datepicker from "vue3-datepicker";
// // import format from "date-fns/format";
// import moment from "moment";

export default {
  components: {
    AppLayout,
  },

  props: {
    data: Object,
    companies: Object,
    years: Object,

    // account: Object,
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      yr_id: this.$page.props.yr_id,
      // account_id: this.account[0].id,
    };
  },

  methods: {
    destroy(id) {
      this.$inertia.delete(route("balances.destroy", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },

    yrch() {
      this.$inertia.get(route("companies.yrch", this.yr_id));
    },
  },
};
</script>
