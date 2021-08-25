<template>
  <div class="fixed flex items-top bg-gray-700 dark:bg-gray-900 pt-0 inset-0">
    <div class="flex flex-row items-center justify-between fixed px-6 py-1">
      <div class="">
        <!-- <inertia-link
          :href="route('login')"
          class="text-md text-white ml-5 hover:text-blue-300"
        >
          Confirmation.pk
        </inertia-link> -->
      </div>
      <div class="fixed inset-y-0 right-0 mr-12 py-5" v-if="canLogin">
        <inertia-link
          v-if="$page.props.user"
          href="/dashboard"
          class="
            text-md
            underline
            text-white
            ml-5
            hover:text-blue-300
            font-bold
          "
        >
          Dashboard
        </inertia-link>

        <template v-else>
          <inertia-link
            :href="route('login')"
            class="text-md underline text-white ml-5 hover:text-blue-300"
          >
            Login
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
      <div class="flex w-screen h-screen z-10">
        <img
          class="px-2 w-full object-cover"
          src="img/wallpaper.jpg"
          alt="Welcome to Confirmation.pk"
        />
        <div class="absolute z-20 invisible md:visible w-full my-8 text-left">
          <inertia-link
            :href="route('login')"
            class="
              text-white
              ml-16
              font-sans font-bold
              text-3xl
              hover:text-blue-300
            "
          >
            CONFIRMATION.PK
          </inertia-link>

          <p class="ml-6 text-md font-bold text-white">
            Is a online platform that facilitates balance confirmation.
          </p>
        </div>

        <div class="absolute z-20 invisible md:visible bottom-16 right-10">
          <p class="text-white text-center font-sans font-bold text-2xl">
            Powered by
          </p>
          <p class="font-bold text-md text-center text-white">
            Digital Solution Department
          </p>
          <p class="font-bold text-md text-white">
            Muniff Ziauddin & Co. Chartered Accountants
          </p>
        </div>

        <div class="absolute z-20 invisible md:visible bottom-16 left-10">
          <p class="text-white text-left font-sans font-bold text-2xl">
            Benefits
          </p>
          <p class="font-bold text-md mt-2 text-left text-white">
            1. Save cost Eliminating extra <br />
            stationery expenses associated<br />
            with conventional method
            <br />
            2. Save time<br />
            3. Secure
          </p>
        </div>
      </div>
    </div>

    <!--picture end -->
    <div
      class="
        flex flex-row
        bottom-px
        items-center
        w-full
        justify-center
        fixed
        px-6
        py-1
      "
    >
      <div class="">
        <p class="text-sm font-bold bg-blend-screen text-white ml-5">
          Copyright @ 2021, Confirmation.pk
        </p>
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
