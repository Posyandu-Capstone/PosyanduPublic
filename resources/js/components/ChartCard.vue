<template>
  <div class="bg-white p-4 rounded-xl shadow-sm chart-wrapper">
    <h3 class="chart-title text-sm font-semibold mb-2">{{ title }}</h3>
    <hr class="divider" />
    <Bar v-if="chartData" :data="chartData" :options="options" />
  </div>
</template>

<script setup>
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

defineProps({
  title: String,
  chartData: Object
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        font: {
        },
        color: '#111216'
      }
    },
    title: {
      display: false
    },
    tooltip: {
      bodyFont: {
      },
      titleFont: {
      },
    }
  },
  scales: {
    x: {
      type: 'category',
      ticks: {
        autoSkip: false,
        maxRotation: 45,
        minRotation: 0,
        font: {
        },
        color: '#111216'
      }
    },
    y: {
      beginAtZero: true,
      ticks: {
        stepSize: 50,
        font: {
        },
        color: '#7D7F81'
      }
    }
  },
  layout: {
    padding: {
      left: 20,
      right: 25,
      top: 10,
      bottom: 50
    }
  }
}
</script>

<style scoped>
.chart-wrapper {
  height: 350px;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 20px;
  box-sizing: border-box;
}

.chart-title {
  color: #6B7280;
  font-weight: bold;
  font-size: 15px;
}

.divider {
  border: none;
  border-top: 1px solid #e5e7eb;
  margin: 10px -10px;
  width: calc(100% + 18px);
}
</style>
