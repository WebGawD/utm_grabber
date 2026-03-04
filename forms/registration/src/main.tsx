import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import RegistrationApp from './RegistrationApp'

const el = document.getElementById('registration-app')
if (el) {
  createRoot(el).render(
    <StrictMode>
      <RegistrationApp />
    </StrictMode>
  )
}
