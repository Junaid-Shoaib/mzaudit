<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Accounts
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-400 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="">
      <form @submit.prevent="form.post(route('accounts.store'))">
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
          <inertia-link
            class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
            :href="route('accounts')"
            >Back
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

        <button
          class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
          @click.prevent="addRow"
        >
          Add row
        </button>

        <div v-if="isError">{{ firstError }}</div>
        <table class="table border">
          <thead class="">
            <tr>
              <th>Branch</th>
              <th>A/c Number</th>
              <th>Type</th>
              <th>Currency</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(account, index) in form.accounts" :key="account.id">
              <td>
                <select v-model="account.branch_id" class="rounded-md w-36">
                  <option
                    v-for="branch in branches"
                    :key="branch.id"
                    :value="branch.id"
                  >
                    {{ branch.name }} - {{ branch.address }}
                  </option>
                </select>
              </td>
              <td>
                <input
                  v-model="account.name"
                  type="text"
                  class="rounded-md w-36"
                />
              </td>
              <td>
                <select v-model="account.type" class="rounded-md w-36">
                  <option>CURRENT</option>
                  <option>SAVING</option>
                  <option>ASAAN</option>
                </select>
              </td>
              <td>
                <select v-model="account.currency" class="rounded-md w-36">
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
            :disabled="form.processing"
          >
            Create Accounts
          </button>
        </div>
      </form>
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
    errors: Object,
    branches: Object,
  },

  setup(props) {
    const form = useForm({
      accounts: [
        {
          branch_id: props.branches[0].id,
          name: "",
          type: "",
          currency: "",
        },
      ],
    });
    return { form };
  },
  // data() {
  //   return {
  //     form: this.$inertia.form({
  //       accounts: [
  //         {
  //           branch_id: this.branches[0].id,
  //           name: "",
  //           type: "",
  //           currency: "",
  //         },
  //       ],
  //     }),
  //     isLoading: false,
  //   };
  // },

  watch: {
    errors: function () {
      if (this.errors) {
        this.firstError = this.errors[Object.keys(this.errors)[0]];
        this.isError = true;
      }
    },
  },

  methods: {
    // submit() {
    //   this.isLoading = true;
    //   setTimeout(() => {
    //     this.isLoading = false;
    //   }, 4000);
    //   this.$inertia.post(route("accounts.store"), this.form.accounts);
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
  },
};
</script>
