import { createClient } from '@supabase/supabase-js'

const supabaseUrl     = import.meta.env.VITE_SUPABASE_URL     as string
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY as string

export const supabase = createClient(supabaseUrl || '', supabaseAnonKey || '', {
  auth:     { persistSession: false },
  realtime: { params: { eventsPerSecond: 5 } },
})

export const WP_REST = import.meta.env.VITE_WP_REST_URL as string ||
  'https://staging.lubabalo.co.za/awsisa/wp-json/awsisa/v1'
