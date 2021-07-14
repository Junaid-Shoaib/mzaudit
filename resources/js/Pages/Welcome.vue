<template>
  <div class="fixed flex items-top bg-gray-700 dark:bg-gray-900 pt-0 inset-0">
    <div class="flex flex-row items-center justify-between fixed px-6 py-1">
      <div class="">
        <inertia-link
          :href="route('login')"
          class="text-sm text-white ml-5 hover:text-blue-300"
        >
          Confirmation.pk
        </inertia-link>
      </div>
      <div class="fixed inset-y-0 right-0 mr-12 py-1" v-if="canLogin">
        <inertia-link
          v-if="$page.props.user"
          href="/dashboard"
          class="text-sm text-white ml-5 hover:text-blue-300 font-bold"
        >
          Dashboard
        </inertia-link>

        <template v-else>
          <inertia-link
            :href="route('login')"
            class="text-sm text-white ml-5 hover:text-blue-300"
          >
            Log in
          </inertia-link>

          <!-- <inertia-link
            v-if="canRegister"
            :href="route('register')"
            class="text-sm text-white ml-5 hover:text-blue-300"
          >
            Register
          </inertia-link> -->
        </template>
      </div>
    </div>
    <!-- End if -->
    <div class="">
      <!-- <div class="flex pt-8 h-11/12 z-10"> -->
      <div class="flex pt-8 w-screen h-screen z-10">
        <img
          class="px-2 w-full object-cover"
          src="img/wallpaper.jpg"
          alt="Welcome to Confirmation.pk"
        />
      </div>
    </div>
    <!--picture end -->
  </div>
</template>


<script>
export default {
  props: {
    canLogin: Boolean,
    canRegister: Boolean,
  },
};
</script>
