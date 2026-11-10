import { ref, watch } from 'vue'

const isDarkMode = ref(localStorage.getItem('admin_theme') !== 'light')
const isSidebarOpen = ref(true)
const isSidebarCollapsed = ref(false)

export function useAdminTheme() {
  function toggleTheme() {
    isDarkMode.value = !isDarkMode.value
    localStorage.setItem('admin_theme', isDarkMode.value ? 'dark' : 'light')
  }

  function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  function toggleCollapse() {
    isSidebarCollapsed.value = !isSidebarCollapsed.value
  }

  return {
    isDarkMode,
    isSidebarOpen,
    isSidebarCollapsed,
    toggleTheme,
    toggleSidebar,
    toggleCollapse
  }
}

