import { useState } from 'react'
import { X, MapPin, Clock } from 'lucide-react'
import IccSvgMap from '@/components/venue/IccSvgMap'
import { useAgenda, formatTime } from '@/hooks/useAgenda'
import type { AgendaSession } from '@/types'

export type Floor = 'ground' | 'level1'

const TRACK_COLORS: Record<string, string> = {
  plenary: '#0D9488', water: '#3B82F6', sanitation: '#16A34A',
  innovation: '#F59E0B', policy: '#8B5CF6', networking: '#EC4899',
}

export default function VenueMapPage() {
  const [floor, setFloor]             = useState<Floor>('ground')
  const [selectedRoom, setSelectedRoom] = useState<string | null>(null)
  const { sessions }                  = useAgenda()

  // Get sessions for the selected room (for today or all days if not conference week)
  const todayDate = new Date().toISOString().split('T')[0]
  const isConfWeek = ['2026-11-09','2026-11-10','2026-11-11','2026-11-12'].includes(todayDate)
  const roomSessions: AgendaSession[] = selectedRoom
    ? sessions.filter(s =>
        s.room === selectedRoom &&
        s.is_published &&
        s.session_type !== 'break' &&
        (isConfWeek ? s.day === todayDate : true)
      ).sort((a, b) => a.day.localeCompare(b.day) || a.start_time.localeCompare(b.start_time))
      .slice(0, 5)
    : []

  return (
    <div style={{ height: 'calc(100dvh - 112px)', display: 'flex', flexDirection: 'column', position: 'relative', overflow: 'hidden' }}>
      {/* Floor selector */}
      <div style={{ display: 'flex', gap: '0.5rem', padding: '0.75rem 1rem', background: '#fff', borderBottom: '1px solid #E2E8F0', flexShrink: 0 }}>
        {([['ground', 'Ground Floor'], ['level1', 'Level 1']] as [Floor, string][]).map(([f, label]) => (
          <button key={f} onClick={() => setFloor(f)}
            style={{ padding: '0.4rem 1rem', borderRadius: '999px', border: `1.5px solid ${floor === f ? '#0D9488' : '#E2E8F0'}`, background: floor === f ? '#F0FDFA' : '#fff', color: floor === f ? '#0D9488' : '#64748B', fontWeight: 600, fontSize: '0.8rem', cursor: 'pointer' }}>
            {label}
          </button>
        ))}
        <div style={{ marginLeft: 'auto', display: 'flex', alignItems: 'center', gap: '0.25rem', color: '#94A3B8', fontSize: '0.75rem' }}>
          <MapPin size={12} /> ICC Durban
        </div>
      </div>

      {/* SVG Map */}
      <div style={{ flex: 1, overflow: 'hidden', padding: '0.5rem' }}>
        <IccSvgMap floor={floor} selectedRoom={selectedRoom} onRoomClick={setSelectedRoom} />
      </div>

      {/* Legend */}
      <div style={{ display: 'flex', gap: '0.75rem', padding: '0.5rem 1rem', background: '#F8FAFC', flexShrink: 0, overflowX: 'auto', borderTop: '1px solid #E2E8F0' }}>
        {[['#0D9488','Plenary'],['#3B82F6','Sessions'],['#F59E0B','Catering'],['#94A3B8','Facilities']].map(([color, label]) => (
          <div key={label} style={{ display: 'flex', alignItems: 'center', gap: '0.3rem', whiteSpace: 'nowrap' }}>
            <div style={{ width: 10, height: 10, borderRadius: 2, background: color, flexShrink: 0 }} />
            <span style={{ fontSize: '0.7rem', color: '#64748B' }}>{label}</span>
          </div>
        ))}
      </div>

      {/* Room panel slide-up */}
      {selectedRoom && (
        <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0, background: '#fff', borderRadius: '1.25rem 1.25rem 0 0', boxShadow: '0 -8px 32px rgba(0,0,0,0.15)', padding: '1.25rem', maxHeight: '60dvh', overflowY: 'auto', zIndex: 10 }}>
          <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '1rem' }}>
            <div>
              <h3 style={{ fontFamily: 'Outfit,sans-serif', fontSize: '1.1rem', fontWeight: 700, color: '#0F172A', margin: 0 }}>{selectedRoom}</h3>
              <p style={{ color: '#64748B', fontSize: '0.8rem', margin: '0.125rem 0 0' }}>{isConfWeek ? "Today's sessions" : 'Upcoming sessions'}</p>
            </div>
            <button onClick={() => setSelectedRoom(null)} style={{ background: '#F1F5F9', border: 'none', borderRadius: '50%', width: 32, height: 32, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }}>
              <X size={16} style={{ color: '#64748B' }} />
            </button>
          </div>
          {roomSessions.length === 0 ? (
            <div style={{ textAlign: 'center', padding: '1rem', color: '#94A3B8', fontSize: '0.875rem' }}>No sessions scheduled for this room</div>
          ) : (
            roomSessions.map(s => (
              <div key={s.id} style={{ borderLeft: `3px solid ${TRACK_COLORS[s.track || ''] || '#94A3B8'}`, paddingLeft: '0.75rem', marginBottom: '0.875rem' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '0.375rem', marginBottom: '0.2rem' }}>
                  <Clock size={11} style={{ color: '#94A3B8' }} />
                  <span style={{ fontSize: '0.72rem', color: '#94A3B8' }}>{s.day} · {formatTime(s.start_time)}–{formatTime(s.end_time)}</span>
                </div>
                <div style={{ fontWeight: 600, fontSize: '0.875rem', color: '#0F172A', lineHeight: 1.3 }}>{s.title}</div>
                {s.speaker_name && <div style={{ fontSize: '0.75rem', color: '#64748B', marginTop: '0.125rem' }}>{s.speaker_name}</div>}
              </div>
            ))
          )}
        </div>
      )}
    </div>
  )
}
