<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Banks</h2>
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
            :href="route('banks')"
            >Back
          </inertia-link>
        </div>
        <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
          <label class="w-28 inline-block text-right mr-4">Name:</label>
          <input
            type="text"
            v-model="form.name"
            class="uppercase pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight"
            label="name"
          />
          <div v-if="errors.name">{{ errors.name }}</div>
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
            Update Bank
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
    bank: Object,
  },

  data() {
    return {
      form: {
        name: this.bank.name,
      },
    };
  },

  methods: {
    submit() {
      this.$inertia.put(route("banks.update", this.bank.id), this.form);
    },
  },
};
</script>
