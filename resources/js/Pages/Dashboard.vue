<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard -
        {{ this.$page.props.co_id }} - {{ this.$page.props.yr_id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
          <!-- <div
            class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
          >
            <a href="pd">Generate pdf file</a>
          </div> -->
          <div
            class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
          >
            <a href="ex">Generate MS-Excel file</a>
          </div>
          <div
            class="border rounded-lg shadow-md p-2 m-2 inline-block hover:bg-gray-600 hover:text-white"
          >
            <a href="doc">Generate MS-Word file</a>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Welcome from "@/Jetstream/Welcome";

export default {
  components: {
    AppLayout,
    Welcome,
  },

  mounted: function () {
    console.log(this.$page.props.co_id);
    console.log(this.$page.props.yr_id);
  },
};
</script>
