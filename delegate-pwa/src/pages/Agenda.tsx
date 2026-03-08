import { useState } from 'react'
import { CalendarDays, BookmarkPlus, BookmarkCheck, Coffee } from 'lucide-react'
import { useAgenda, groupByDay, formatTime, CONFERENCE_DAYS, TRACKS } from '@/hooks/useAgenda'
import type { AgendaSession } from '@/types'

const BOOKMARK_KEY = 'awsisa_bookmarks'

function getBookmarks(): Set<string> {
  try { return new Set(JSON.parse(localStorage.getItem(BOOKMARK_KEY) || '[]')) }
  catch { return new Set() }
}

function toggleBookmark(id: string, current: Set<string>): Set<string> {
  const next = new Set(current)
  if (next.has(id)) next.delete(id)
  else next.add(id)
  localStorage.setItem(BOOKMARK_KEY, JSON.stringify([...next]))
  return next
}

const TRACK_COLORS: Record<string, string> = {
  plenary: '#0D9488', water: '#3B82F6', sanitation: '#16A34A',
  innovation: '#F59E0B', policy: '#8B5CF6', networking: '#EC4899',
}

function SessionCard({
  session, bookmarks, onToggle,
}: {
  session: AgendaSession
  bookmarks: Set<string>
  onToggle: (id: string) => void
}) {
  const isBreak      = session.session_type === 'break'
  const isBookmarked = bookmarks.has(session.id)
  const trackColor   = TRACK_COLORS[session.track || ''] || '#94A3B8'

  if (isBreak) {
    return (
      <div style={{ background: '#F8FAFC', borderRadius: '0.625rem', padding: '0.625rem 0.875rem', marginBottom: '0.5rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
        <Coffee size={14} style={{ color: '#94A3B8' }} />
        <span style={{ fontSize: '0.8rem', color: '#94A3B8', fontWeight: 500 }}>
          {session.title} · {formatTime(session.start_time)}
        </span>
      </div>
    )
  }

  return (
    <div style={{ background: '#fff', border: '1px solid #E2E8F0', borderLeft: `4px solid ${trackColor}`, borderRadius: '0.75rem', padding: '0.875rem', marginBottom: '0.625rem' }}>
      <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: '0.5rem' }}>
        <div style={{ flex: 1, minWidth: 0 }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '0.25rem', flexWrap: 'wrap' }}>
            <span style={{ fontSize: '0.75rem', fontWeight: 700, color: trackColor }}>
              {formatTime(session.start_time)}–{formatTime(session.end_time)}
            </span>
            {session.room && (
              <span style={{ fontSize: '0.7rem', color: '#fff', background: '#94A3B8', padding: '0.1rem 0.45rem', borderRadius: '999px' }}>
                {session.room}
              </span>
            )}
            {session.track && (
              <span style={{ fontSize: '0.7rem', color: trackColor, background: `${trackColor}18`, padding: '0.1rem 0.45rem', borderRadius: '999px', textTransform: 'capitalize' }}>
                {session.track}
              </span>
            )}
          </div>
          <div style={{ fontWeight: 700, fontSize: '0.9rem', color: '#0F172A', lineHeight: 1.35, marginBottom: '0.25rem' }}>
            {session.title}
          </div>
          {session.speaker_name && (
            <div style={{ fontSize: '0.75rem', color: '#64748B' }}>
              {session.speaker_name}{session.speaker_org ? ` · ${session.speaker_org}` : ''}
            </div>
          )}
          {session.description && (
            <div style={{
              fontSize: '0.75rem', color: '#94A3B8', marginTop: '0.375rem', lineHeight: 1.5,
              display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden',
            }}>
              {session.description}
            </div>
          )}
        </div>
        <button
          onClick={() => onToggle(session.id)}
          style={{ flexShrink: 0, background: 'none', border: 'none', cursor: 'pointer', padding: '0.25rem', color: isBookmarked ? '#F59E0B' : '#CBD5E1' }}
        >
          {isBookmarked ? <BookmarkCheck size={18} /> : <BookmarkPlus size={18} />}
        </button>
      </div>
    </div>
  )
}

export default function AgendaPage() {
  const { sessions, loading, error } = useAgenda()
  const [activeDay,   setActiveDay]  = useState(CONFERENCE_DAYS[0].date)
  const [activeTrack, setTrack]      = useState('')
  const [bookmarks,   setBookmarks]  = useState<Set<string>>(getBookmarks)

  const grouped     = groupByDay(sessions)
  const daySessions = (grouped[activeDay] || [])
    .filter(s => !activeTrack || s.track === activeTrack || s.session_type === 'break')
    .sort((a, b) => a.start_time.localeCompare(b.start_time))

  function onToggle(id: string) {
    setBookmarks(prev => toggleBookmark(id, prev))
  }

  return (
    <div>
      {/* Day tabs */}
      <div style={{ display: 'flex', overflowX: 'auto', borderBottom: '1px solid #E2E8F0', background: '#fff', padding: '0 1rem' }}>
        {CONFERENCE_DAYS.map(d => (
          <button
            key={d.date}
            onClick={() => setActiveDay(d.date)}
            style={{
              padding: '0.75rem 0.875rem',
              fontWeight: activeDay === d.date ? 700 : 500,
              fontSize: '0.8rem',
              color: activeDay === d.date ? '#0D9488' : '#64748B',
              background: 'none',
              border: 'none',
              borderBottom: activeDay === d.date ? '2px solid #0D9488' : '2px solid transparent',
              cursor: 'pointer',
              whiteSpace: 'nowrap',
              marginBottom: '-1px',
              flexShrink: 0,
              lineHeight: 1.4,
            }}
          >
            {d.label}<br />
            <span style={{ fontWeight: 400, fontSize: '0.7rem' }}>{d.short}</span>
          </button>
        ))}
      </div>

      {/* Track filter chips */}
      <div style={{ display: 'flex', gap: '0.5rem', overflowX: 'auto', padding: '0.75rem 1rem', background: '#F8FAFC', borderBottom: '1px solid #E2E8F0' }}>
        {TRACKS.map(t => (
          <button
            key={t.value}
            onClick={() => setTrack(t.value)}
            style={{
              padding: '0.3rem 0.75rem',
              borderRadius: '999px',
              border: `1.5px solid ${activeTrack === t.value ? (t.color || '#0D9488') : '#E2E8F0'}`,
              background: activeTrack === t.value ? `${(t.color || '#0D9488')}18` : '#fff',
              color: activeTrack === t.value ? (t.color || '#0D9488') : '#64748B',
              fontWeight: 600,
              fontSize: '0.75rem',
              cursor: 'pointer',
              whiteSpace: 'nowrap',
              flexShrink: 0,
              transition: 'all 0.1s',
            }}
          >
            {t.label}
          </button>
        ))}
      </div>

      {/* Session list */}
      <div style={{ padding: '0.75rem 1rem' }}>
        {loading ? (
          [1, 2, 3, 4].map(i => (
            <div key={i} style={{ background: '#E2E8F0', borderRadius: '0.75rem', height: '90px', marginBottom: '0.625rem', opacity: 0.6 }} />
          ))
        ) : error ? (
          <div style={{ textAlign: 'center', padding: '3rem 1rem', color: '#EF4444' }}>
            ❌ {error}
          </div>
        ) : daySessions.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '3rem 1rem', color: '#94A3B8' }}>
            <CalendarDays size={40} style={{ margin: '0 auto 1rem', opacity: 0.3, display: 'block' }} />
            <p style={{ margin: 0, fontWeight: 600, color: '#64748B' }}>No sessions for this selection</p>
            <p style={{ margin: '0.375rem 0 0', fontSize: '0.875rem' }}>Try a different day or track filter</p>
          </div>
        ) : (
          daySessions.map(s => (
            <SessionCard key={s.id} session={s} bookmarks={bookmarks} onToggle={onToggle} />
          ))
        )}
      </div>
    </div>
  )
}
