import { useState, useEffect, useCallback } from 'react'
import type { DelegateSession } from '@/types'

const SESSION_KEY   = 'awsisa_delegate_session'
const SESSION_DAYS  = 7

export function useAuth() {
  const [session, setSession]   = useState<DelegateSession | null>(null)
  const [loading, setLoading]   = useState(true)

  useEffect(() => {
    const raw = localStorage.getItem(SESSION_KEY)
    if (raw) {
      try {
        const parsed: DelegateSession = JSON.parse(raw)
        if (parsed.expiresAt > Date.now()) {
          setSession(parsed)
        } else {
          localStorage.removeItem(SESSION_KEY)
        }
      } catch {
        localStorage.removeItem(SESSION_KEY)
      }
    }
    setLoading(false)
  }, [])

  const login = useCallback((delegate: DelegateSession) => {
    const sess: DelegateSession = {
      ...delegate,
      expiresAt: Date.now() + SESSION_DAYS * 24 * 60 * 60 * 1000,
    }
    localStorage.setItem(SESSION_KEY, JSON.stringify(sess))
    setSession(sess)
  }, [])

  const logout = useCallback(() => {
    localStorage.removeItem(SESSION_KEY)
    setSession(null)
  }, [])

  return { session, loading, login, logout, isLoggedIn: !!session }
}
