import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import AccommodationApp from './AccommodationApp'

const el = document.getElementById('accommodation-app')
if (el) {
  createRoot(el).render(
    <StrictMode>
      <AccommodationApp />
    </StrictMode>
  )
}
