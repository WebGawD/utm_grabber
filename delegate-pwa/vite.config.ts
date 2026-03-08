import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { VitePWA } from 'vite-plugin-pwa'
import path from 'path'

export default defineConfig({
  base: '/awsisa/app/',
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'icons/*.png'],
      manifest: {
        name: 'Watersan 2026 — Delegate App',
        short_name: 'WaterSan 2026',
        description: 'Your companion app for the AWSISA Watersan Dialogue 2026, ICC Durban',
        theme_color: '#0D9488',
        background_color: '#0F172A',
        display: 'standalone',
        orientation: 'portrait',
        scope: '/awsisa/app/',
        start_url: '/awsisa/app/',
        icons: [
          { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
          { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'any maskable' },
        ],
      },
      workbox: {
        navigateFallback: '/awsisa/app/index.html',
        globPatterns: ['**/*.{js,css,html,svg,woff2,ico,png}'],
        runtimeCaching: [
          {
            urlPattern: /\/awsisa\/v1\/agenda/i,
            handler: 'StaleWhileRevalidate',
            options: {
              cacheName: 'agenda-cache',
              expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 },
            },
          },
          {
            urlPattern: /\/awsisa\/v1\/delegate\/alerts/i,
            handler: 'NetworkFirst',
            options: { cacheName: 'alerts-cache' },
          },
          {
            urlPattern: /.*supabase\.co\/rest\/v1\/(agenda|sponsors|swag_bag).*/i,
            handler: 'StaleWhileRevalidate',
            options: {
              cacheName: 'supabase-cache',
              expiration: { maxEntries: 200, maxAgeSeconds: 60 * 60 * 6 },
            },
          },
          {
            urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
            handler: 'CacheFirst',
            options: { cacheName: 'google-fonts-cache' },
          },
        ],
      },
    }),
  ],
  resolve: {
    alias: { '@': path.resolve(__dirname, './src') },
  },
})
