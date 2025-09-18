<template>
  <div class="border p-4 rounded h-full flex flex-col">
    <h2 class="font-bold text-lg mb-2">League Table</h2>
    <table class="w-full border-collapse text-center">
      <thead>
        <tr>
          <th class="border px-2 py-1">Teams</th>
          <th class="border px-2 py-1">PTS</th>
          <th class="border px-2 py-1">P</th>
          <th class="border px-2 py-1">W</th>
          <th class="border px-2 py-1">D</th>
          <th class="border px-2 py-1">L</th>
          <th class="border px-2 py-1">GD</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in standings" :key="row.team_id">
          <td class="border px-2 py-1">{{ row.team_name }}</td>
          <td class="border px-2 py-1">{{ row.points }}</td>
          <td class="border px-2 py-1">{{ row.played }}</td>
          <td class="border px-2 py-1">{{ row.wins }}</td>
          <td class="border px-2 py-1">{{ row.draws }}</td>
          <td class="border px-2 py-1">{{ row.losses }}</td>
          <td class="border px-2 py-1">{{ row.goal_difference }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({
  standings: {
    type: Array,
    default: () => []
  },
  reload: {
    type: Function,
    default: () => {}
  }
})
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}
th {
  background: #f4f4f4;
  color: #000;
}
td:first-child, th:first-child {
  text-align: left;
}
</style>
