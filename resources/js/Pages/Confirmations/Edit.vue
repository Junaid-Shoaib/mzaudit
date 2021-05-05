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
          class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center"
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
              <th>Sent</th>
              <th>Create Conformation/Date</th>
              <th>Reminder</th>
              <th>Received</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(confirm, index) in data" :key="confirm.id">
              <!-- v-for="(confirm, index) in data" :key="confirm.id" -->
              <td>
                <input
                  v-model="confirm.sent"
                  type="date"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
                <!-- <datepicker
                  v-model="confirm.sent"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                /> -->
              </td>
              <!-- <td>
                <input
                  v-model="confirm.sent"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                  type="text"
                />
              </td> -->
              <td>
                <!-- :upper-limit="upper"
                  :lower-limit="lower" -->
                <input
                  v-model="confirm.confirm_create"
                  type="date"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
                <!-- <datepicker
                  v-model="confirm.confirm_create"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                /> -->
              </td>
              <td>
                <input
                  v-model="confirm.reminder"
                  type="date"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                />
                <!-- <datepicker
                  v-model="confirm.reminder"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                /> -->
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
              <!-- <td>
                <datepicker
                  v-model="confirm.received"
                  :upper-limit="upper"
                  :lower-limit="lower"
                  class="pr-2 pb-2 w-44 rounded-md leading-tight"
                  label="received"
                />
              </td> -->
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
          class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex justify-start items-center"
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
      // balances: {
      // sent: this.data[0].sent == null ? null : new Date(this.data[0].sent),
      // reminder:
      //   this.data[0].reminder == null ? null : new Date(this.data[0].reminder),
      // confirm_create:
      //   this.data[0].confirm_create == null
      //     ? null
      //     : new Date(this.data[0].confirm_create),
      // received:
      //   this.data[0].received == null ? null : new Date(this.data[0].received),
      // company_id: this.data[0].company_id,
      // branch_id: this.data[0].branch_id,
      // year_id: this.data[0].year_id,
      // },
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
    // doFormat($item) {
    //   var $i = format($item, "yyyy-MM-dd");
    //   return $i;
    // },
    // },
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