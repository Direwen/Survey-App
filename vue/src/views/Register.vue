<template>
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600"
      alt="Your Company" />
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
      Register For Free
    </h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" @submit="register">
      <div v-if="errorMsg" class="bg-red-500 text-white flex justify-between items-center p-2 rounded">
        <p class="">{{ errorMsg }}</p>
        <span @click="errorMsg = ''" class="cursor-pointer p-1 rounded-full transition-all ease-in-out duration-200 hover:bg-red-700 active:scale-90">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </span>
      </div>
      <!-- NAME -->
      <div>
        <label for="fullname" class="block text-sm font-medium leading-6 text-gray-900">Full Name</label>
        <div class="mt-2">
          <input
            v-model="user.name" 
            id="fullname" 
            name="name"
            type="text" 
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
          />
        </div>
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
      <!-- PASSWORD CONFIRMATION -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Password Confirmation</label>
        <div class="mt-2">
          <input
            v-model="user.password_confirmation" 
            id="password_confirmation" 
            name="password_confirmation" 
            type="password" 
            autocomplete="current-password" 
            required
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
          />
        </div>
      </div>
      <!-- SUBMIT BUTTON -->
      <div>
        <button type="submit"
          class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          Create an account
        </button>
      </div>
    </form>

    <p class="mt-5 text-right text-sm text-gray-500">
      <router-link 
        :to="{ name: 'Login' }"
        class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
        Login to your account
      </router-link>
      
    </p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router"
import store from "../store";

const router = useRouter();
const user = {
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
}
const errors = ref({});
let errorMsg = ref('');

function register(event){
  event.preventDefault();
  store
    .dispatch('register', user)
    .then((result) => {
      console.log('Result' + result);
      router.push({name: 'Dashboard'});
    })
    .catch(error => {
      console.log("Error => " + typeof(error));
      errorMsg.value = error;
    });
}
</script>
