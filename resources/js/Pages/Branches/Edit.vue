<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Update Banks Branches
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-400 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto pb-2">
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
              class="
                border
                rounded-xl
                px-4
                py-1
                m-1
                bg-blue-400
                hover:text-white
                hover:bg-blue-600
              "
              :href="route('branches')"
              >Back
            </inertia-link>
          </div>
          <!-- <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="w-28 inline-block text-right mr-4">ID:</label>
          <select
            v-model="form.bank_id"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md"
            label="bank_id"
            placeholder="Enter type"
          >
            <option v-for="bank in banks" :key="bank.id" :value="bank.id">
              {{ bank.name }}
            </option>
          </select>
          <div v-if="errors.bank_id">{{ errors.bank_id }}</div>
        </div> -->
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Branch:</label>
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
            <!-- <input
            type="text"
            v-model="form.address"
            class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight"
            label="address"
          /> -->
            <div v-if="errors.address">{{ errors.address }}</div>
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
              class="
                border
                bg-green-500
                rounded-xl
                px-4
                py-2
                ml-4
                mt-4
                hover:text-white
                hover:bg-green-600
              "
              type="submit"
            >
              Update Branch
            </button>
          </div>
        </form>
      </div>
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
    branch: Object,
    banks: Object,
  },

  data() {
    return {
      form: {
        bank_id: this.branch.bank_id,
        address: this.branch.address,
      },
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("branches.update", this.branch.id), this.form);
    },
  },
};
</script>
