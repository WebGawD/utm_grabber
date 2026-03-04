import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

const isBuild = process.env.NODE_ENV === 'production' || process.argv.includes('build')

export default defineConfig({
  plugins: [react()],
  ...(isBuild ? {
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
  } : {}),
})
