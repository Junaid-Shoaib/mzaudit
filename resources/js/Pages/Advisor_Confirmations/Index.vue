<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Advisor Confirmations

        <div class="flex-1 inline-block float-right">
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
    <div v-if="$page.props.flash.success" class="bg-green-600 text-white">
      {{ $page.props.flash.success }}
    </div>
    <div  class="bg-red-600 text-white text-center" v-if="errors.file">{{ errors.file }}</div>


    <div class="max-w-7xl mx-auto pb-2">
      <div class="relative mt-3 ml-7 flex-row">
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
            :href="route('advisor_confirmations.create')"
            v-if="create"
            >Add Confiramtion
          </inertia-link>

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
            :href="route('advisor_confirmations.edit')"
          >
            <span>Edit</span>
          </inertia-link>
        </div>

        <div
          class="
            border
            bg-blue-400
            hover:bg-blue-600
            hover:text-white
            inline-block
            shadow-md
            rounded-xl
            px-4
            py-1
            m-1
          "
        >
          <a href="advisor_word">Generate Advisor Letters</a>
        </div>
        <!-- <div
          class="
            border
            inline-block
            bg-blue-400
            hover:bg-blue-600
            hover:text-white
            shadow-md
            rounded-xl
            px-4
            py-1
            m-1
          "
        >
          <a href="bankConfig">Generate Remaining Pages</a>
        </div> -->
        <!-- <div
          class="
            border
            inline-block
            bg-blue-400
            hover:bg-blue-600
            hover:text-white
            shadow-md
            rounded-xl
            px-4
            py-1
            m-1
          "
        >
          <a href="ex">Generate Control Sheet</a>
        </div> -->

        <div
          class="
            border
            inline-block
            bg-blue-400
            hover:bg-blue-600
            hover:text-white
            shadow-md
            rounded-xl
            px-4
            py-1
            m-1
          "
        >
          <a href="advisorspdf" target="_blank">Generate Advisors</a>
        </div>
      </div>

      <div class="">
        <table class="shadow-lg border mt-4 ml-12 rounded-xl w-11/12">
          <thead>
            <tr class="bg-gray-700 text-white text-centre font-bold">
              <th class="px-3 pt-3 pb-3 border">Bank</th>
              <th class="px-3 pt-3 pb-3 border">Create Date</th>
              <th class="px-3 pt-3 pb-3 border">Sent Date</th>
              <th class="px-3 pt-3 pb-3 border">Reminder Date</th>
              <th class="px-3 pt-3 pb-3 border">Received Date</th>
              <th class="px-4 pt-4 pb-4 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in balances.data" :key="item.id">
              <td
                Style="width: 52%"
                class="py-2 px-2 border text-left capitalize"
              >
                {{ item.branch }}
              </td>
              <td Style="width: 12%" class="py-2 px-2 border text-center">
                {{ item.confirm_create }}
              </td>
              <td Style="width: 12%" class="py-2 px-2 border text-center">
                {{ item.sent }}
              </td>
              <td Style="width: 12%" class="py-2 px-2 border text-center">
                {{ item.reminder }}
              </td>
              <td Style="width: 12%" class="py-2 px-2 border text-center">
                {{ item.received }}
              </td>
                <td Style="width: 12%" class="py-2 px-2 border text-center">
                <input
                    class="border-gray-800 w-24 ring-gray-800 ring-1 outline-none"
                    type="file"
                    accept=".pdf"
                    v-on:change="onFileChange($event,item.id)"
                    />
                    <a v-if="item.path" class="
                        border
                        inline-block
                        bg-blue-400
                        hover:bg-blue-600
                        hover:text-white
                        shadow-md
                        rounded-xl
                        px-4
                        py-1
                        m-1
                    "
                     :href="'/advisorconfirmUpload/' +item.id"
                     >Download</a>
                  </td>

              <!-- <td class="py-2 px-4 border text-center">
              <button
                class="border bg-indigo-300 rounded-xl px-4 py-1 m-1"
                @click="destroy(item.id)"
              >
                <span>Delete</span>
              </button>
            </td> -->
            </tr>
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
    errors: Object,
    balances: Object,
    companies: Object,
    years: Object,
    create: Object,
    cochange: Object,
  },
  data() {
    return {
      options: this.companies,
      co_id: this.cochange,
      // co_id: this.$page.props.co_id,
      yr_id: this.$page.props.yr_id,
    };
  },
  methods: {
       onFileChange(e, index) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
        var id = index;
       this.$inertia.post(route('advisor.updated', id),
            {
            _method: 'put',
            file: files[0],
            },
            {
            preserveState: true,
            preserveScroll: true,
            }
        )
    },
    destroy(id) {
      this.$inertia.delete(route("confirmations.destroy", id));
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
