/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      fontFamily: { sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
      colors: {
        simb: { bg: '#c5d5ef', page: '#eef3fb', from: '#4a69b0', to: '#4b86f0', ink: '#1e293b' },
      },
      keyframes: {
        'fade-up':  { '0%': { opacity: 0, transform: 'translateY(14px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
        'fade-in':  { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
        'zoom-out': { '0%': { opacity: 0, transform: 'scale(1.06)' }, '100%': { opacity: 1, transform: 'scale(1)' } },
        'pop':      { '0%': { opacity: 0, transform: 'translateY(10px) scale(.96)' }, '100%': { opacity: 1, transform: 'translateY(0) scale(1)' } },
      },
      animation: {
        'fade-up':  'fade-up .5s ease-out both',
        'fade-in':  'fade-in .6s ease-out both',
        'zoom-out': 'zoom-out 1.2s ease-out both',
        'pop':      'pop .25s ease-out both',
      },
    },
  },
  plugins: [],
}
