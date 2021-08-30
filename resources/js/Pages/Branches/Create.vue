<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bank Branches
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-400 text-white">
      {{ $page.props.flash.success }}
    </div>

    <div class="">
      <form @submit.prevent="form.post(route('branches.store'))">
        <div
          class="
            px-4
            py-2
            bg-gray-100
            border-t border-gray-200
            flex
            border
            justify-start
            items-center
          "
        >
          <inertia-link
            class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
            :href="route('branches')"
            >Back
          </inertia-link>
        </div>

        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="w-28 inline-block text-right mr-4">Bank:</label>

          <multiselect
            class="max-h-10 w-full lg:w-1/4 rounded-md border border-black"
            v-model="form.bank_id"
            placeholder="Select Branch."
            track-by="id"
            label="name"
            :options="options"
          >
          </multiselect>
          <!-- <select
            class="pr-2 pb-2 max-h-10 w-full lg:w-1/4 rounded-md"
            label="bank_id"
            v-model="form.bank_id"
          >
            <option v-for="bank in banks" :key="bank.id" :value="bank.id">
              {{ bank.name }}
            </option>
          </select> -->
          <!-- <div v-if="errors.bank_id">{{ errors.bank_id }}</div> -->
          <label class="w-28 inline-block text-right ml-7 mr-4"
            >Branch name and address:</label
          >
          <textarea
            v-model="form.address"
            rows="4"
            cols="100"
            class="
              pr-2
              pb-2
              w-full
              lg:w-1/4
              rounded-md
              leading-tight
              text-transform:
              capitalize
            "
            label="address"
          ></textarea>
          <button
            class="
              border
              bg-indigo-300
              rounded-xl
              px-8
              py-2
              ml-16
              my-5
              max-h-10
            "
            type="submit"
            :disabled="form.processing"
          >
            Save
          </button>
        </div>
        <div v-if="errors.address">{{ errors.address }}</div>
        <!-- </div> -->

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
        ></div>
      </form>
    </div>
    <div class="">
      <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
        <thead>
          <tr class="bg-indigo-100 text-centre font-bold">
            <th class="px-4 pt-4 pb-4 border">branches #</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in branches" :key="item.id">
            <td
              v-if="item.bank_id == form.bank_id['id']"
              class="py-3 px-4 border text-left"
            >
              {{ item.add }}
            </td>
          </tr>
          <tr v-if="branches.length === 0">
            <td class="border-t px-6 py-4" colspan="4">No Record found.</td>
          </tr>
        </tbody>
      </table>
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
    branches: Object,
    errors: Object,
    banks: Object,
    accounts: Object,
  },

  data() {
    return {
      options: this.banks,
    };
  },

  setup(props) {
    const form = useForm({
      address: null,
      accounts: props.accounts,
      bank_id: props.banks[0],
      //   bank_id: null,
    });
    return { form };
  },
};
</script>

