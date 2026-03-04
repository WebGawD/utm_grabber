import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../../wp-theme/assets/js',
    emptyOutDir: false,
    lib: {
      entry: 'src/main.tsx',
      name: 'AwsisaRegistration',
      fileName: () => 'registration-app.js',
      formats: ['iife'],
    },
    rollupOptions: {
      external: [],
    },
  },
  define: {
    'process.env.NODE_ENV': '"production"',
  },
})
