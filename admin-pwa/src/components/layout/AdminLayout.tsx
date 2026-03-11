import { Outlet, NavLink, useNavigate } from 'react-router-dom'
import { useState, useEffect } from 'react'
import {
  LayoutDashboard, QrCode, Users, Bell, Building2,
  Gift, LogOut, Menu, Wifi, WifiOff, AlertCircle, BarChart2, Printer,
} from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import { getQueueSize } from '@/hooks/useOfflineQueue'
import toast from 'react-hot-toast'

const navItems = [
  { to: '/dashboard', icon: LayoutDashboard, label: 'Dashboard' },
  { to: '/checkin',   icon: QrCode,          label: 'QR Check-In' },
  { to: '/delegates', icon: Users,            label: 'Delegates' },
  { to: '/alerts',    icon: Bell,             label: 'Flash Alerts' },
  { to: '/sponsors',  icon: Building2,        label: 'Sponsors' },
  { to: '/swag-bag',  icon: Gift,             label: 'Swag Bag' },
  { to: '/reports',   icon: BarChart2,        label: 'Reports' },
  { to: '/badges',    icon: Printer,          label: 'Badge Print' },
]

export default function AdminLayout() {
  const { staffUser, signOut }    = useAuth()
  const navigate                  = useNavigate()
  const [sidebarOpen, setSidebar] = useState(false)
  const [isOnline, setIsOnline]   = useState(navigator.onLine)
  const [queueSize, setQueueSize] = useState(0)

  useEffect(() => {
    function onOnline()  { setIsOnline(true);  toast.success('Back online — syncing check-ins…') }
    function onOffline() { setIsOnline(false); toast.error('You are offline. Check-ins will be queued.') }

    window.addEventListener('online',  onOnline)
    window.addEventListener('offline', onOffline)

    // Poll queue size every 5 s.
    const interval = setInterval(async () => {
      const size = await getQueueSize()
      setQueueSize(size)
    }, 5000)

    return () => {
      window.removeEventListener('online',  onOnline)
      window.removeEventListener('offline', onOffline)
      clearInterval(interval)
    }
  }, [])

  async function handleSignOut() {
    await signOut()
    navigate('/login')
  }

  const Sidebar = (
    <aside className="flex flex-col h-full bg-slate-900 border-r border-slate-800 w-64">
      {/* Logo */}
      <div className="p-5 border-b border-slate-800">
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5" aria-hidden="true">
              <path d="M12 2C8 2 4 8 4 14s4 8 8 8 8-2 8-8S16 2 12 2z"/>
              <path d="M12 8v8M8 12h8"/>
            </svg>
          </div>
          <div>
            <div className="text-white font-heading font-bold text-sm leading-tight">AWSISA Admin</div>
            <div className="text-primary-light text-xs font-semibold">Watersan 2026</div>
          </div>
        </div>
      </div>

      {/* Nav */}
      <nav className="flex-1 p-3 space-y-0.5" aria-label="Admin navigation">
        {navItems.map(({ to, icon: Icon, label }) => (
          <NavLink
            key={to}
            to={to}
            className={({ isActive }) =>
              `sidebar-link ${isActive ? 'active' : ''}`
            }
            onClick={() => setSidebar(false)}
          >
            <Icon size={18} />
            <span>{label}</span>
          </NavLink>
        ))}
      </nav>

      {/* Connection status */}
      <div className="p-3 border-t border-slate-800">
        <div className={`flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold ${
          isOnline ? 'text-green-400 bg-green-500/10' : 'text-amber-400 bg-amber-500/10'
        }`}>
          {isOnline
            ? <><Wifi size={14} /> Online</>
            : <><WifiOff size={14} /> Offline</>
          }
          {queueSize > 0 && (
            <span className="ml-auto bg-amber-500 text-amber-950 rounded-full px-1.5 py-0.5 text-xs font-bold">
              {queueSize}
            </span>
          )}
        </div>
      </div>

      {/* User */}
      <div className="p-3 border-t border-slate-800">
        <div className="flex items-center gap-3 px-3 py-2">
          <div className="w-8 h-8 bg-primary/30 rounded-full flex items-center justify-center flex-shrink-0">
            <span className="text-primary-light text-xs font-bold">
              {staffUser?.full_name?.charAt(0).toUpperCase() ?? '?'}
            </span>
          </div>
          <div className="flex-1 min-w-0">
            <div className="text-white text-sm font-semibold truncate">{staffUser?.full_name ?? 'Staff'}</div>
            <div className="text-slate-500 text-xs capitalize">{staffUser?.role ?? 'staff'}</div>
          </div>
          <button
            onClick={handleSignOut}
            className="text-slate-500 hover:text-red-400 transition-colors"
            title="Sign out"
            aria-label="Sign out"
          >
            <LogOut size={16} />
          </button>
        </div>
      </div>
    </aside>
  )

  return (
    <div className="flex h-screen overflow-hidden bg-slate-950">
      {/* Desktop sidebar */}
      <div className="hidden lg:flex flex-col">
        {Sidebar}
      </div>

      {/* Mobile sidebar overlay */}
      {sidebarOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div
            className="absolute inset-0 bg-black/60"
            onClick={() => setSidebar(false)}
            aria-hidden="true"
          />
          <div className="absolute left-0 top-0 bottom-0 flex flex-col">
            {Sidebar}
          </div>
        </div>
      )}

      {/* Main content */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        {/* Top bar */}
        <header className="h-14 flex items-center justify-between px-4 bg-slate-900 border-b border-slate-800 flex-shrink-0">
          <button
            className="lg:hidden text-slate-400 hover:text-white"
            onClick={() => setSidebar(true)}
            aria-label="Open navigation"
          >
            <Menu size={22} />
          </button>

          {/* Offline queue warning */}
          {!isOnline && queueSize > 0 && (
            <div className="flex items-center gap-2 px-3 py-1.5 bg-amber-500/10 border border-amber-500/30 rounded-lg text-amber-400 text-xs font-semibold">
              <AlertCircle size={14} />
              {queueSize} check-in{queueSize !== 1 ? 's' : ''} queued offline
            </div>
          )}

          <div className="flex items-center gap-3 ml-auto">
            <span className={`flex items-center gap-1.5 text-xs font-semibold ${isOnline ? 'text-green-400' : 'text-amber-400'}`}>
              <span className={`w-2 h-2 rounded-full ${isOnline ? 'bg-green-400 animate-pulse-dot' : 'bg-amber-400'}`} />
              {isOnline ? 'Online' : 'Offline'}
            </span>
          </div>
        </header>

        {/* Offline banner */}
        {!isOnline && (
          <div className="offline-banner">
            <WifiOff size={16} />
            You are offline — check-ins are being saved locally and will sync automatically.
          </div>
        )}

        {/* Page content */}
        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
