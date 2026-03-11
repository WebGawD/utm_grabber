import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import { supabase } from '@/lib/supabase'
import type { User, Session } from '@supabase/supabase-js'
import type { StaffUser } from '@/types'

interface AuthState {
  user:       User | null
  session:    Session | null
  staffUser:  StaffUser | null
  loading:    boolean
  isAdmin:    boolean
  isStaff:    boolean
}

interface AuthContextValue extends AuthState {
  signIn:  (email: string, password: string) => Promise<{ error: Error | null }>
  signOut: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | null>(null)

/**
 * AuthProvider — wraps the whole app so getSession() runs exactly ONCE
 * per page load and all components share the same auth state.
 * This eliminates the Web Lock deadlock race that caused "stuck on /login".
 */
export function AuthProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<AuthState>({
    user:      null,
    session:   null,
    staffUser: null,
    loading:   true,
    isAdmin:   false,
    isStaff:   false,
  })

  useEffect(() => {
    // Race getSession() against a 6 s timeout — Supabase's Web Lock can
    // deadlock if a previous tab held the lock. Without this the spinner
    // hangs indefinitely. Now this runs only ONCE for the whole app.
    const timeout = new Promise<never>((_, reject) =>
      setTimeout(() => reject(new Error('auth_timeout')), 6000)
    )
    Promise.race([supabase.auth.getSession(), timeout])
      .then(async ({ data: { session } }: Awaited<ReturnType<typeof supabase.auth.getSession>>) => {
        await resolveSession(session)
      })
      .catch(() => {
        setState({ user: null, session: null, staffUser: null, loading: false, isAdmin: false, isStaff: false })
      })

    const { data: { subscription } } = supabase.auth.onAuthStateChange(
      async (_event, session) => {
        await resolveSession(session)
      }
    )

    return () => subscription.unsubscribe()
  }, [])

  async function resolveSession(session: Session | null) {
    if (!session) {
      setState({ user: null, session: null, staffUser: null, loading: false, isAdmin: false, isStaff: false })
      return
    }

    try {
      const { data: staffUser } = await supabase
        .from('staff_users')
        .select('*')
        .eq('id', session.user.id)
        .single()

      setState({
        user:      session.user,
        session,
        staffUser: staffUser ?? null,
        loading:   false,
        isAdmin:   ['admin', 'super_admin'].includes(staffUser?.role ?? ''),
        isStaff:   ['staff', 'admin', 'super_admin', 'volunteer'].includes(staffUser?.role ?? ''),
      })
    } catch {
      setState({ user: null, session: null, staffUser: null, loading: false, isAdmin: false, isStaff: false })
    }
  }

  async function signIn(email: string, password: string) {
    const { error } = await supabase.auth.signInWithPassword({ email, password })
    return { error: error as Error | null }
  }

  async function signOut() {
    await supabase.auth.signOut()
  }

  return (
    <AuthContext.Provider value={{ ...state, signIn, signOut }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within <AuthProvider>')
  return ctx
}
