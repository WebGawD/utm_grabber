import { Outlet, useNavigate, useLocation } from 'react-router-dom'
import { ChevronLeft, Droplets } from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import BottomNav from './BottomNav'

const PAGE_TITLES: Record<string, string> = {
  '/home':          'Home',
  '/agenda':        'Programme',
  '/map':           'Venue Map',
  '/networking':    'Networking',
  '/notifications': 'Announcements',
  '/profile':       'My Profile',
}

const NAV_ROUTES = ['/home', '/agenda', '/map', '/networking', '/notifications']

export default function AppShell() {
  const { session } = useAuth()
  const navigate    = useNavigate()
  const { pathname } = useLocation()

  const title    = PAGE_TITLES[pathname] ?? 'Watersan 2026'
  const showBack = !NAV_ROUTES.includes(pathname)
  const isHome   = pathname === '/home'

  return (
    <div style={{ minHeight: '100dvh', background: '#F8FAFC', paddingBottom: 60 }}>
      {/* Top header */}
      <header style={{
        position: 'sticky', top: 0, zIndex: 40,
        background: '#fff', borderBottom: '1px solid #E2E8F0',
        paddingTop: 'env(safe-area-inset-top)',
      }}>
        <div style={{
          display: 'flex', alignItems: 'center',
          height: 52, padding: '0 0.875rem', gap: '0.5rem',
        }}>
          {showBack && (
            <button
              onClick={() => navigate(-1)}
              style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#0D9488', padding: '0.25rem', display: 'flex', flexShrink: 0 }}
            >
              <ChevronLeft size={24} />
            </button>
          )}

          {isHome ? (
            <div style={{ display: 'flex', alignItems: 'center', gap: '0.375rem' }}>
              <Droplets size={20} style={{ color: '#0D9488' }} />
              <span style={{ fontFamily: 'Outfit,sans-serif', fontWeight: 800, fontSize: '1rem', color: '#0F172A' }}>
                Watersan 2026
              </span>
            </div>
          ) : (
            <h1 style={{ fontFamily: 'Outfit,sans-serif', fontWeight: 700, fontSize: '1.05rem', color: '#0F172A', margin: 0 }}>
              {title}
            </h1>
          )}

          <div style={{ marginLeft: 'auto' }}>
            <button
              onClick={() => navigate('/profile')}
              title="My Profile"
              style={{
                width: 33, height: 33, borderRadius: '50%',
                background: 'linear-gradient(135deg,#0D9488,#115E59)',
                border: 'none', cursor: 'pointer',
                color: '#fff', fontWeight: 800, fontSize: '0.875rem',
                fontFamily: 'Outfit,sans-serif',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                flexShrink: 0,
              }}
            >
              {session?.firstName?.charAt(0).toUpperCase() ?? '?'}
            </button>
          </div>
        </div>
      </header>

      {/* Page content */}
      <main>
        <Outlet />
      </main>

      <BottomNav />
    </div>
  )
}
