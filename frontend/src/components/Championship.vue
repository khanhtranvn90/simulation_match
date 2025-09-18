<template>
  <div v-if="champion" class="max-w-sm mx-auto bg-gradient-to-r from-yellow-400 via-yellow-300 to-yellow-500 shadow-xl rounded-2xl p-6 text-center relative">
    <h1 class="mt-10 text-3xl font-bold text-white mb-4 drop-shadow-md">Winner</h1>

    <h2 class="mt-10 text-3xl font-bold text-white mb-4 drop-shadow-md">{{ champion.name }}</h2>

    <div class="flex justify-around text-white font-semibold mb-4">
      <div>
        <p class="text-lg">{{ champion.pts }}</p>
        <p class="text-sm">PTS</p>
      </div>
      <div>
        <p class="text-lg">{{ champion.win }}</p>
        <p class="text-sm">Win</p>
      </div>
      <div>
        <p class="text-lg">{{ champion.loss }}</p>
        <p class="text-sm">Loss</p>
      </div>
    </div>

    <div class="flex justify-around text-white font-semibold">
      <div>
        <p class="text-lg">{{ champion.draw }}</p>
        <p class="text-sm">Draw</p>
      </div>
    </div>
  </div>
  <div v-else class="max-w-sm mx-auto bg-yellow-100 shadow rounded p-6 text-center">
    <h2 class="text-xl font-bold text-gray-700">No champion data available</h2>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({
  champion: {
    type: Object,
    default: () => ({})
  }
})
</script>
