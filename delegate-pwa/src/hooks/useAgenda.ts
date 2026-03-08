import { useState, useEffect } from 'react'
import type { AgendaSession } from '@/types'

const CACHE_KEY = 'awsisa_agenda_cache'
const CACHE_TTL = 60 * 60 * 1000  // 1 hour

export const CONFERENCE_DAYS = [
  { date: '2026-11-09', label: 'Day 1', short: 'Mon 9' },
  { date: '2026-11-10', label: 'Day 2', short: 'Tue 10' },
  { date: '2026-11-11', label: 'Day 3', short: 'Wed 11' },
  { date: '2026-11-12', label: 'Day 4', short: 'Thu 12' },
]

export const TRACKS = [
  { value: '', label: 'All' },
  { value: 'plenary',    label: 'Plenary',    color: '#0D9488' },
  { value: 'water',      label: 'Water',       color: '#3B82F6' },
  { value: 'sanitation', label: 'Sanitation',  color: '#16A34A' },
  { value: 'innovation', label: 'Innovation',  color: '#F59E0B' },
  { value: 'policy',     label: 'Policy',      color: '#8B5CF6' },
  { value: 'networking', label: 'Networking',  color: '#EC4899' },
]

export function useAgenda() {
  const [sessions, setSessions] = useState<AgendaSession[]>([])
  const [loading, setLoading]   = useState(true)
  const [error, setError]       = useState('')

  useEffect(() => {
    // Try cache first
    try {
      const cached = JSON.parse(localStorage.getItem(CACHE_KEY) || 'null')
      if (cached && cached.ts && Date.now() - cached.ts < CACHE_TTL) {
        setSessions(cached.data)
        setLoading(false)
        return
      }
    } catch { /* ignore */ }

    // Fetch from WP REST (uses existing /awsisa/v1/agenda endpoint)
    fetch('https://staging.lubabalo.co.za/awsisa/wp-json/awsisa/v1/agenda')
      .then(r => r.json())
      .then((data: AgendaSession[]) => {
        if (Array.isArray(data)) {
          setSessions(data)
          localStorage.setItem(CACHE_KEY, JSON.stringify({ ts: Date.now(), data }))
        }
      })
      .catch(e => setError(e.message))
      .finally(() => setLoading(false))
  }, [])

  return { sessions, loading, error }
}

export function groupByDay(sessions: AgendaSession[]): Record<string, AgendaSession[]> {
  return sessions.reduce((acc, s) => {
    const day = s.day
    if (!acc[day]) acc[day] = []
    acc[day].push(s)
    return acc
  }, {} as Record<string, AgendaSession[]>)
}

export function formatTime(t: string) {
  if (!t) return ''
  const [h, m] = t.split(':')
  const hour = parseInt(h, 10)
  const ampm = hour >= 12 ? 'PM' : 'AM'
  const h12  = hour % 12 || 12
  return `${h12}:${m} ${ampm}`
}
