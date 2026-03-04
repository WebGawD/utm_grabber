import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { VitePWA } from 'vite-plugin-pwa'
import path from 'path'

export default defineConfig({
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'icons/*.png'],
      manifest: {
        name: 'AWSISA Admin — Watersan Dialogue 2026',
        short_name: 'AWSISA Admin',
        description: 'Admin Command Center for the AWSISA Watersan Dialogue 2026',
        theme_color: '#0D9488',
        background_color: '#0F172A',
        display: 'standalone',
        orientation: 'portrait',
        scope: '/',
        start_url: '/',
        icons: [
          { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
          { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'any maskable' },
        ],
      },
      workbox: {
        // Cache strategies for offline resilience
        runtimeCaching: [
          {
            // Delegate lookups: network-first for real-time accuracy
            urlPattern: /.*supabase\.co\/rest\/v1\/delegates.*/i,
            handler: 'NetworkFirst',
            options: {
              cacheName: 'delegates-cache',
              networkTimeoutSeconds: 4,
              expiration: { maxEntries: 1000, maxAgeSeconds: 60 * 60 * 24 },
            },
          },
          {
            // Agenda & static data: stale-while-revalidate
            urlPattern: /.*supabase\.co\/rest\/v1\/(agenda|sponsors|accommodation).*/i,
            handler: 'StaleWhileRevalidate',
            options: {
              cacheName: 'event-data-cache',
              expiration: { maxEntries: 500, maxAgeSeconds: 60 * 60 * 6 },
            },
          },
          {
            // Storage assets: cache-first
            urlPattern: /.*supabase\.co\/storage\/.*/i,
            handler: 'CacheFirst',
            options: {
              cacheName: 'storage-cache',
              expiration: { maxEntries: 200, maxAgeSeconds: 60 * 60 * 24 * 7 },
            },
          },
          {
            // Google Fonts
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
