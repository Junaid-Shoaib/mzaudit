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
      <div class="fixed inset-y-0 right-0 mt-4 mr-14 py-5" v-if="canLogin">
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
          <!-- hover:text-blue-300 -->
          <inertia-link
            :href="route('login')"
            class="
              text-md
              bg-blue-500
              underline
              text-white
              font-bold
              ml-5
              border
              px-6
              rounded-md
              hover:text-red-300
              hover:bg-blue-700
            "
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
          src="img/wallpaper3.jpg"
          alt="Picture not Load Confirmtaion.pk"
        />
        <div class="absolute z-20 invisible md:visible w-50% my-2 text-left">
          <!-- class="
                text-white
                ml-16
                font-sans font-bold
                text-3xl
                hover:text-blue-300
              " -->
          <inertia-link :href="route('login')">
            <img
              class="block ml-16 h-28 w-auto"
              src="img/logoMenu1.png"
              alt="Welcome to Confirmation.pk"
            />
            <p
              class="
                text-white
                ml-12
                font-sans font-bold
                text-3xl
                hover:text-blue-400
              "
            >
              Confirmation.pk
            </p>
          </inertia-link>

          <p class="ml-8 text-md font-bold text-white">
            Is a online platform that facilitates
          </p>
          <p class="ml-20 text-md font-bold text-white">
            balance confirmation.
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
            1. Save cost Eliminating extra
          </p>
          <p class="font-bold ml-4 text-md text-left text-white">
            stationery expenses associated<br />
          </p>
          <p class="font-bold ml-4 text-md text-left text-white">
            with conventional method
          </p>
          <p class="font-bold text-md text-left text-white">
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
