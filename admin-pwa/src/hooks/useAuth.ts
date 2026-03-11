import { useState, useEffect } from 'react'
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

export function useAuth(): AuthState & {
  signIn:  (email: string, password: string) => Promise<{ error: Error | null }>
  signOut: () => Promise<void>
} {
  const [state, setState] = useState<AuthState>({
    user:      null,
    session:   null,
    staffUser: null,
    loading:   true,
    isAdmin:   false,
    isStaff:   false,
  })

  useEffect(() => {
    // Initialise session on mount.
    // Race against a 6 s timeout — Supabase's Web Lock can deadlock if a
    // previous tab held the lock; without this the spinner hangs indefinitely.
    const timeout = new Promise<never>((_, reject) =>
      setTimeout(() => reject(new Error('auth_timeout')), 6000)
    )
    Promise.race([supabase.auth.getSession(), timeout])
      .then(async ({ data: { session } }: Awaited<ReturnType<typeof supabase.auth.getSession>>) => {
        await resolveSession(session)
      })
      .catch(() => {
        // getSession failed or timed out — treat as logged out.
        setState({ user: null, session: null, staffUser: null, loading: false, isAdmin: false, isStaff: false })
      })

    // Listen for auth changes.
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
      // staff_users query failed — clear session and stop loading.
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

  return { ...state, signIn, signOut }
}
