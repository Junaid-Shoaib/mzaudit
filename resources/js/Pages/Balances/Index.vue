<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Balances
        <div class="flex-1 inline-block float-right">
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
        <div class="flex-1 inline-block float-right">
          <multiselect
            class="rounded-md border border-black"
            placeholder="Select Company."
            v-model="co_id"
            track-by="id"
            label="name"
            :options="options"
            @update:model-value="coch"
          >
          </multiselect>
        </div>
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white text-center">
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
            :href="route('balances.create')"
            >Add Balances
          </inertia-link>

          <inertia-link
            v-if="dataEdit"
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
            :href="route('bal.edit')"
            >Edit
          </inertia-link>
        </div>
      </div>

      <div class="">
        <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
          <thead>
            <tr class="bg-gray-700 text-white text-centre font-bold">
              <th class="px-3 pt-3 pb-3 border">Account Number</th>
              <th class="px-3 pt-3 pb-3 border">Ledger</th>
              <th class="px-3 pt-3 pb-3 border">Statement</th>
              <th class="px-3 pt-3 pb-3 border">Confirmation</th>
              <!-- <th class="px-4 pt-4 pb-4 border">Actions</th> -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in balances.data" :key="item.id">
              <td Style="width: 40%" class="py-2 px-2 border text-left">
                {{ item.number }}
              </td>
              <td Style="width: 20%" class="py-2 px-2 border text-center">
                {{ item.ledger }}
              </td>
              <td Style="width: 20%" class="py-2 px-2 border text-center">
                {{ item.statement }}
              </td>
              <td Style="width: 20%" class="py-2 px-2 border text-center">
                {{ item.confirmation }}
              </td>
              <td class="border text-center" v-if="role">
              <button
                class="border bg-red-600 rounded-xl px-4 py-1 m-1"
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
          </tbody>
        </table>
        <paginator class="mt-6" :balances="balances" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Paginator from "@/Layouts/Paginator";
import Multiselect from "@suadelabs/vue3-multiselect";

export default {
  components: {
    AppLayout,
    Paginator,
    Multiselect,
  },

  props: {
    companies: Object,
    years: Object,
    balances: Object,
    dataEdit: Object,
    cochange: Object,
    role: Object,
  },

  data() {
    return {
      options: this.companies,
      co_id: this.cochange,
      role: this.role,
      // co_id: this.$page.props.co_id,
      yr_id: this.$page.props.yr_id,
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
