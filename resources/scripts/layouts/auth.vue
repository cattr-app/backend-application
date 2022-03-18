<template>
  <div class="auth-layout container grid grid-cols-3 h-full">
    <div class="h-full flex flex-col items-center relative">
      <div class="mt-24 text-center">
        <img src="@/assets/logo.svg" alt="Cattr logo" class="w-32">
        <h1 class="mt-5 text-4xl font-bold">Cattr</h1>
      </div>
      <div class="mt-10 w-80">
        <slot/>
      </div>
      <div class="absolute bottom-12">
        <a v-html="slogan" href="https://cattr.app" class="text-gray-400"/>
      </div>
    </div>
    <div class="col-span-2 bg-login h-full bg-login-page bg-no-repeat bg-contain"/>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
const slogans = ['Cattr - a free open source time tracker', 'Manage your time with ease', 'Looks its fine'];
const getRandomInt = (max: number) => Math.floor(Math.random() * Math.floor(max));

export default defineComponent({
  name: 'auth-layout',
  data: () => ({
    slogan: slogans[getRandomInt(slogans.length)],
  })
});
</script>
