import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { CalendarDays, MapPin, QrCode, Bell, Droplets } from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import { useFlashAlerts } from '@/hooks/useFlashAlerts'
import { useAgenda, formatTime, CONFERENCE_DAYS } from '@/hooks/useAgenda'
import AlertBanner from '@/components/ui/AlertBanner'
import type { AgendaSession } from '@/types'

const CONF_START = new Date('2026-11-09T07:00:00Z') // 09:00 SAST = 07:00 UTC

const DELEGATE_TYPE_COLORS: Record<string, { bg: string; text: string; label: string }> = {
  government:     { bg: '#DBEAFE', text: '#1D4ED8', label: 'Government' },
  utility:        { bg: '#FEF3C7', text: '#92400E', label: 'Utility' },
  private_sector: { bg: '#F3F4F6', text: '#374151', label: 'Private Sector' },
  ngo:            { bg: '#DCFCE7', text: '#166534', label: 'NGO / NPO' },
  academic:       { bg: '#EDE9FE', text: '#6D28D9', label: 'Academic' },
  media:          { bg: '#FEE2E2', text: '#991B1B', label: 'Media' },
  exhibitor:      { bg: '#F0FDFA', text: '#0F766E', label: 'Exhibitor' },
  sponsor:        { bg: '#FFF7ED', text: '#C2410C', label: 'Sponsor' },
}

const TRACK_COLORS: Record<string, string> = {
  plenary:    '#0D9488',
  water:      '#3B82F6',
  sanitation: '#16A34A',
  innovation: '#F59E0B',
  policy:     '#8B5CF6',
  networking: '#EC4899',
}

function Countdown() {
  const [diff, setDiff] = useState(CONF_START.getTime() - Date.now())

  useEffect(() => {
    if (diff <= 0) return
    const id = setInterval(() => setDiff(CONF_START.getTime() - Date.now()), 1000)
    return () => clearInterval(id)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  if (diff <= 0) {
    return (
      <div
        style={{
          textAlign: 'center',
          color: '#0D9488',
          fontWeight: 700,
          fontSize: '1rem',
          padding: '0.5rem 0',
          fontFamily: 'Outfit, sans-serif',
        }}
      >
        The conference is happening now!
      </div>
    )
  }

  const days  = Math.floor(diff / 86400000)
  const hours = Math.floor((diff % 86400000) / 3600000)
  const mins  = Math.floor((diff % 3600000) / 60000)

  return (
    <div style={{ display: 'flex', justifyContent: 'center', gap: '1rem', margin: '0.5rem 0' }}>
      {([['days', days], ['hrs', hours], ['min', mins]] as [string, number][]).map(
        ([label, val]) => (
          <div key={label} style={{ textAlign: 'center' }}>
            <div
              style={{
                fontFamily: 'Outfit, sans-serif',
                fontSize: '2rem',
                fontWeight: 800,
                color: '#0D9488',
                lineHeight: 1,
              }}
            >
              {String(val).padStart(2, '0')}
            </div>
            <div
              style={{
                fontSize: '0.7rem',
                color: '#94A3B8',
                textTransform: 'uppercase',
                letterSpacing: '0.05em',
                marginTop: '2px',
                fontFamily: 'Inter, sans-serif',
              }}
            >
              {label}
            </div>
          </div>
        )
      )}
    </div>
  )
}

function getTodaySessions(sessions: AgendaSession[]): AgendaSession[] {
  const todayDate = new Date().toISOString().split('T')[0]
  const now       = new Date()
  const nowTime   = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:00`

  return sessions
    .filter(
      s =>
        s.day === todayDate &&
        s.is_published &&
        s.start_time >= nowTime &&
        s.session_type !== 'break'
    )
    .sort((a, b) => a.start_time.localeCompare(b.start_time))
    .slice(0, 2)
}

export default function HomePage() {
  const { session }               = useAuth()
  const { alerts, unreadCount }   = useFlashAlerts()
  const { sessions }              = useAgenda()
  const navigate                  = useNavigate()
  const todaySessions             = getTodaySessions(sessions)
  const latestAlert               = alerts.find(a => a.audience === 'all')
  const typeInfo                  =
    DELEGATE_TYPE_COLORS[session?.type ?? ''] ??
    { bg: '#F0FDFA', text: '#0F766E', label: session?.type ?? '' }
  const isConferenceWeek          = CONFERENCE_DAYS.some(
    d => d.date === new Date().toISOString().split('T')[0]
  )

  return (
    <div
      style={{
        padding: '1rem',
        maxWidth: 480,
        margin: '0 auto',
        fontFamily: 'Inter, sans-serif',
      }}
    >
      {/* Flash alert banner */}
      {latestAlert && <AlertBanner message={latestAlert.body} />}

      {/* Greeting */}
      <div style={{ marginBottom: '1.25rem' }}>
        <div
          style={{
            display: 'flex',
            alignItems: 'center',
            gap: '0.625rem',
            flexWrap: 'wrap',
          }}
        >
          <h1
            style={{
              fontFamily: 'Outfit, sans-serif',
              fontSize: '1.5rem',
              fontWeight: 800,
              color: '#0F172A',
              margin: 0,
            }}
          >
            Welcome, {session?.firstName || 'Delegate'}!
          </h1>
          <span
            style={{
              background: typeInfo.bg,
              color: typeInfo.text,
              fontSize: '0.7rem',
              fontWeight: 700,
              padding: '0.2rem 0.6rem',
              borderRadius: '999px',
              whiteSpace: 'nowrap',
              fontFamily: 'Inter, sans-serif',
            }}
          >
            {typeInfo.label}
          </span>
        </div>
        {session?.organisation && (
          <p
            style={{
              color: '#64748B',
              fontSize: '0.875rem',
              margin: '0.25rem 0 0',
              fontFamily: 'Inter, sans-serif',
            }}
          >
            {session.organisation}
          </p>
        )}
      </div>

      {/* Countdown card */}
      <div
        style={{
          background: 'linear-gradient(135deg, #F0FDFA, #fff)',
          border: '1px solid #99F6E4',
          borderRadius: '1rem',
          padding: '1.25rem',
          marginBottom: '1.25rem',
          textAlign: 'center',
        }}
      >
        <div
          style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            gap: '0.375rem',
            marginBottom: '0.625rem',
          }}
        >
          <Droplets size={16} style={{ color: '#0D9488' }} />
          <span
            style={{
              fontSize: '0.75rem',
              fontWeight: 600,
              color: '#0D9488',
              textTransform: 'uppercase',
              letterSpacing: '0.05em',
              fontFamily: 'Outfit, sans-serif',
            }}
          >
            Conference opens in
          </span>
        </div>
        <Countdown />
        <div
          style={{
            fontSize: '0.75rem',
            color: '#94A3B8',
            marginTop: '0.5rem',
            fontFamily: 'Inter, sans-serif',
          }}
        >
          9 Nov 2026 &middot; ICC Durban
        </div>
      </div>

      {/* Today's sessions */}
      <div style={{ marginBottom: '1.25rem' }}>
        <div
          style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            marginBottom: '0.75rem',
          }}
        >
          <h2
            style={{
              fontFamily: 'Outfit, sans-serif',
              fontSize: '1rem',
              fontWeight: 700,
              color: '#0F172A',
              margin: 0,
            }}
          >
            {isConferenceWeek ? "Today's Sessions" : 'Conference Programme'}
          </h2>
          <button
            onClick={() => navigate('/agenda')}
            style={{
              color: '#0D9488',
              fontSize: '0.8rem',
              fontWeight: 600,
              background: 'none',
              border: 'none',
              cursor: 'pointer',
              fontFamily: 'Inter, sans-serif',
            }}
          >
            View all &rarr;
          </button>
        </div>

        {!isConferenceWeek ? (
          <div
            style={{
              background: '#F8FAFC',
              borderRadius: '0.75rem',
              padding: '1rem',
              textAlign: 'center',
              color: '#64748B',
              fontSize: '0.875rem',
              fontFamily: 'Inter, sans-serif',
            }}
          >
            Full agenda available from{' '}
            <strong style={{ color: '#0F172A' }}>9 November 2026</strong>
          </div>
        ) : todaySessions.length === 0 ? (
          <div
            style={{
              background: '#F8FAFC',
              borderRadius: '0.75rem',
              padding: '1rem',
              textAlign: 'center',
              color: '#64748B',
              fontSize: '0.875rem',
              fontFamily: 'Inter, sans-serif',
            }}
          >
            No more sessions today
          </div>
        ) : (
          todaySessions.map(s => {
            const trackColor = TRACK_COLORS[s.track || ''] || '#0D9488'
            return (
              <div
                key={s.id}
                style={{
                  background: '#fff',
                  border: '1px solid #E2E8F0',
                  borderLeft: `4px solid ${trackColor}`,
                  borderRadius: '0.75rem',
                  padding: '0.875rem',
                  marginBottom: '0.625rem',
                }}
              >
                <div
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    marginBottom: '0.25rem',
                  }}
                >
                  <span
                    style={{
                      fontSize: '0.75rem',
                      fontWeight: 700,
                      color: trackColor,
                      fontFamily: 'Outfit, sans-serif',
                    }}
                  >
                    {formatTime(s.start_time)}
                  </span>
                  {s.room && (
                    <span
                      style={{
                        fontSize: '0.7rem',
                        color: '#94A3B8',
                        fontFamily: 'Inter, sans-serif',
                      }}
                    >
                      {s.room}
                    </span>
                  )}
                </div>
                <div
                  style={{
                    fontWeight: 600,
                    fontSize: '0.9rem',
                    color: '#0F172A',
                    lineHeight: 1.3,
                    fontFamily: 'Inter, sans-serif',
                  }}
                >
                  {s.title}
                </div>
                {s.speaker_name && (
                  <div
                    style={{
                      fontSize: '0.75rem',
                      color: '#64748B',
                      marginTop: '0.25rem',
                      fontFamily: 'Inter, sans-serif',
                    }}
                  >
                    {s.speaker_name}
                    {s.speaker_org ? ` \u00b7 ${s.speaker_org}` : ''}
                  </div>
                )}
              </div>
            )
          })
        )}
      </div>

      {/* Quick actions */}
      <div>
        <h2
          style={{
            fontFamily: 'Outfit, sans-serif',
            fontSize: '1rem',
            fontWeight: 700,
            color: '#0F172A',
            margin: '0 0 0.75rem',
          }}
        >
          Quick Actions
        </h2>
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: '0.75rem',
          }}
        >
          {[
            {
              icon: CalendarDays,
              label: 'Agenda',
              desc: 'Full programme',
              path: '/agenda',
              color: '#0D9488',
            },
            {
              icon: MapPin,
              label: 'Venue Map',
              desc: 'ICC Durban floors',
              path: '/map',
              color: '#3B82F6',
            },
            {
              icon: QrCode,
              label: 'Network',
              desc: 'Exchange contacts',
              path: '/networking',
              color: '#8B5CF6',
            },
            {
              icon: Bell,
              label: 'Alerts',
              desc:
                unreadCount > 0
                  ? `${unreadCount} unread`
                  : 'Announcements',
              path: '/notifications',
              color: '#F59E0B',
            },
          ].map(({ icon: Icon, label, desc, path, color }) => (
            <button
              key={path}
              onClick={() => navigate(path)}
              style={{
                background: '#fff',
                border: '1px solid #E2E8F0',
                borderRadius: '1rem',
                padding: '1rem',
                textAlign: 'left',
                cursor: 'pointer',
                transition: 'box-shadow 0.15s',
              }}
            >
              <div
                style={{
                  width: 36,
                  height: 36,
                  borderRadius: '0.625rem',
                  background: `${color}18`,
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  marginBottom: '0.625rem',
                }}
              >
                <Icon size={20} style={{ color }} />
              </div>
              <div
                style={{
                  fontWeight: 700,
                  fontSize: '0.9rem',
                  color: '#0F172A',
                  fontFamily: 'Outfit, sans-serif',
                }}
              >
                {label}
              </div>
              <div
                style={{
                  fontSize: '0.75rem',
                  color: '#64748B',
                  marginTop: '2px',
                  fontFamily: 'Inter, sans-serif',
                }}
              >
                {desc}
              </div>
            </button>
          ))}
        </div>
      </div>
    </div>
  )
}
