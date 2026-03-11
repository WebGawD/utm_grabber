import { useState, useEffect, useRef } from 'react'
import { useNavigate, useLocation } from 'react-router-dom'
import { Mail, ArrowRight, ArrowLeft, KeyRound, Loader2 } from 'lucide-react'
import toast from 'react-hot-toast'
import { useAuth } from '@/hooks/useAuth'
import type { DelegateSession } from '@/types'

const REST = 'https://staging.lubabalo.co.za/awsisa/wp-json/awsisa/v1'

type Stage = 'email' | 'otp' | 'loading'

function mapDelegate(d: Record<string, unknown>): DelegateSession {
  return {
    delegateId:   d.id as string,
    email:        d.email as string,
    name:         `${d.first_name} ${d.last_name}`.trim(),
    firstName:    d.first_name as string,
    lastName:     d.last_name as string,
    type:         d.delegate_type as string,
    organisation: (d.organisation as string) || '',
    qrToken:      d.qr_code_token as string,
    expiresAt:    0, // set by login()
  }
}

export default function LoginPage() {
  const { login, isLoggedIn } = useAuth()
  const navigate   = useNavigate()
  const location   = useLocation()
  const returnTo   = (location.state as { returnTo?: string } | null)?.returnTo || '/home'
  const [stage, setStage] = useState<Stage>('email')
  const [email, setEmail] = useState('')
  const [otp, setOtp]     = useState('')
  const [error, setError] = useState('')
  const otpRef = useRef<HTMLInputElement>(null)

  // Redirect if already logged in
  useEffect(() => {
    if (isLoggedIn) navigate(returnTo, { replace: true })
  }, [isLoggedIn, navigate, returnTo])

  // Check for QR token in URL on mount
  useEffect(() => {
    const params = new URLSearchParams(window.location.search)
    const hash   = new URLSearchParams(window.location.hash.replace(/^#\??/, ''))
    const qr     = params.get('qr') || hash.get('qr')
    if (qr) handleQrLogin(qr)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  async function handleQrLogin(token: string) {
    setStage('loading')
    try {
      const res = await fetch(`${REST}/delegate/auth/qr/${encodeURIComponent(token)}`)
      if (!res.ok) throw new Error('QR code not recognised')
      const { delegate } = await res.json()
      login(mapDelegate(delegate))
      navigate(returnTo, { replace: true })
    } catch (e) {
      setError((e as Error).message)
      setStage('email')
    }
  }

  async function handleSendOtp(e: React.FormEvent) {
    e.preventDefault()
    setError('')
    if (!email.includes('@')) {
      setError('Please enter a valid email address.')
      return
    }
    setStage('loading')
    try {
      const res = await fetch(`${REST}/delegate/auth/request-otp`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ email }),
      })
      const j = await res.json()
      if (!res.ok) throw new Error(j.message || 'Could not send code. Are you registered?')
      setStage('otp')
      setTimeout(() => otpRef.current?.focus(), 100)
    } catch (e) {
      setError((e as Error).message)
      setStage('email')
    }
  }

  async function handleVerifyOtp(e: React.FormEvent) {
    e.preventDefault()
    setError('')
    if (otp.length !== 6) {
      setError('Enter the 6-digit code from your email.')
      return
    }
    setStage('loading')
    try {
      const res = await fetch(`${REST}/delegate/auth/verify-otp`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ email, code: otp }),
      })
      const j = await res.json()
      if (!res.ok || !j.delegate) throw new Error(j.message || 'Invalid or expired code.')
      login(mapDelegate(j.delegate))
      toast.success(`Welcome, ${j.delegate.first_name}!`)
      navigate(returnTo, { replace: true })
    } catch (e) {
      setError((e as Error).message)
      setStage('otp')
    }
  }

  const isLoading = stage === 'loading'

  return (
    <div
      style={{
        minHeight: '100dvh',
        background: 'linear-gradient(135deg, #115E59 0%, #0D9488 60%, #5EEAD4 100%)',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '1.5rem',
      }}
    >
      {/* Branding */}
      <div style={{ textAlign: 'center', marginBottom: '2rem' }}>
        <div
          style={{
            fontFamily: 'Outfit, sans-serif',
            fontSize: '1.75rem',
            fontWeight: 800,
            color: '#fff',
            marginBottom: '0.25rem',
          }}
        >
          AWSISA Africa
        </div>
        <div style={{ color: '#CCFBF1', fontSize: '0.875rem', fontWeight: 600 }}>
          Water &amp; Sanitation Dialogue 2026
        </div>
        <div style={{ color: '#99F6E4', fontSize: '0.75rem', marginTop: '0.25rem' }}>
          ICC Durban &middot; 9&ndash;12 November
        </div>
      </div>

      {/* Card */}
      <div
        style={{
          background: '#fff',
          borderRadius: '1.25rem',
          padding: '2rem',
          width: '100%',
          maxWidth: '380px',
          boxShadow: '0 20px 60px rgba(0,0,0,0.2)',
        }}
      >
        {isLoading ? (
          /* Loading state */
          <div style={{ textAlign: 'center', padding: '2rem 0' }}>
            <Loader2
              size={40}
              style={{
                color: '#0D9488',
                animation: 'spin 1s linear infinite',
                margin: '0 auto 1rem',
                display: 'block',
              }}
            />
            <p style={{ color: '#64748B', fontSize: '0.9rem', margin: 0 }}>
              {otp.length > 0 ? 'Verifying code\u2026' : 'Sending code\u2026'}
            </p>
          </div>
        ) : stage === 'email' ? (
          /* Email stage */
          <>
            <h1
              style={{
                fontFamily: 'Outfit, sans-serif',
                fontSize: '1.375rem',
                fontWeight: 700,
                color: '#0F172A',
                margin: '0 0 0.375rem',
              }}
            >
              Sign In
            </h1>
            <p
              style={{
                color: '#64748B',
                fontSize: '0.875rem',
                margin: '0 0 1.5rem',
                lineHeight: 1.5,
                fontFamily: 'Inter, sans-serif',
              }}
            >
              Enter the email you registered with. We&rsquo;ll send you a 6-digit access code.
            </p>
            <form onSubmit={handleSendOtp}>
              <label
                style={{
                  display: 'block',
                  fontWeight: 600,
                  fontSize: '0.875rem',
                  color: '#374151',
                  marginBottom: '0.375rem',
                  fontFamily: 'Inter, sans-serif',
                }}
              >
                Email Address
              </label>
              <div style={{ position: 'relative', marginBottom: error ? '0.75rem' : '1.25rem' }}>
                <Mail
                  size={16}
                  style={{
                    position: 'absolute',
                    left: '0.875rem',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    color: '#94A3B8',
                  }}
                />
                <input
                  type="email"
                  value={email}
                  onChange={e => {
                    setEmail(e.target.value)
                    setError('')
                  }}
                  placeholder="you@organisation.com"
                  autoComplete="email"
                  required
                  style={{
                    width: '100%',
                    paddingLeft: '2.5rem',
                    paddingRight: '1rem',
                    paddingTop: '0.75rem',
                    paddingBottom: '0.75rem',
                    border: `1.5px solid ${error ? '#EF4444' : '#E2E8F0'}`,
                    borderRadius: '0.625rem',
                    fontSize: '1rem',
                    outline: 'none',
                    boxSizing: 'border-box',
                    fontFamily: 'Inter, sans-serif',
                    color: '#0F172A',
                    background: '#fff',
                  }}
                />
              </div>
              {error && (
                <p
                  style={{
                    color: '#EF4444',
                    fontSize: '0.8rem',
                    marginBottom: '1rem',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.375rem',
                    fontFamily: 'Inter, sans-serif',
                  }}
                >
                  &#9888;&#65039; {error}
                </p>
              )}
              <button
                type="submit"
                style={{
                  width: '100%',
                  background: '#0D9488',
                  color: '#fff',
                  fontWeight: 700,
                  fontSize: '1rem',
                  padding: '0.875rem',
                  borderRadius: '0.75rem',
                  border: 'none',
                  cursor: 'pointer',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  gap: '0.5rem',
                  fontFamily: 'Outfit, sans-serif',
                }}
              >
                Send Code <ArrowRight size={18} />
              </button>
            </form>
          </>
        ) : (
          /* OTP stage */
          <>
            <button
              onClick={() => {
                setStage('email')
                setOtp('')
                setError('')
              }}
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: '0.375rem',
                color: '#0D9488',
                fontSize: '0.875rem',
                fontWeight: 600,
                background: 'none',
                border: 'none',
                cursor: 'pointer',
                padding: 0,
                marginBottom: '1rem',
                fontFamily: 'Inter, sans-serif',
              }}
            >
              <ArrowLeft size={16} /> Back
            </button>
            <h1
              style={{
                fontFamily: 'Outfit, sans-serif',
                fontSize: '1.375rem',
                fontWeight: 700,
                color: '#0F172A',
                margin: '0 0 0.375rem',
              }}
            >
              Enter Your Code
            </h1>
            <p
              style={{
                color: '#64748B',
                fontSize: '0.875rem',
                margin: '0 0 1.5rem',
                lineHeight: 1.5,
                fontFamily: 'Inter, sans-serif',
              }}
            >
              We sent a 6-digit code to{' '}
              <strong style={{ color: '#0F172A' }}>{email}</strong>. Check your inbox (and spam
              folder).
            </p>
            <form onSubmit={handleVerifyOtp}>
              <label
                style={{
                  display: 'block',
                  fontWeight: 600,
                  fontSize: '0.875rem',
                  color: '#374151',
                  marginBottom: '0.375rem',
                  fontFamily: 'Inter, sans-serif',
                }}
              >
                6-Digit Code
              </label>
              <div style={{ position: 'relative', marginBottom: error ? '0.75rem' : '1.25rem' }}>
                <KeyRound
                  size={16}
                  style={{
                    position: 'absolute',
                    left: '0.875rem',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    color: '#94A3B8',
                  }}
                />
                <input
                  ref={otpRef}
                  type="text"
                  inputMode="numeric"
                  pattern="[0-9]*"
                  maxLength={6}
                  value={otp}
                  onChange={e => {
                    setOtp(e.target.value.replace(/\D/g, ''))
                    setError('')
                  }}
                  placeholder="123456"
                  autoComplete="one-time-code"
                  style={{
                    width: '100%',
                    paddingLeft: '2.5rem',
                    paddingRight: '1rem',
                    paddingTop: '0.875rem',
                    paddingBottom: '0.875rem',
                    border: `1.5px solid ${error ? '#EF4444' : '#E2E8F0'}`,
                    borderRadius: '0.625rem',
                    fontSize: '1.5rem',
                    letterSpacing: '0.25em',
                    textAlign: 'center',
                    outline: 'none',
                    boxSizing: 'border-box',
                    fontFamily: 'monospace',
                    color: '#0F172A',
                    background: '#fff',
                  }}
                />
              </div>
              {error && (
                <p
                  style={{
                    color: '#EF4444',
                    fontSize: '0.8rem',
                    marginBottom: '1rem',
                    fontFamily: 'Inter, sans-serif',
                  }}
                >
                  &#9888;&#65039; {error}
                </p>
              )}
              <button
                type="submit"
                disabled={otp.length !== 6}
                style={{
                  width: '100%',
                  background: otp.length === 6 ? '#0D9488' : '#E2E8F0',
                  color: otp.length === 6 ? '#fff' : '#94A3B8',
                  fontWeight: 700,
                  fontSize: '1rem',
                  padding: '0.875rem',
                  borderRadius: '0.75rem',
                  border: 'none',
                  cursor: otp.length === 6 ? 'pointer' : 'not-allowed',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  gap: '0.5rem',
                  fontFamily: 'Outfit, sans-serif',
                  transition: 'all 0.15s',
                }}
              >
                Enter App <ArrowRight size={18} />
              </button>
            </form>
          </>
        )}
      </div>

      <p
        style={{
          color: '#CCFBF1',
          fontSize: '0.75rem',
          marginTop: '1.5rem',
          textAlign: 'center',
          fontFamily: 'Inter, sans-serif',
        }}
      >
        AWSISA 2026 &middot; Powered by Lubabalo
      </p>

      <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
    </div>
  )
}
