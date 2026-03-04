import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../../wp-theme/assets/js',
    emptyOutDir: false,
    lib: {
      entry: 'src/main.tsx',
      name: 'AwsisaDonate',
      fileName: () => 'donate-app.js',
      formats: ['iife'],
    },
  },
  define: { 'process.env.NODE_ENV': '"production"' },
})
