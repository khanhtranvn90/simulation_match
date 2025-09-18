<template>
  <div class="max-w-sm mx-auto mt-10 p-6 bg-white shadow rounded px-[15px]">
    <h2 class="text-lg font-semibold mb-4" v-html="ordinalWeek(week) + ' Week Predictions of Championship'"></h2>
    <div v-for="team in teams" :key="team.name" class="flex justify-between items-center mb-3">
      <span>{{ team.name }}</span>
      <span>{{ team.percentage }}%</span>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({
  teams: {
    type: Array,
    default: () => []
  },
  week: {
    type: [Number, String],
    default: null
  }
})

function ordinalWeek(week) {
  if (!week) return ''
  if (week === 1 || week === '1') return '1<sup>st</sup>'
  if (week === 2 || week === '2') return '2<sup>nd</sup>'
  if (week === 3 || week === '3') return '3<sup>rd</sup>'
  return week + '<sup>th</sup>'
}
</script>