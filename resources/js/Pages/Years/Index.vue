<template>
  <app-layout>
    <template #header>
      <div class="flex-row">
        <!-- <div class="flex-1 inline-block"> -->
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Years
          <div class="flex-1 inline-block float-right">
            <multiselect
              class="w-full rounded-md border border-black"
              placeholder="Select Company."
              v-model="co_id"
              track-by="id"
              label="name"
              :options="options"
              @update:model-value="coch"
            >
            </multiselect>
            <!-- <select
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
            </select> -->
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
          class="
            border
            bg-blue-400
            rounded-xl
            px-4
            py-1
            m-1
            ml-8
            mt-4
            hover:text-white
            hover:bg-blue-600
          "
          type="submit"
          :disabled="form.processing"
        >
          Add Year
        </button>
        <div class="">
          <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
            <thead>
              <tr class="bg-gray-700 text-white text-centre font-bold">
                <th class="px-3 pt-3 pb-3 border">Begin</th>
                <th class="px-3 pt-3 pb-3 border">End</th>
                <th class="px-3 pt-3 pb-3 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in balances.data" :key="item.id">
                <td class="py-2 px-2 border text-center">{{ item.begin }}</td>
                <td class="py-2 px-2 border text-center">{{ item.end }}</td>
                <td class="py-2 px-2 border text-center">
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
                    :href="route('years.edit', item.id)"
                  >
                    <span>Edit</span>
                  </inertia-link>

                  <inertia-link
                    class="
                      border
                      bg-red-500
                      rounded-xl
                      px-4
                      py-1
                      m-1
                      hover:text-white
                      hover:bg-red-600
                    "
                    @click="destroy(item.id)"
                    v-if="item.delete"
                  >
                    <span>Delete</span>
                  </inertia-link>
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
import Multiselect from "@suadelabs/vue3-multiselect";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    Paginator,
    Multiselect,
  },

  props: {
    balances: Object,
    companies: Object,
    cochange: Object,
  },
  setup(props) {
    const form = useForm({});
    return { form };
  },

  data() {
    return {
      options: this.companies,
      co_id: this.cochange,
      // co_id: this.$page.props.co_id,
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
