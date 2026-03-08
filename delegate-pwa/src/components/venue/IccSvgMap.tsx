import type { Floor } from '@/pages/VenueMap'

interface Room {
  id: string
  label: string
  x: number; y: number; w: number; h: number
  fill: string
  floor: Floor
}

const ROOMS: Room[] = [
  // Ground floor
  { id: 'Main Arena',         label: 'Main Arena\n(Hall 1)',         x: 60,  y: 120, w: 260, h: 200, fill: '#0D9488', floor: 'ground' },
  { id: 'Registration',       label: 'Registration',                 x: 60,  y: 60,  w: 120, h: 55,  fill: '#3B82F6', floor: 'ground' },
  { id: 'Exhibition Hall',    label: 'Exhibition\nHall',             x: 340, y: 60,  w: 200, h: 140, fill: '#F59E0B', floor: 'ground' },
  { id: 'Catering',           label: 'Catering &\nDining',           x: 340, y: 210, w: 200, h: 110, fill: '#F59E0B', floor: 'ground' },
  { id: 'Entrance Foyer',     label: 'Entrance\nFoyer',              x: 185, y: 60,  w: 145, h: 55,  fill: '#94A3B8', floor: 'ground' },

  // Level 1
  { id: 'Meeting Room 1',     label: 'Meeting\nRoom 1',              x: 60,  y: 60,  w: 130, h: 90,  fill: '#16A34A', floor: 'level1' },
  { id: 'Meeting Room 2',     label: 'Meeting\nRoom 2',              x: 60,  y: 160, w: 130, h: 90,  fill: '#16A34A', floor: 'level1' },
  { id: 'Meeting Room 3',     label: 'Meeting\nRoom 3',              x: 60,  y: 260, w: 130, h: 90,  fill: '#16A34A', floor: 'level1' },
  { id: 'Meeting Room 4',     label: 'Meeting\nRoom 4',              x: 205, y: 60,  w: 130, h: 90,  fill: '#8B5CF6', floor: 'level1' },
  { id: 'Meeting Room 5',     label: 'Meeting\nRoom 5',              x: 205, y: 160, w: 130, h: 90,  fill: '#8B5CF6', floor: 'level1' },
  { id: 'Arbor Room',         label: 'Arbor Room\n(Workshop)',       x: 205, y: 260, w: 130, h: 90,  fill: '#EC4899', floor: 'level1' },
  { id: 'Media Centre',       label: 'Media\nCentre',                x: 350, y: 60,  w: 120, h: 90,  fill: '#64748B', floor: 'level1' },
  { id: 'Prayer Room',        label: 'Prayer /\nQuiet Room',         x: 350, y: 160, w: 120, h: 90,  fill: '#94A3B8', floor: 'level1' },
]

function splitLabel(label: string) {
  return label.split('\n')
}

interface Props {
  floor: Floor
  selectedRoom: string | null
  onRoomClick: (room: string) => void
}

export default function IccSvgMap({ floor, selectedRoom, onRoomClick }: Props) {
  const visible = ROOMS.filter(r => r.floor === floor)

  return (
    <div style={{ width: '100%', height: '100%', display: 'flex', flexDirection: 'column' }}>
      <svg
        viewBox="0 0 600 400"
        style={{ width: '100%', flex: 1, border: '1px solid #E2E8F0', borderRadius: '1rem', background: '#F8FAFC' }}
        aria-label={`ICC Durban ${floor === 'ground' ? 'Ground Floor' : 'Level 1'} Map`}
      >
        {/* Building outline */}
        <rect x="45" y="45" width="510" height="310" rx="12" fill="#EFF6FF" stroke="#BFDBFE" strokeWidth="2" />

        {/* Floor label */}
        <text x="300" y="30" textAnchor="middle" fontSize="11" fontWeight="700" fill="#94A3B8" fontFamily="Inter,sans-serif">
          {floor === 'ground' ? 'GROUND FLOOR' : 'LEVEL 1'}
        </text>

        {visible.map(room => {
          const isSelected = selectedRoom === room.id
          const lines      = splitLabel(room.label)
          const midX       = room.x + room.w / 2
          const midY       = room.y + room.h / 2
          const lineH      = 13

          return (
            <g
              key={room.id}
              onClick={() => onRoomClick(room.id)}
              style={{ cursor: 'pointer' }}
              role="button"
              aria-label={room.id}
            >
              <rect
                x={room.x} y={room.y} width={room.w} height={room.h} rx={8}
                fill={isSelected ? room.fill : `${room.fill}22`}
                stroke={room.fill}
                strokeWidth={isSelected ? 2.5 : 1.5}
                opacity={isSelected ? 1 : 0.9}
              />
              {lines.map((line, i) => (
                <text
                  key={i}
                  x={midX}
                  y={midY - ((lines.length - 1) * lineH) / 2 + i * lineH + 4}
                  textAnchor="middle"
                  fontSize={Math.min(11, room.w / line.length * 1.4)}
                  fontWeight={isSelected ? 700 : 600}
                  fill={isSelected ? '#fff' : room.fill}
                  fontFamily="Inter,sans-serif"
                  style={{ pointerEvents: 'none' }}
                >
                  {line}
                </text>
              ))}
              {isSelected && (
                <rect
                  x={room.x} y={room.y} width={room.w} height={room.h} rx={8}
                  fill="none" stroke="#fff" strokeWidth={1} opacity={0.4}
                />
              )}
            </g>
          )
        })}

        {/* Compass / north indicator */}
        <text x="555" y="370" textAnchor="middle" fontSize="10" fill="#CBD5E1" fontFamily="Inter,sans-serif">N↑</text>
      </svg>

      <p style={{ textAlign: 'center', fontSize: '0.7rem', color: '#94A3B8', margin: '0.375rem 0 0' }}>
        Tap a room to see today's sessions
      </p>
    </div>
  )
}
