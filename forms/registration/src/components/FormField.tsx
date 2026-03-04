import type { ReactNode } from 'react'

interface Props {
  label: string
  hint?: string
  required?: boolean
  children: ReactNode
  error?: string
}

export function FormField({ label, hint, required, children, error }: Props) {
  return (
    <div className="form-group" style={{ marginBottom: '1.125rem' }}>
      <label className="form-label" style={{ display: 'block', fontWeight: 600, fontSize: '.875rem', color: '#374151', marginBottom: '.375rem' }}>
        {label}
        {required && <span style={{ color: '#EF4444', marginLeft: '.2rem' }}>*</span>}
      </label>
      {children}
      {hint && !error && (
        <p style={{ fontSize: '.75rem', color: '#94A3B8', margin: '.3rem 0 0' }}>{hint}</p>
      )}
      {error && (
        <p role="alert" style={{ fontSize: '.75rem', color: '#EF4444', margin: '.3rem 0 0' }}>{error}</p>
      )}
    </div>
  )
}
