import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import DonateApp from './DonateApp'

const el = document.getElementById('donate-app')
if (el) {
  createRoot(el).render(
    <StrictMode>
      <DonateApp />
    </StrictMode>
  )
}
