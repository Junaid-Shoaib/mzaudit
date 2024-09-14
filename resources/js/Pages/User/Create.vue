<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create New User
      </h2>
    </template>
    <div v-if="$page.props.flash.success" class="bg-green-400 text-white text-center">
      {{ $page.props.flash.success }}
    </div>
    <div class="max-w-7xl mx-auto pb-2">
    <jet-authentication-card>
      <div class="">
        <jet-validation-errors class="mb-4" />
        <form @submit.prevent="submit">
          <div>
            <jet-label for="name" value="Name" />
            <jet-input
              id="name"
              type="text"
              class="mt-1 block w-full"
              v-model="form.name"
              required
              autofocus
              autocomplete="name"
            />
          </div>

          <div class="mt-4">
            <jet-label for="email" value="Email" />
            <jet-input
              id="email"
              type="email"
              class="mt-1 block w-full"
              v-model="form.email"
              required
            />
          </div>

          <div class="mt-4">
            <jet-label for="password" value="Password" />
            <jet-input
              id="password"
              type="password"
              class="mt-1 block w-full"
              v-model="form.password"
              required
              autocomplete="new-password"
            />
          </div>

          <div class="mt-4">
            <jet-label for="password_confirmation" value="Confirm Password" />
            <jet-input
              id="password_confirmation"
              type="password"
              class="mt-1 block w-full"
              v-model="form.password_confirmation"
              required
              autocomplete="new-password"
            />
          </div>

          <div class="mt-4">
            <jet-label for="role" value="Role" />
            <select
                  v-model="form.role"
                  class="pr-2 pb-2 w-full  rounded-md leading-tight"
                  label="role"
                  required
                >
                  <option value="0">Admin</option>
                  <option value="1">Manager</option>
                  <option value="2">Employee</option>
                </select>
            <!-- <jet-input
              id="role"
              type="role"
              class="mt-1 block w-full"
              v-model="form.role"
              
            /> -->
          </div>

          <div
            class="mt-4"
            v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
          >
            <jet-label for="terms">
              <div class="flex items-center">
                <jet-checkbox
                  name="terms"
                  id="terms"
                  v-model:checked="form.terms"
                />

                <div class="ml-2">
                  I agree to the
                  <a
                    target="_blank"
                    :href="route('terms.show')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    >Terms of Service</a
                  >
                  and
                  <a
                    target="_blank"
                    :href="route('policy.show')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                    >Privacy Policy</a
                  >
                </div>
              </div>
            </jet-label>
          </div>

          <div class="flex items-center justify-end mt-4">
            <jet-button
              class="ml-4"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Register
            </jet-button>
          </div>
        </form>
      </div>
    </jet-authentication-card>
  </div>
  </app-layout>
</template>
<!-- 
<template>
  <jet-authentication-card>
   

    <jet-validation-errors class="mb-4" />

   
  </jet-authentication-card>
</template> -->

<script>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo";
import JetButton from "@/Jetstream/Button";
import JetInput from "@/Jetstream/Input";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetLabel from "@/Jetstream/Label";
import JetValidationErrors from "@/Jetstream/ValidationErrors";
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    JetInput,
    JetCheckbox,
    JetLabel,
    JetValidationErrors,
  },

  data() {
    return {
      form: this.$inertia.form({
        name: "",
        email: "",
        password: "",
        role:"",
        password_confirmation: "",
        terms: false,
      }),
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("users.store"),
       {
        onFinish: () => this.form.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>
