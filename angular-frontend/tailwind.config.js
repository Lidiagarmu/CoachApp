/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,ts}", // busca clases tailwind en tus componentes Angular
  ],
  theme: {
    extend: {
      colors: {
        primary: '#78ffd6',     // color principal (botones, detalles)
        secondary: '#24243e',   // color secundario
        hover: '#a8ff78',       // hover principal
        darkbg: '#0f0c29',      // fondo oscuro de tarjetas, modales, etc.
      },
      fontFamily: {
        sans: ['Poppins', 'sans-serif'], // fuente global
      },
      backgroundImage: {
        'app-gradient': 'linear-gradient(to right, #78ffd6, #a8ff78)', // fondo global reutilizable
      },
    },
  },
  plugins: [
      require("@tailwindcss/line-clamp"),

  ],
};


