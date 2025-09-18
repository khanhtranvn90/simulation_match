<template>
  <div class="border p-4 rounded h-full flex flex-col">
    <h2 class="font-bold text-lg mb-2 text-center">Match Results</h2>
    <p class="mb-2 text-center bg-white text-black m-0" style="line-height:41px;">
      <span v-if="matches.length" v-html="ordinalWeek(matches[0]?.week) + ' Week Match Result'"></span>
      <span v-else>Week Match Result</span>
    </p>
    <ul class="list-none space-y-2 p-0 m-0" style="padding:0;">
      <li v-if="matches.length" v-for="match in matches" :key="match.match_id"
          class="flex items-center rounded px-[10px] py-[5px]">
        <span class="flex-1 text-left">{{ match.home }}</span>
        <span class="mx-2 font-bold">{{ match.home_score }} - {{ match.away_score }}</span>
        <span class="flex-1 text-right">{{ match.away }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({
  matches: {
    type: Array,
    default: () => []
  },
  reload: {
    type: Function,
    default: () => {}
  }
})

function ordinalWeek(week) {
  if (!week) return ''
  if (week === 1) return '1<sup>st</sup>'
  if (week === 2) return '2<sup>nd</sup>'
  if (week === 3) return '3<sup>rd</sup>'
  return week + '<sup>th</sup>'
}
</script>
