<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Bank Accounts
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-400 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto pb-2">
      <div class="relative mt-5 ml-7 flex-row">
        <div class="flex-1 inline-block">
          <inertia-link
            class="
              border
              bg-blue-400
              rounded-xl
              px-4
              py-1
              m-1
              hover:text-white
              hover:bg-blue-600
            "
            :href="route('accounts')"
            >Back
          </inertia-link>
          <inertia-link
            class="
              border
              bg-blue-400
              hover:text-white
              hover:bg-blue-600
              rounded-xl
              px-4
              py-1
              m-1
            "
            :href="route('banks.create', 'accounts')"
            >Create Bank
          </inertia-link>
          <inertia-link
            class="
              border
              bg-blue-400
              rounded-xl
              px-4
              py-1
              m-1
              hover:text-white
              hover:bg-blue-600
            "
            :href="route('branches.create', 'accounts')"
            >Create Branch
          </inertia-link>
        </div>
      </div>

      <div class="relative mt-5 flex-row border-t border-b border-gray-200">
        <div v-if="isError">{{ firstError }}</div>
        <form @submit.prevent="form.post(route('accounts.store'))">
          <div class="">
            <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
              <thead>
                <tr class="bg-gray-700 text-white text-centre font-bold">
                  <th class="px-3 pt-3 pb-3 border">Account Number</th>
                  <th class="px-3 pt-3 pb-3 border">Branch</th>
                  <th class="px-3 pt-3 pb-3 border">Type</th>
                  <th class="px-3 pt-3 pb-3 border">Currency</th>
                  <th class="px-3 pt-3 pb-3 border">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(account, index) in form.accounts" :key="account.id">
                  <td class="w-3/12">
                    <input
                      v-model="account.name"
                      type="number"
                      class="rounded-md w-full"
                    />
                  </td>
                  <td class="w-4/12 rounded-md">
                    <multiselect
                      class="w-full rounded-md border border-black"
                      placeholder="Select Branch."
                      v-model="account.branch_id"
                      track-by="id"
                      label="address"
                      :options="options"
                    >
                    </multiselect>
                    <!-- <select v-model="account.branch_id" class="rounded-md w-full">
                    <option
                      v-for="branch in branches"
                      :key="branch.id"
                      :value="branch.id"
                    >
                      {{ branch.name }} -
                      {{ branch.address }}
                    </option>
                  </select> -->
                  </td>
                  <td class="3/12">
                    <select v-model="account.type" class="rounded-md w-full">
                      <option>CURRENT</option>
                      <option>SAVING</option>
                    </select>
                  </td>
                  <td class="2/12">
                    <select
                      v-model="account.currency"
                      class="rounded-md w-full"
                    >
                      <option>PKR</option>
                      <option>$</option>
                      <option>USD</option>
                      <option>EUR</option>
                    </select>
                  </td>
                  <td>
                    <button
                      type="button"
                      @click.prevent="deleteRow(index)"
                      class="
                        border
                        bg-red-500
                        rounded-xl
                        px-4
                        py-2
                        m-1
                        hover:text-white
                        hover:bg-red-600
                      "
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
              class="
                border
                bg-blue-400
                rounded-xl
                px-4
                py-1
                m-1
                hover:text-white
                hover:bg-blue-600
              "
              type="button"
              @click.prevent="addRow"
            >
              Add More Accounts
            </button>

            <button
              type="submit"
              class="
                border
                bg-green-500
                rounded-xl
                px-6
                py-1
                m-1
                hover:text-white
                hover:bg-green-600
              "
              :disabled="form.processing"
            >
              Save
            </button>
          </div>
        </form>
      </div>

      <div class="">
        <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
          <thead>
            <tr class="bg-gray-700 text-white text-centre font-bold">
              <th class="px-3 pt-3 pb-3 border">Account Number</th>
              <th class="px-3 pt-3 pb-3 border">Branch</th>
              <th class="px-3 pt-3 pb-3 border">Type</th>
              <th class="px-3 pt-3 pb-3 border">Currency</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in balances" :key="item.id">
              <td class="py-2 px-2 border text-left">{{ item.name }}</td>
              <td class="py-2 px-2 border text-center">
                {{ item.branches }}
              </td>
              <td class="py-2 px-2 border text-center">{{ item.type }}</td>
              <td class="py-2 px-2 border text-center">
                {{ item.currency }}
              </td>
            </tr>
            <!-- Null Balance -->
            <tr v-if="balances.items === 0">
              <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Multiselect,
  },

  props: {
    errors: Object,
    branches: Array,
    balances: Object,
  },

  data() {
    return {
      options: this.branches,
    };
  },

  setup(props) {
    const form = useForm({
      accounts: [
        {
          branch_id: props.branches[0],
          type: "CURRENT",
          name: null,
          currency: "PKR",
        },
      ],
    });
    return { form };
  },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },
  },

  methods: {
    addRow() {
      this.form.accounts.push({
        branch_id: this.branches[0],
        type: "CURRENT",
        name: null,
        currency: "PKR",
      });
    },

    deleteRow(index) {
      this.form.accounts.splice(index, 1);
    },
  },
};
</script>
