<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Update Banks Confirmations
      </h2>
    </template>
        <div v-if="$page.props.flash.success" class="bg-green-600 text-white text-center">
      {{ $page.props.flash.success }}
    </div>
    <div  class="bg-red-600 text-white text-center" v-if="errors.file">{{ errors.file }}</div>

    <div class="max-w-7xl mx-auto pb-2">
      <div class="relative mt-5 ml-7 flex-row">
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
            :href="route('confirmations')"
            >Back
          </inertia-link>
        </div>
      </div>

      <div class="relative mt-5 flex-row border-t border-b border-gray-200">
        <form @submit.prevent="submit">
          <div class="">
            <table class="shadow-lg border mt-4 mb-4 ml-12 rounded-xl w-11/12">
              <thead class="bg-gray-700 text-white text-centre font-bold">
                <tr>
                  <th class="px-3 pt-3 pb-3 border">Bank</th>
                  <th class="px-3 pt-3 pb-3 border">Create Date</th>
                  <th class="px-3 pt-3 pb-3 border">Sent Date</th>
                  <th class="px-3 pt-3 pb-3 border">Reminder Date</th>
                  <th class="px-3 pt-3 pb-3 border">Received Date</th>
                  <!-- <th class="px-3 pt-3 pb-3 border">Received Date</th> -->
                  <!-- <th class="px-3 pt-3 pb-3 border">Uploading PDF</th> -->
                  <!-- <th class="px-3 pt-3 pb-3 border">Received Date</th> -->
                </tr>
              </thead>
              <tbody>
                <tr v-for=" confirm in data" :key="confirm.id">
                  <td class="w-6/12">
                    <input
                      v-model="confirm.name"
                      type="text"
                      class="rounded-md w-full my-1"
                      readonly
                    />
                  </td>
                  <td class="w-1/12">
                    <input
                      v-model="confirm.confirm_create"
                      type="date"
                      readonly
                      class="rounded-md w-full my-1"
                    />
                  </td>
                  <td class="w-1/12">
                    <input
                      v-model="confirm.sent"
                      type="date"
                      :upper-limit="upper"
                      :lower-limit="lower"
                      class="rounded-md w-full my-1"
                    />
                  </td>
                  <td class="w-1/12">
                    <input
                      v-model="confirm.reminder"
                      type="date"
                      :upper-limit="upper"
                      :lower-limit="lower"
                      class="rounded-md w-full my-1"
                    />
                  </td>
                  <td class="w-1/12">
                    <input
                      v-model="confirm.received"
                      type="date"
                      :upper-limit="upper"
                      :lower-limit="lower"
                      class="rounded-md w-full my-1"
                    />
                  </td>


                  <!-- <td>
                      <div></div>
                    </td> -->


                  <!-- <td>
                <button
                  @click.prevent="deleteRow(index)"
                  class="border bg-indigo-300 rounded-xl px-4 py-2 m-4"
                >
                  Delete
                </button>
              </td> -->
                </tr>
              </tbody>
            </table>
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
              Update Confirmation
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
import Datepicker from "vue3-datepicker";

export default {
  components: {
    AppLayout,
    Datepicker,
    useForm,
  },
//   remember: 'form',
  props: {
    errors: Object,
    data: Object,
    branches: Object,
    year: Object,
  },



  data() {
    return {
      balances: this.data,
      upper: new Date(this.year.end),
      lower: new Date(this.year.begin),
    };

  },

    //  setup() {
    //     const form = useForm({
    //     file: null,
    //     id:null,
    //     });
    //     return { form };
    // },

  methods: {
    submit() {
      this.$inertia.put(route("confirmations.update", this.balances[0]), {
        balances: this.balances,
      });
    },



    onFileChange(e, index) {
        //         console.log(id);
        // debugger;
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
    //   var file = files[0];
    //   var id = ;
    //   console.log(this.form.avatar);
        var id = index;
    //   this.files[indexOfConfirm] = this.form.avatar;
    // //   this.$set(this.balances, indexOfConfirm, files[0]);
    // //   this.balances.splice(indexOfConfirm,  files[0])
    //   console.log(this.balances);
    //   debugger;
       this.$inertia.post(route('balances.updated', id),
            {
            _method: 'put',
            file: files[0],
            balances: this.balances,
            },
            {
            preserveState: true,
            preserveScroll: true,
            }
        )
    //   this.form.post(route("balances.updated"));
    },



    addRow() {
      this.balances.push({
        sent: null,
        confirm_create: null,
        reminder: null,
        received: null,
        path: null,
      });
    },
    deleteRow(index) {
      this.balances.splice(index, 1);
    },
  },
};
</script>
