import { useState, useEffect, useRef } from 'react'
import toast from 'react-hot-toast'
import { supabase } from '@/lib/supabase'
import type { FlashAlert } from '@/types'

const READ_KEY = 'awsisa_alerts_read_ids'

function getReadIds(): Set<string> {
  try {
    return new Set(JSON.parse(localStorage.getItem(READ_KEY) || '[]'))
  } catch { return new Set() }
}

function markRead(ids: string[]) {
  const current = getReadIds()
  ids.forEach(id => current.add(id))
  localStorage.setItem(READ_KEY, JSON.stringify([...current]))
}

export function useFlashAlerts() {
  const [alerts, setAlerts]       = useState<FlashAlert[]>([])
  const [unreadCount, setUnread]  = useState(0)
  const channelRef                = useRef<ReturnType<typeof supabase.channel> | null>(null)

  // Load initial alerts from Supabase
  useEffect(() => {
    supabase
      .from('flash_alerts')
      .select('id,title,body,audience,status,sent_at')
      .eq('status', 'sent')
      .eq('audience', 'all')
      .order('sent_at', { ascending: false })
      .limit(50)
      .then(({ data }) => {
        if (data) {
          setAlerts(data as FlashAlert[])
          const readIds = getReadIds()
          setUnread(data.filter(a => !readIds.has(a.id)).length)
        }
      })

    // Subscribe to new alerts
    channelRef.current = supabase
      .channel('delegate-flash-alerts')
      .on(
        'postgres_changes',
        { event: 'INSERT', schema: 'public', table: 'flash_alerts', filter: 'status=eq.sent' },
        (payload) => {
          const alert = payload.new as FlashAlert
          if (alert.audience === 'all') {
            setAlerts(prev => [alert, ...prev])
            setUnread(n => n + 1)
            toast(alert.body || alert.title, {
              duration: 8000,
              icon: '📢',
              style: { background: '#0F172A', color: '#F1F5F9', border: '1px solid #0D9488' },
            })
          }
        }
      )
      .subscribe()

    return () => {
      channelRef.current?.unsubscribe()
    }
  }, [])

  const markAllRead = () => {
    markRead(alerts.map(a => a.id))
    setUnread(0)
  }

  return { alerts, unreadCount, markAllRead }
}
