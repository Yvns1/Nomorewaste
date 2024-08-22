/** @type {import('tailwindcss').Config} */
module.exports = {
  purge: ['./src/**/*.{js,jsx,ts,tsx}', './public/index.html'],
  darkMode: false, // ou 'media' ou 'class'
  theme: {
    extend: {},
  },
  colors: {
    'custom-bg': '#F0F4E3',
  },
  
  variants: {
    extend: {},
  },
  plugins: [],
}
