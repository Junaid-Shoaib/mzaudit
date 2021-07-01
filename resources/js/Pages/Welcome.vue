<template>
  <div
    class="
      relative
      flex
      items-top
      justify-center
      min-h-screen
      dark:bg-gray-900
      sm:items-center
      sm:pt-0
      bg-gray-200
    "
  >
    <div class="">
      <img
        class="h-screen w-screen"
        src="img/wallpaper.jpg"
        alt="Welcome to Confirmation.pk"
      />
      <div class="hidden fixed top-0 px-6 py-4 sm:block float-left">
        <h1 class="font-bold font-serif text-5xl text-white">CONFIRMATION</h1>
        <div
          v-if="canLogin"
          class="hidden fixed top-0 right-0 px-6 py-4 sm:block"
        >
          <inertia-link
            v-if="$page.props.user"
            href="/dashboard"
            class="text-sm text-white underline"
          >
            Dashboard
          </inertia-link>

          <template v-else>
            <inertia-link
              :href="route('login')"
              class="text-sm text-white underline"
            >
              Log in
            </inertia-link>

            <inertia-link
              v-if="canRegister"
              :href="route('register')"
              class="ml-4 text-sm text-white underline"
            >
              Register
            </inertia-link>
          </template>
        </div>
      </div>
    </div>
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
