import { useState, useEffect } from 'react'
import { supabase } from '@/lib/supabase'
import type { DashboardStats } from '@/types'

/** Real-time dashboard stats via Supabase channel subscriptions */
export function useRealtimeDelegates() {
  const [stats, setStats] = useState<DashboardStats | null>(null)
  const [loading, setLoading] = useState(true)

  async function fetchStats() {
    const [delegatesRes, donationsRes, bookingsRes, tapsRes] = await Promise.all([
      supabase.from('delegates').select('delegate_type, checked_in, country'),
      supabase.from('donations').select('amount_zar').eq('payment_status', 'completed'),
      supabase.from('accommodation_bookings').select('id').eq('payment_status', 'paid'),
      supabase.from('nfc_taps').select('id'),
    ])

    const delegates  = delegatesRes.data  ?? []
    const donations  = donationsRes.data  ?? []
    const bookings   = bookingsRes.data   ?? []
    const taps       = tapsRes.data       ?? []

    const byType: Record<string, number> = {}
    const byCountry: Record<string, number> = {}
    let checkedIn = 0

    for (const d of delegates) {
      byType[d.delegate_type] = (byType[d.delegate_type] ?? 0) + 1
      byCountry[d.country]    = (byCountry[d.country]    ?? 0) + 1
      if (d.checked_in) checkedIn++
    }

    const totalDonations = donations.reduce((sum, d) => sum + (d.amount_zar ?? 0), 0)

    setStats({
      total_delegates:     delegates.length,
      checked_in:          checkedIn,
      total_donations_zar: totalDonations,
      total_bookings:      bookings.length,
      nfc_taps:            taps.length,
      by_type:             byType as DashboardStats['by_type'],
      by_country:          Object.entries(byCountry)
                             .map(([country, count]) => ({ country, count }))
                             .sort((a, b) => b.count - a.count)
                             .slice(0, 10),
    })
    setLoading(false)
  }

  useEffect(() => {
    fetchStats()

    // Subscribe to real-time changes on delegates and checkin_log.
    const channel = supabase
      .channel('dashboard-stats')
      .on('postgres_changes', { event: '*', schema: 'public', table: 'delegates' }, () => fetchStats())
      .on('postgres_changes', { event: 'INSERT', schema: 'public', table: 'checkin_log' }, () => fetchStats())
      .subscribe()

    return () => { supabase.removeChannel(channel) }
  }, [])

  return { stats, loading, refresh: fetchStats }
}
