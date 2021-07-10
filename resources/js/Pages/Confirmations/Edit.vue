<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Banks Confirmations
      </h2>
    </template>
    <div class="">
      <form @submit.prevent="submit">
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
            :href="route('confirmations')"
            >Back
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
              <th>Bank</th>
              <th>Create Date</th>
              <th>Sent Date</th>
              <th>Reminder Date</th>
              <th>Received Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(confirm, index) in data" :key="confirm.id">
              <td>
                <input
                  v-model="confirm.name"
                  type="text"
                  class="pr-2 pb-2 w-60 rounded-md leading-tight"
                  readonly
                />
              </td>
              <td>
                <input
                  v-model="confirm.confirm_create"
                  type="date"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
              </td>
              <td>
                <input
                  v-model="confirm.sent"
                  type="date"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
              </td>
              <td>
                <input
                  v-model="confirm.reminder"
                  type="date"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
              </td>
              <td>
                <input
                  v-model="confirm.received"
                  type="date"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
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
          >
            Update Confirmation
          </button>
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Datepicker from "vue3-datepicker";

export default {
  components: {
    AppLayout,
    Datepicker,
  },

  props: {
    errors: Object,
    data: Object,
    branches: Object,
    year: Object,
  },

  data() {
    return {
      balances: this.data,
      upper: new Date(this.year.end),
      lower: new Date(this.year.begin),
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("confirmations.update", this.balances[0]), {
        balances: this.balances,
      });
    },

    addRow() {
      this.balances.push({
        sent: null,
        confirm_create: null,
        reminder: null,
        received: null,
      });
    },
    deleteRow(index) {
      this.balances.splice(index, 1);
    },
  },
};
</script>
