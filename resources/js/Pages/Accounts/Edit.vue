<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Account
      </h2>
    </template>

    <div class="relative mt-5 ml-7 flex-row">
      <div class="flex-1 inline-block">
        <inertia-link
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
          :href="route('accounts')"
          >Back
        </inertia-link>
      </div>
    </div>

    <div class="relative mt-5 flex-row border-t border-b border-gray-200">
      <form @submit.prevent="submit">
        <div class="">
          <div v-if="isError">{{ firstError }}</div>
          <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
            <thead class="bg-indigo-100 text-centre font-bold">
              <tr>
                <th class="px-4 pt-4 pb-4 border">Branches</th>
                <th class="px-4 pt-4 pb-4 border">Account Number</th>
                <th class="px-4 pt-4 pb-4 border">Type</th>
                <th class="px-4 pt-4 pb-4 border">Currency</th>
                <!-- <th class="px-4 pt-4 pb-4 border">Action</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in data" :key="account.id">
                <td class="w-4/12">
                  <input
                    v-model="account.branches"
                    type="text"
                    readonly
                    class="rounded-md w-full my-2"
                  />
                </td>
                <td class="w-3/12">
                  <input
                    v-model="account.name"
                    type="number"
                    class="rounded-md w-full my-2"
                  />
                </td>
                <td class="w-3/12">
                  <select v-model="account.type" class="rounded-md w-full py-2">
                    <option>CURRENT</option>
                    <option>SAVING</option>
                  </select>
                </td>
                <td class="w-2/12">
                  <select
                    v-model="account.currency"
                    class="rounded-md w-full my-2"
                  >
                    <option>PKR</option>
                    <option>$</option>
                    <option>USD</option>
                    <option>EUR</option>
                  </select>
                </td>
                <!-- <td>
                  <select v-model="account.account_id" class="rounded-md w-36">
                    <option
                      v-for="account in accounts"
                      :key="account.id"
                      :value="account.id"
                    >
                      {{ account.branch }}
                    </option>
                  </select>
                </td> -->
              </tr>
            </tbody>
          </table>
        </div>
        <div
          class="
            px-4
            py-2
            bg-gray-100
            border-t border-gray-200
            flex
            justify-start
            items-center
          "
        >
          <button
            class="border bg-indigo-300 rounded-xl px-4 py-2 ml-4 mt-4"
            type="submit"
          >
            Update Account
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    AppLayout,
  },

  props: {
    errors: Object,
    data: Object,
  },

  data() {
    return {
      balances: this.data,
      isError: false,
      firstError: "",
    };
  },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },
    data: function () {
      this.balances = this.data;
    },
  },

  methods: {
    submit() {
      this.$inertia.put(route("accounts.update", this.balances[0]), {
        balances: this.balances,
      });
    },
    // doFormat($item) {
    //   var $i = format($item, "yyyy-MM-dd");
    //   return $i;
    // },

    addRow() {
      this.balances.push({
        name: null,
        type: null,
        currency: null,
        // account_id: this.accounts[0].id,
      });
    },

    deleteRow(index) {
      this.balances.splice(index, 1);
    },
  },
};
</script>
