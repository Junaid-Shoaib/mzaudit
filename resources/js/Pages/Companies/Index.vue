<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Companies
        <div class="flex-1 inline-block float-right">
          <select
            v-model="co_id"
            class="max-w-md rounded-md"
            label="company_id"
            @change="coch"
          >
            <option
              v-for="company in data"
              :key="company.id"
              :value="company.id"
            >
              {{ company.name }}
            </option>
          </select>
        </div>
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="relative mt-5 ml-7">
      <inertia-link
        class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
        :href="route('companies.create')"
        >Create
      </inertia-link>
    </div>

    <div class="">
      <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
        <thead>
          <tr class="bg-indigo-100">
            <th class="px-4 pt-4 pb-4 border">Name</th>
            <th class="px-4 pt-4 pb-4 border">Address</th>
            <th class="px-4 pt-4 pb-4 border">Year End</th>
            <th class="px-4 pt-4 pb-4 border">Actions</th>
          </tr>
          <!-- Null Balance -->
        </thead>
        <tbody>
          <tr v-for="item in balances.data" :key="item.id">
            <td class="py-3 px-4 border text-left text-transform: uppercase">
              {{ item.name }}
            </td>
            <td class="py-3 px-4 border text-center">{{ item.address }}</td>
            <td class="py-3 px-4 border text-center">{{ item.fiscal }}</td>
            <td class="py-3 px-4 border text-center">
              <inertia-link
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                :href="route('companies.edit', item.id)"
              >
                <span>Edit</span>
              </inertia-link>
              <button
                class="border bg-red-500 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
                v-if="item.delete"
              >
                <span>Delete</span>
              </button>
            </td>
          </tr>
          <tr v-if="balances.data.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
          </tr>
        </tbody>
      </table>
      <paginator class="mt-6" :balances="balances" />
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Paginator from "@/Layouts/Paginator";

export default {
  components: {
    AppLayout,
    Paginator,
  },

  props: {
    data: Object,
    balances: Object,
  },

  data() {
    return { co_id: this.$page.props.co_id };
  },

  methods: {
    destroy(id) {
      this.$inertia.delete(route("companies.destroy", id));
    },
    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },
  },
};
</script>
