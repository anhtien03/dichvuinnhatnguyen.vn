/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          blue: '#0077B6',
          'blue-dark': '#023E8A',
          'blue-light': '#E8F4FA',
          gold: '#F8AC22',
          'gold-hover': '#E09816',
          'gold-light': '#FEF7EA',
          accent: '#EA580C',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      }
    },
  },
  plugins: [],
}

