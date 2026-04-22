import { createClient } from '@supabase/supabase-js'

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL as string
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY as string

if (!supabaseUrl || !supabaseAnonKey) {
  console.warn('[Awsisa] Supabase env vars not set. Set VITE_SUPABASE_URL and VITE_SUPABASE_ANON_KEY in .env')
}

// Bypass the Supabase Web Lock entirely. The lock is designed for multi-tab
// token-refresh coordination but causes deadlocks and AbortErrors in our
// single-tab admin context — gotrue-js force-steals the lock after 5 s,
// aborting the in-progress refresh and emitting a spurious SIGNED_OUT event
// which redirects the user back to /login mid-session.
// A no-op lock immediately executes the callback without acquiring a real
// browser Web Lock, which is safe here because only one tab runs this app.
const noopLock = <R>(
  _name: string,
  _timeout: number,
  fn: () => Promise<R>,
): Promise<R> => fn()

export const supabase = createClient(supabaseUrl || '', supabaseAnonKey || '', {
  auth: { persistSession: true, autoRefreshToken: true, lock: noopLock },
  realtime: { params: { eventsPerSecond: 10 } },
})
