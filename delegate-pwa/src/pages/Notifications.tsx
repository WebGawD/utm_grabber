import { Bell, CheckCheck } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'
import { useFlashAlerts } from '@/hooks/useFlashAlerts'

function getReadIds(): Set<string> {
  try { return new Set(JSON.parse(localStorage.getItem('awsisa_alerts_read_ids') || '[]')) }
  catch { return new Set<string>() }
}

export default function NotificationsPage() {
  const { alerts, unreadCount, markAllRead } = useFlashAlerts()
  const readIds = getReadIds()

  return (
    <div>
      {/* Header row */}
      <div style={{
        display: 'flex', alignItems: 'center', justifyContent: 'space-between',
        padding: '0.875rem 1rem', background: '#fff', borderBottom: '1px solid #E2E8F0',
      }}>
        <div>
          <h1 style={{ fontFamily: 'Outfit,sans-serif', fontSize: '1.125rem', fontWeight: 700, color: '#0F172A', margin: 0 }}>
            Announcements
          </h1>
          {unreadCount > 0 && (
            <span style={{ fontSize: '0.72rem', color: '#0D9488', fontWeight: 600 }}>
              {unreadCount} unread
            </span>
          )}
        </div>
        {unreadCount > 0 && (
          <button
            onClick={markAllRead}
            style={{
              display: 'flex', alignItems: 'center', gap: '0.375rem',
              color: '#0D9488', fontSize: '0.8rem', fontWeight: 600,
              background: '#F0FDFA', border: '1px solid #99F6E4',
              borderRadius: '0.5rem', padding: '0.4rem 0.75rem', cursor: 'pointer',
            }}
          >
            <CheckCheck size={14} /> Mark all read
          </button>
        )}
      </div>

      {/* Alert list */}
      <div style={{ padding: '0.75rem 1rem' }}>
        {alerts.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '4rem 1rem', color: '#94A3B8' }}>
            <Bell size={48} style={{ margin: '0 auto 1rem', opacity: 0.3, display: 'block' }} />
            <p style={{ margin: 0, fontWeight: 600, color: '#64748B' }}>No announcements yet</p>
            <p style={{ margin: '0.375rem 0 0', fontSize: '0.875rem' }}>
              Event updates will appear here in real-time
            </p>
          </div>
        ) : (
          alerts.map(alert => {
            const isUnread = !readIds.has(alert.id)
            return (
              <div
                key={alert.id}
                style={{
                  background: isUnread ? '#F0FDFA' : '#fff',
                  border: `1px solid ${isUnread ? '#99F6E4' : '#E2E8F0'}`,
                  borderRadius: '0.875rem',
                  padding: '1rem',
                  marginBottom: '0.625rem',
                  position: 'relative',
                }}
              >
                {isUnread && (
                  <div style={{
                    position: 'absolute', top: '1rem', right: '1rem',
                    width: 8, height: 8, borderRadius: '50%', background: '#0D9488',
                  }} />
                )}
                <div style={{
                  fontWeight: 700, fontSize: '0.9rem', color: '#0F172A',
                  marginBottom: '0.375rem', paddingRight: '1.25rem',
                }}>
                  {alert.title}
                </div>
                <div style={{ fontSize: '0.875rem', color: '#475569', lineHeight: 1.5, marginBottom: '0.5rem' }}>
                  {alert.body}
                </div>
                <div style={{ fontSize: '0.7rem', color: '#94A3B8' }}>
                  {alert.sent_at
                    ? formatDistanceToNow(new Date(alert.sent_at), { addSuffix: true })
                    : ''}
                </div>
              </div>
            )
          })
        )}
      </div>
    </div>
  )
}
