import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '@/hooks/useAuth'
import toast from 'react-hot-toast'

export default function LoginPage() {
  const { signIn, user, loading } = useAuth()
  const navigate                  = useNavigate()
  const [email, setEmail]         = useState('')
  const [password, setPassword]   = useState('')
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!loading && user) navigate('/dashboard', { replace: true })
  }, [user, loading, navigate])

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    setSubmitting(true)

    const { error } = await signIn(email.trim(), password)
    if (error) {
      toast.error(error.message ?? 'Invalid credentials. Please try again.')
    } else {
      toast.success('Welcome back!')
      navigate('/dashboard', { replace: true })
    }
    setSubmitting(false)
  }

  return (
    <div className="min-h-screen bg-slate-950 flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        {/* Logo */}
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" aria-hidden="true">
              <path d="M12 2C8 2 4 8 4 14s4 8 8 8 8-2 8-8S16 2 12 2z"/>
              <path d="M8 12c0-2.2 1.8-4 4-4s4 1.8 4 4-1.8 4-4 4"/>
            </svg>
          </div>
          <h1 className="text-2xl font-heading font-bold text-white">AWSISA Admin</h1>
          <p className="text-slate-400 text-sm mt-1">Watersan Dialogue 2026 · Command Center</p>
        </div>

        {/* Login form */}
        <div className="bg-slate-900 border border-slate-800 rounded-2xl p-6">
          <h2 className="text-lg font-semibold text-white mb-6">Sign in to your account</h2>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="label" htmlFor="email">Email address</label>
              <input
                id="email"
                type="email"
                className="input"
                placeholder="you@organisation.com"
                value={email}
                onChange={e => setEmail(e.target.value)}
                required
                autoComplete="email"
                autoFocus
              />
            </div>

            <div>
              <label className="label" htmlFor="password">Password</label>
              <input
                id="password"
                type="password"
                className="input"
                placeholder="••••••••"
                value={password}
                onChange={e => setPassword(e.target.value)}
                required
                autoComplete="current-password"
              />
            </div>

            <button
              type="submit"
              disabled={submitting}
              className="btn-primary w-full justify-center py-3 text-base mt-2"
            >
              {submitting ? (
                <span className="flex items-center gap-2">
                  <span className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                  Signing in…
                </span>
              ) : 'Sign In'}
            </button>
          </form>
        </div>

        <p className="text-center text-slate-600 text-xs mt-6">
          AWSISA Watersan Dialogue 2026 · Staff Access Only
        </p>
      </div>
    </div>
  )
}
