<script setup>
import { ref, computed } from 'vue'
import Standings from './components/Standings.vue'
import Match from './components/Matches.vue'
import Predictions from './components/Predictions.vue'
import Championship from './components/Championship.vue'

const standings = ref([])
const matches = ref([])
const predictions = ref([])
const predictionWeek = ref(null)
const finished = ref(false)
const champion = ref(null)
const nextDisabled = ref(false)
const loading = ref(false)

const API_BASE = 'http://127.0.0.1:8000'

async function fetchJson(path, opts = {}) {
  const res = await fetch(`${API_BASE}${path}`, opts)
  if (!res.ok) throw new Error(`HTTP ${res.status}`)
  return res.json()
}

function getCurrentWeekFromMatches(arr) {
  if (!arr || !arr.length) return 0
  const weeks = arr.map(m => Number(m.week || 0))
  return Math.max(...weeks)
}

async function loadStandings() {
  try {
    const data = await fetchJson('/api/standings')
    standings.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error('loadStandings error', e)
    standings.value = []
  }
}

async function loadChampions() {
  try {
    const data = await fetchJson('/api/simulation/champion')
    champion.value = data || null
  } catch (e) {
    console.error('loadChampions error', e)
    champion.value = null
  }
}

async function loadPredictions(week) {
  try {
    const data = await fetchJson(`/api/simulation/prediction`)
    predictions.value = Array.isArray(data) ? data : (Array.isArray(data?.teams) ? data.teams : [])
    predictionWeek.value = week
  } catch (e) {
    console.error('loadPredictions error', e)
    predictions.value = []
    predictionWeek.value = week
  }
}

async function loadMatches() {
  try {
    const data = await fetchJson('/api/matches')
    matches.value = Array.isArray(data) ? data : []

    const allFinished = matches.value.length > 0 && matches.value.every(m => Number(m.finished) === 1)
    finished.value = !!allFinished
    nextDisabled.value = finished.value

    if (finished.value) {
      // finished => load champion only
      await loadChampions()
      predictions.value = []
      predictionWeek.value = null
      return
    }

    // not finished => maybe show predictions if week >= 4
    const week = getCurrentWeekFromMatches(matches.value)
    if (week >= 4) {
      await loadPredictions(week)
    } else {
      predictions.value = []
      predictionWeek.value = null
    }
  } catch (e) {
    console.error('loadMatches error', e)
    matches.value = []
    finished.value = false
    nextDisabled.value = false
    predictions.value = []
    predictionWeek.value = null
  }
}

async function nextWeek() {
  if (loading.value) return
  loading.value = true
  try {
    const data = await fetchJson('/api/simulation/next', { method: 'GET' })

    if (typeof data?.finished !== 'undefined') {
      finished.value = !!Number(data.finished)
      nextDisabled.value = finished.value
      if (data.champion) champion.value = data.champion
    }

    await loadStandings()
    await loadMatches()
  } catch (e) {
    console.error('nextWeek error', e)
    await loadStandings()
    await loadMatches()
  } finally {
    loading.value = false
  }
}

// Play all weeks sequentially
async function playAllLoop() {
  if (finished.value) return
  await nextWeek()
  if (!finished.value) {
    setTimeout(playAllLoop, 500)
  }
}

function playAll() {
  if (finished.value || loading.value) return
  playAllLoop()
}

async function resetLeague() {
  try {
    await fetchJson('/api/simulation/new', { 
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    finished.value = false
    champion.value = null
    predictions.value = []
    predictionWeek.value = null
    nextDisabled.value = false
    await loadStandings()
    await loadMatches()
  } catch (e) {
    console.error('resetLeague error', e)
  }
}

const currentWeek = computed(() => matches.value.length ? getCurrentWeekFromMatches(matches.value) : 0)
const showPredictions = computed(() => currentWeek.value >= 4 && !finished.value && predictions.value.length > 0)
const showChampionship = computed(() => finished.value && !!champion.value)

// initial load
loadStandings()
loadMatches()
</script>

<template>
  <div class="p-6 min-h-screen bg-gray-900 text-white">
    <h1 class="text-3xl font-bold mb-6 text-center">Football League Simulator</h1>
    <div class="grid grid-cols-3 gap-6 mb-6" style="margin-bottom:1rem;">
      <div class="border p-4 rounded h-full bg-gray-800">
        <Standings :standings="standings" :reload="loadStandings" />
      </div>
      <div class="border p-4 rounded h-full bg-gray-800">
        <Match :matches="matches" :reload="loadMatches" />
      </div>
      <div v-if="showChampionship" class="border p-4 rounded h-full bg-gray-800">
        <Championship :champion="champion" />
      </div>
      <div v-else-if="showPredictions" class="border p-4 rounded h-full bg-gray-800">
        <Predictions :teams="predictions" :week="predictionWeek" />
      </div>
    </div>
    <div class="flex justify-between pt-2">
      <template v-if="!finished">
        <button class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800" @click="playAll" :disabled="finished || loading">
          Play All
        </button>
        <button class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800" @click="nextWeek" :disabled="nextDisabled || loading">
          Next Week
        </button>
      </template>
      <button v-else class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-800" @click="resetLeague" :disabled="!finished || loading">
        Reset League
      </button>
    </div>
  </div>
</template>
