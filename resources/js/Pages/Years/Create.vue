<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Years</h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white text-center">
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
              class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
              :href="route('years')"
              >Back
            </inertia-link>
          </div>
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Begin:</label>
            <datepicker
              v-model="form.begin"
              class="pr-2 pb-2 w-44 rounded-md leading-tight"
              label="begin"
            />
            <div v-if="errors.begin">{{ errors.begin }}</div>
          </div>

          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">End:</label>
            <datepicker
              v-model="form.end"
              class="pr-2 pb-2 w-44 rounded-md leading-tight"
              label="end"
            />
            <div v-if="errors.end">{{ errors.end }}</div>
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
              :disabled="isLoading"
            >
              Create Year
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Datepicker from "vue3-datepicker";
import format from "date-fns/format";

export default {
  components: {
    AppLayout,
    Datepicker,
  },

  props: {
    errors: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        begin: null,
        end: null,
      }),
      isLoading: false,
    };
  },

  methods: {
    submit() {
      this.isLoading = true;
      setTimeout(() => {
        this.isLoading = false;
      }, 4000);
      if (this.form.begin && this.form.end) {
        this.form.begin = format(this.form.begin, "yyyy-MM-dd");
        this.form.end = format(this.form.end, "yyyy-MM-dd");
        this.$inertia.post(route("years.store"), this.form);
      } else {
        alert("Insert Your Begin/End Date");
      }
    },
  },
};
</script>
