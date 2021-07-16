<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Accounts
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
        <button
          v-on:click="ch"
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
        >
          Create
        </button>
        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('account.edit')"
        >
          <span>Edit</span>
        </inertia-link>

        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('banks.create', 'accounts')"
          >Create Bank
        </inertia-link>
        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('branches.create', 'accounts')"
          >Create Branch
        </inertia-link>
      </div>
    </div>

    <div
      v-if="seen"
      class="relative mt-5 flex-row border-t border-b border-gray-200"
    >
      <form @submit.prevent="form.post(route('accounts.store'))">
        <div v-if="isError">{{ firstError }}</div>
        <div class="">
          <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
            <thead>
              <tr class="bg-indigo-100 text-centre font-bold">
                <th class="px-4 pt-4 pb-4 border">Branch</th>
                <th class="px-4 pt-4 pb-4 border">Account Number</th>
                <th class="px-4 pt-4 pb-4 border">Type</th>
                <th class="px-4 pt-4 pb-4 border">Currency</th>
                <th class="px-4 pt-4 pb-4 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(account, index) in form.accounts" :key="account.id">
                <td class="w-4/12">
                  <select v-model="account.branch_id" class="rounded-md w-full">
                    <option
                      v-for="branch in branches"
                      :key="branch.id"
                      :value="branch.id"
                    >
                      {{ branch.name }} - {{ branch.address }}
                    </option>
                  </select>
                </td>
                <td class="w-3/12">
                  <input
                    v-model="account.name"
                    type="text"
                    class="rounded-md w-full"
                  />
                </td>
                <td class="3/12">
                  <select v-model="account.type" class="rounded-md w-full">
                    <option>CURRENT</option>
                    <option>SAVING</option>
                    <option>ASAAN</option>
                  </select>
                </td>
                <td class="2/12">
                  <select v-model="account.currency" class="rounded-md w-full">
                    <option>USD</option>
                    <option>$</option>
                    <option>PKR</option>
                    <option>EUR</option>
                  </select>
                </td>
                <td>
                  <button
                    @click.prevent="deleteRow(index)"
                    class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- <div v-if="isError">{{ firstError }}</div> -->
        <div
          class="
            relative
            mt-5
            mb-5
            ml-7
            flex-row
            bg-gray-100
            justify-start
            items-center
          "
        >
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
            @click.prevent="addRow"
          >
            Add row
          </button>

          <button
            class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
            type="submit"
            :disabled="form.processing"
          >
            Create Accounts
          </button>
        </div>
      </form>
    </div>

    <div class="">
      <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
        <thead>
          <tr class="bg-indigo-100 text-centre font-bold">
            <th class="px-4 pt-4 pb-4 border">Account #</th>
            <th class="px-4 pt-4 pb-4 border">Type</th>
            <th class="px-4 pt-4 pb-4 border">Currency</th>
            <th class="px-4 pt-4 pb-4 border">Branch</th>
            <th class="px-4 pt-4 pb-4 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in balances" :key="item.id">
            <td class="py-3 px-4 border text-left">{{ item.name }}</td>
            <td class="py-3 px-4 border text-center">{{ item.type }}</td>
            <td class="py-3 px-4 border text-center">
              {{ item.currency }}
            </td>
            <td class="py-1 px-4 border text-center">
              {{ item.branches }}
            </td>
            <td class="py-3 px-4 border text-center">
              <!-- <button
                class="border bg-red-500 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
                v-if="item.delete"
              >
                <span>Delete</span>
              </button> -->
            </td>
          </tr>
          <!-- Null Balance -->
          <tr v-if="balances.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
  },

  props: {
    balances: Object,
    companies: Object,
    years: Object,
    branches: Object,
  },

  setup(props) {
    const form = useForm({
      accounts: [
        {
          branch_id: props.branches[0].id,
          name: null,
          type: null,
          currency: null,
        },
      ],
    });

    return { form };
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
      yr_id: this.$page.props.yr_id,
      seen: false,
    };
  },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },
  },

  //   mounted() {
  //     // seen() {
  //     this.seen = false;
  //     // },
  //   },
  methods: {
    ch() {
      if (this.seen == true) {
        this.seen = false;
      } else {
        this.seen = true;
      }
    },

    // store() {
    //   this.$inertia.form.accounts.name = null;
    // },

    addRow() {
      this.form.accounts.push({
        branch_id: this.branches[0].id,
        name: "",
        type: "",
        currency: "",
      });
    },

    deleteRow(index) {
      this.form.accounts.splice(index, 1);
    },

    destroy(id) {
      this.$inertia.delete(route("accounts.destroy", id));
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
