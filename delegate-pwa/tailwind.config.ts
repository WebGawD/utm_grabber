import type { Config } from 'tailwindcss'

export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        heading: ['Outfit', 'Inter', 'system-ui', 'sans-serif'],
        body:    ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        primary: {
          DEFAULT: '#0D9488',
          dark:    '#115E59',
          light:   '#5EEAD4',
          50:      '#F0FDFA',
        },
        accent: {
          DEFAULT: '#F59E0B',
          dark:    '#B45309',
        },
        earth: '#EA580C',
        legacy: '#16A34A',
        dark:   '#0F172A',
      },
      animation: {
        'slide-in': 'slideIn 0.2s ease',
        'pulse-dot': 'pulseDot 1.5s ease-in-out infinite',
        'scan-line': 'scanLine 2s linear infinite',
      },
      keyframes: {
        slideIn: {
          from: { opacity: '0', transform: 'translateY(8px)' },
          to:   { opacity: '1', transform: 'translateY(0)' },
        },
        pulseDot: {
          '0%, 100%': { transform: 'scale(1)', opacity: '1' },
          '50%':      { transform: 'scale(1.4)', opacity: '0.6' },
        },
        scanLine: {
          '0%':   { top: '0%' },
          '100%': { top: '100%' },
        },
      },
    },
  },
  plugins: [],
} satisfies Config
