import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { Toaster } from 'react-hot-toast'
import { useAuth } from '@/hooks/useAuth'

// Layout
import AppShell from '@/components/layout/AppShell'

// Pages
import LoginPage         from '@/pages/Login'
import HomePage          from '@/pages/Home'
import AgendaPage        from '@/pages/Agenda'
import VenueMapPage      from '@/pages/VenueMap'
import NetworkingPage    from '@/pages/Networking'
import NotificationsPage from '@/pages/Notifications'
import ProfilePage       from '@/pages/Profile'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isLoggedIn, loading } = useAuth()

  if (loading) {
    return (
      <div style={{
        minHeight: '100dvh', display: 'flex', alignItems: 'center', justifyContent: 'center',
        background: 'linear-gradient(135deg,#115E59,#0D9488)',
      }}>
        <div style={{ textAlign: 'center' }}>
          <div style={{
            width: 40, height: 40,
            border: '3px solid rgba(255,255,255,0.3)', borderTopColor: '#fff',
            borderRadius: '50%', animation: 'spin 0.8s linear infinite',
            margin: '0 auto 1rem',
          }} />
          <p style={{ color: '#CCFBF1', fontSize: '0.875rem', margin: 0 }}>Loading…</p>
        </div>
        <style>{`@keyframes spin { to { transform: rotate(360deg) } }`}</style>
      </div>
    )
  }

  if (!isLoggedIn) return <Navigate to="/login" replace />
  return <>{children}</>
}

export default function App() {
  return (
    <Router basename="/awsisa/app">
      <Toaster
        position="top-center"
        toastOptions={{
          style: {
            background: '#0F172A',
            color: '#F1F5F9',
            border: '1px solid #0D9488',
            borderRadius: '0.875rem',
            fontSize: '0.875rem',
            maxWidth: '90vw',
          },
          success: { iconTheme: { primary: '#0D9488', secondary: '#0F172A' } },
          error:   { iconTheme: { primary: '#EF4444', secondary: '#0F172A' } },
        }}
      />

      <Routes>
        {/* Public */}
        <Route path="/login" element={<LoginPage />} />

        {/* Protected — AppShell wraps all inner routes */}
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <AppShell />
            </ProtectedRoute>
          }
        >
          <Route index                element={<Navigate to="/home" replace />} />
          <Route path="home"          element={<HomePage />} />
          <Route path="agenda"        element={<AgendaPage />} />
          <Route path="map"           element={<VenueMapPage />} />
          <Route path="networking"    element={<NetworkingPage />} />
          <Route path="notifications" element={<NotificationsPage />} />
          <Route path="profile"       element={<ProfilePage />} />
        </Route>

        {/* 404 */}
        <Route path="*" element={<Navigate to="/home" replace />} />
      </Routes>
    </Router>
  )
}
