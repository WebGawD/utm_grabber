import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// Dev mode serves index.html as a normal app for hot-reload testing.
// Build mode compiles an IIFE bundle into wp-theme/assets/js/.
const isBuild = process.env.NODE_ENV === 'production' || process.argv.includes('build')

export default defineConfig({
  plugins: [react()],
  ...(isBuild ? {
    build: {
      outDir: '../../wp-theme/assets/js',
      emptyOutDir: false,
      lib: {
        entry: 'src/main.tsx',
        name: 'AwsisaRegistration',
        fileName: () => 'registration-app.js',
        formats: ['iife'],
      },
      rollupOptions: { external: [] },
    },
    define: { 'process.env.NODE_ENV': '"production"' },
  } : {}),
})
