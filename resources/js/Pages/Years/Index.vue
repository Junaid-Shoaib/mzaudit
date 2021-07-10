<template>
  <app-layout>
    <template #header>
      <div class="flex-row">
        <!-- <div class="flex-1 inline-block"> -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Years
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
          </div>
        </h2>
      </div>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>

    <div class="">
      <form @submit.prevent="form.get(route('years.create'))">
        <button
          class="border bg-indigo-300 rounded-xl px-4 py-1 m-1 ml-8 mt-4"
          type="submit"
          :disabled="form.processing"
        >
          Add Year
        </button>
        <div class="">
          <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
            <thead>
              <tr class="bg-indigo-100 text-centre font-bold">
                <th class="px-4 pt-4 pb-4 border">Begin</th>
                <th class="px-4 pt-4 pb-4 border">End</th>
                <th class="px-4 pt-4 pb-4 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in balances.data" :key="item.id">
                <td class="py-1 px-4 border text-center">{{ item.begin }}</td>
                <td class="py-1 px-4 border text-center">{{ item.end }}</td>
                <td class="py-1 px-4 border text-center">
                  <inertia-link
                    class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                    :href="route('years.edit', item.id)"
                  >
                    <span>Edit</span>
                  </inertia-link>

                  <button
                    type="button"
                    class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                    @click="destroy(item.id)"
                    v-if="item.delete"
                  >
                    <span>Delete</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <paginator class="mt-6" :balances="balances" />
        </div>
      </form>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Paginator from "@/Layouts/Paginator";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    Paginator,
  },

  props: {
    balances: Object,
    companies: Object,
  },
  setup(props) {
    const form = useForm({});
    return { form };
  },

  data() {
    return {
      co_id: this.$page.props.co_id,
    };
  },

  methods: {
    destroy(id) {
      this.$inertia.delete(route("years.destroy", id));
    },

    coch() {
      this.$inertia.get(route("companies.coch", this.co_id));
    },
  },
};
</script>
