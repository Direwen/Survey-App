<template>
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
      alt="Your Company" />
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
      Log in to your account
    </h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" @submit="login">
      <div v-if="errorMsg" class="bg-red-500 text-white flex justify-between items-center p-2 rounded">
        <p class="">{{ errorMsg }}</p>
        <span @click="errorMsg = ''" class="cursor-pointer p-1 rounded-full transition-all ease-in-out duration-200 hover:bg-red-700 active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </span>
      </div>
      <!-- EMAIL -->
      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
        <div class="mt-2">
          <input
            v-model="user.email" 
            id="email" 
            name="email" 
            type="email" 
            autocomplete="email" 
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
          />
        </div>
      </div>
      <!-- PASSWORD -->
      <div>
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        <div class="mt-2">
          <input
            v-model="user.password" 
            id="password" 
            name="password" 
            type="password" 
            autocomplete="current-password" 
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
          />
        </div>
      </div>
      <!-- REMEMBER CHECK BOX -->
      <div class="flex items-center justify-start gap-2">
        <input
          v-model="user.remember" 
          type="checkbox" 
          name="remember-me" 
          id="remember-me" 
          class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
        >
        <label for="remember-me" class="block text-sm text-gray-900">Remember me</label>
      </div>
      <!-- SUBMIT BUTTON -->
      <div>
        <button
          :disabled="loading" 
          type="submit"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          :class="{'cursor-not-allowed': loading, 'hover:bg-indigo-500': loading}"
        >
          
          <svg
            v-if="loading" 
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Sign in
        </button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
      Not a member?
      {{ " " }}
      <router-link 
        :to="{ name: 'Register' }"
        class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
        Register for Free
      </router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import store from '../store';

const router = useRouter();
const user = {
  email: '',
  password: '',
  remember: false,
}
const loading = ref(false);
let errorMsg = ref('');

function login(event){
  event.preventDefault();
  loading.value = true;
  store.dispatch('login', user)
    .then((result) => {
      console.log('Result => ' + result);
      loading.value = false;
      router.push({name: 'Dashboard'});
    })
    .catch((error) => {
      console.log("Error => " + error);
      loading.value = false;
      errorMsg.value = error;
    });
}
</script>
