<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Balances
        <div class="flex-1 inline-block float-right">
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
        <!-- <inertia-link
          v-for="items in data"
          :key="items.id"
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('balances.edit', items.id)"
          >Edit
        </inertia-link> -->

        <!-- :v-if="items > 2" -->
        <!-- v-for="items in data"
          :key="items.id" -->

        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('bal.edit')"
          >Edit
        </inertia-link>
      </div>
    </div>

    <div class="">
      <!-- <div class=""> -->
      <!-- <table class="shadow-lg border rounded-xl mt-4 ml-12 rounded-xl w-11/12"> -->
      <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
        <thead>
          <tr class="bg-indigo-100 text-centre font-bold">
            <th class="px-4 pt-4 pb-4 border">Account</th>
            <th class="px-4 pt-4 pb-4 border">Ledger</th>
            <th class="px-4 pt-4 pb-4 border">Statement</th>
            <th class="px-4 pt-4 pb-4 border">Confirmation</th>
            <th class="px-4 pt-4 pb-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in balances.data" :key="item.id">
            <!-- class="bg-indigo-100 text-center font-bold" -->
            <td class="py-1 px-4 border text-left">{{ item.number }}</td>
            <td class="border text-center">{{ item.ledger }}</td>
            <td class="border text-center">{{ item.statement }}</td>
            <td class="border text-center">{{ item.confirmation }}</td>
            <td class="border text-center">
              <!-- <inertia-link
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                :href="route('balances.edit', item.id)"
              >
                <spa  n>Edit</span>
              </inertia-link> -->
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
              >
                <span>Delete</span>
              </button>
            </td>
          </tr>

          <!-- Null Balance -->
          <tr v-if="balances.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
          </tr>
          <!-- <tr> -->
          <!-- Pagination Next & Back -->
          <!-- <div class="p-5 flex">
              <inertia-link
                class="border bg-gray-300 rounded-xl px-4 py-1 m-1"
                :href="balances.prev_page_url"
                >Previous</inertia-link
              >


              <inertia-link
                class="border bg-gray-300 rounded-xl px-4 py-1 m-1"
                :href="balances.next_page_url"
                >Next</inertia-link
              >
            </div> -->
          <!-- <pagination class="mt-6" :links="balances.links" /> -->
          <!-- </tr> -->
        </tbody>
      </table>
      <paginator class="mt-6" :balances="balances" />
    </div>

    <!-- <pagination class="mt-6" :links="balances.data.links" /> -->
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
// import Pagination from "@/Components/Pagination";
import Paginator from "@/Layouts/Paginator";
// import Pagination from "@/Layouts/Pagination";
export default {
  components: {
    AppLayout,
    Paginator,
  },

  props: {
    // data: Object,
    companies: Object,
    years: Object,
    // balances: Object,
    balances: Object,
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
