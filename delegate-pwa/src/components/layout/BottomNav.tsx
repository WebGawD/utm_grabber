import { useNavigate, useLocation } from 'react-router-dom'
import { Home, CalendarDays, MapPin, QrCode, Bell } from 'lucide-react'
import { useFlashAlerts } from '@/hooks/useFlashAlerts'

const TABS = [
  { path: '/home',          icon: Home,         label: 'Home'    },
  { path: '/agenda',        icon: CalendarDays, label: 'Agenda'  },
  { path: '/map',           icon: MapPin,        label: 'Map'     },
  { path: '/networking',    icon: QrCode,        label: 'Network' },
  { path: '/notifications', icon: Bell,          label: 'Alerts'  },
]

export default function BottomNav() {
  const navigate       = useNavigate()
  const { pathname }   = useLocation()
  const { unreadCount } = useFlashAlerts()

  return (
    <nav style={{
      position: 'fixed', bottom: 0, left: 0, right: 0, zIndex: 50,
      background: '#fff',
      borderTop: '1px solid #E2E8F0',
      display: 'flex',
      paddingBottom: 'env(safe-area-inset-bottom)',
      height: 60,
    }}>
      {TABS.map(({ path, icon: Icon, label }) => {
        const active  = pathname === path
        const isBell  = path === '/notifications'
        return (
          <button
            key={path}
            onClick={() => navigate(path)}
            style={{
              flex: 1,
              display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
              gap: '0.2rem',
              background: 'none', border: 'none', cursor: 'pointer',
              color: active ? '#0D9488' : '#94A3B8',
              position: 'relative',
              padding: '0.5rem 0.25rem 0.375rem',
              transition: 'color 0.1s',
            }}
          >
            <div style={{ position: 'relative' }}>
              <Icon size={21} strokeWidth={active ? 2.5 : 1.8} />
              {isBell && unreadCount > 0 && (
                <span style={{
                  position: 'absolute', top: -4, right: -5,
                  background: '#EF4444', color: '#fff',
                  fontSize: '0.55rem', fontWeight: 800,
                  minWidth: 15, height: 15,
                  borderRadius: '999px',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  border: '1.5px solid #fff',
                  padding: '0 2px',
                }}>
                  {unreadCount > 9 ? '9+' : unreadCount}
                </span>
              )}
            </div>
            <span style={{ fontSize: '0.6rem', fontWeight: active ? 700 : 500, lineHeight: 1 }}>
              {label}
            </span>
            {active && (
              <div style={{
                position: 'absolute', bottom: 0, left: '50%', transform: 'translateX(-50%)',
                width: 22, height: 3, borderRadius: '2px 2px 0 0', background: '#0D9488',
              }} />
            )}
          </button>
        )
      })}
    </nav>
  )
}
