<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Bank Balances
      </h2>
    </template>
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
            :href="route('balances')"
            >Back
          </inertia-link>
        </div>
      </div>
      <div class="relative mt-5 flex-row border-t border-b border-gray-200">
        <form @submit.prevent="form.post(route('balances.store'))">
          <div class="">
            <div v-if="isError">{{ firstError }}</div>

            <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
              <thead>
                <tr class="bg-gray-700 text-white text-centre font-bold">
                  <th class="px-3 pt-3 pb-3 border">Ledger</th>
                  <th class="px-3 pt-3 pb-3 border">Account</th>
                  <th class="px-3 pt-3 pb-3 border">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(balance, index) in form.balances" :key="balance.id">
                  <td class="w-5/12">
                    <input
                      v-model="balance.ledger"
                      type="number"
                      class="rounded-md w-full"
                    />
                  </td>
                  <!-- <td>
                  <input
                    v-model="balance.statement"
                    type="text"
                    class="rounded-md w-36"
                  />
                </td>
                <td>
                  <input
                    v-model="balance.confirmation"
                    type="text"
                    class="rounded-md w-36"
                  />
                </td> -->
                  <td class="w-5/12">
                    <multiselect
                      class="w-full rounded-md border border-black"
                      placeholder="Select Account Number."
                      v-model="balance.account_id"
                      track-by="id"
                      label="branch"
                      :options="options"
                    >
                    </multiselect>
                    <!-- <select
                    v-model="balance.account_id"
                    class="rounded-md w-full"
                  >
                    <option
                      v-for="account in accounts"
                      :key="account.id"
                      :value="account.id"
                    >
                      {{ account.branch }}
                    </option>
                  </select> -->
                  </td>
                  <td class="w-2/12">
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
              Add More Balances
            </button>

            <button
              type="submit"
              class="
                border
                bg-green-500
                hover:text-white
                hover:bg-green-600
                rounded-xl
                px-6
                py-1
                m-1
              "
              :disabled="form.processing"
            >
              Save
            </button>
          </div>
        </form>
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
    accounts: Array,
  },

  data() {
    return {
      options: this.accounts,
    };
  },

  setup(props) {
    const form = useForm({
      balances: [
        {
          ledger: "",
          statement: "",
          confirmation: "",
          account_id: props.accounts[0],
          //   account_id: props.accounts[0].id,
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
      this.form.balances.push({
        ledger: "",
        statement: "",
        confirmation: "",
        account_id: this.accounts[0],
      });
    },

    deleteRow(index) {
      this.form.balances.splice(index, 1);
    },
  },
};
</script>
