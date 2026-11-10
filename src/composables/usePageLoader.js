import { ref } from 'vue'

const isNavLoading = ref(false)
const navProgress = ref(0)
let navTimer = null

export function usePageLoader() {
  const startNavLoading = () => {
    isNavLoading.value = true
    navProgress.value = 25
    if (navTimer) clearInterval(navTimer)
    navTimer = setInterval(() => {
      if (navProgress.value < 88) {
        navProgress.value += Math.floor(Math.random() * 10) + 4
      }
    }, 120)
  }

  const finishNavLoading = () => {
    navProgress.value = 100
    if (navTimer) {
      clearInterval(navTimer)
      navTimer = null
    }
    setTimeout(() => {
      isNavLoading.value = false
      setTimeout(() => {
        navProgress.value = 0
      }, 250)
    }, 280)
  }

  return {
    isNavLoading,
    navProgress,
    startNavLoading,
    finishNavLoading
  }
}

