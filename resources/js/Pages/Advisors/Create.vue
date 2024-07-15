<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Advisors
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-yellow-400 text-white text-center content-center">
      {{ $page.props.flash.success }}
    </div>

    <div class="max-w-7xl mx-auto pb-2">
      <div class="">
        <form @submit.prevent="form.post(route('advisors.store'))">
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
              :href="route('advisors')"
              >Back
            </inertia-link>
          </div>
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Name:</label>
            <input
              type="text"
              placeholder="Enter Your Name"
              v-model="form.name"
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
              label="name"
            />
          <div v-if="errors.name">{{ errors.name }}</div>
          </div>
          <div class="p-2 mr-2 mb-2 mt-4 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Address:</label>
            <textarea
              v-model="form.address"
              placeholder="Enter Your Address"
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
             <div v-if="errors.address">{{ errors.address }}</div>
          </div>
          <div class="p-2 mr-2 mb-2 ml-6 flex flex-wrap">
            <label class="w-28 inline-block text-right mr-4">Type:</label>
            <select
              v-model="form.type"
              class="pr-2 pb-2 w-full lg:w-1/4 rounded-md leading-tight"
              label="type"
            >
              <option selected value="tax">Tax Advisor</option>
              <option value="legal">Legal Advisor</option>
            </select>
            <div v-if="errors.type">{{ errors.type }}</div>
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
                rounded-xl
                px-4
                py-2
                ml-4
                mt-4
                bg-green-500
                hover:text-white
                hover:bg-green-600
              "
              type="submit"
              :disabled="form.processing"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
  },

  props: {
    errors: Object,
  },

  setup(props) {
    const form = useForm({
      name: null,
      address: null,
      type: 'legal',
    });
    return { form };
  },
};
</script>
