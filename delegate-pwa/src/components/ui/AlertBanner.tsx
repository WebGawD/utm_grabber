import { AlertTriangle, X } from 'lucide-react'
import { useState } from 'react'

interface AlertBannerProps {
  message: string
  onDismiss?: () => void
}

export default function AlertBanner({ message, onDismiss }: AlertBannerProps) {
  const [dismissed, setDismissed] = useState(false)

  if (dismissed) return null

  function handleDismiss() {
    setDismissed(true)
    onDismiss?.()
  }

  return (
    <div
      style={{
        background: '#FFF7ED',
        border: '1px solid #FED7AA',
        borderLeft: '4px solid #F97316',
        borderRadius: '0.75rem',
        padding: '0.75rem 1rem',
        marginBottom: '1rem',
        display: 'flex',
        alignItems: 'flex-start',
        gap: '0.625rem',
      }}
    >
      <AlertTriangle
        size={16}
        style={{ color: '#EA580C', flexShrink: 0, marginTop: '1px' }}
      />
      <p
        style={{
          flex: 1,
          fontSize: '0.85rem',
          color: '#7C2D12',
          lineHeight: 1.5,
          margin: 0,
          fontFamily: 'Inter, sans-serif',
        }}
      >
        {message}
      </p>
      <button
        onClick={handleDismiss}
        aria-label="Dismiss alert"
        style={{
          background: 'none',
          border: 'none',
          cursor: 'pointer',
          padding: '0',
          color: '#9A3412',
          flexShrink: 0,
          lineHeight: 1,
        }}
      >
        <X size={15} />
      </button>
    </div>
  )
}
