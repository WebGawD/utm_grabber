import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { Toaster } from 'react-hot-toast'
import { useAuth } from '@/hooks/useAuth'

// Layouts
import AdminLayout from '@/components/layout/AdminLayout'

// Pages
import LoginPage      from '@/pages/Login'
import DashboardPage  from '@/pages/Dashboard'
import CheckInPage    from '@/pages/CheckIn'
import DelegatesPage  from '@/pages/Delegates'
import AlertsPage     from '@/pages/Alerts'
import SponsorsPage   from '@/pages/Sponsors'
import SwagBagPage    from '@/pages/SwagBag'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { user, loading } = useAuth()

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-slate-950">
        <div className="flex flex-col items-center gap-4">
          <div className="w-10 h-10 border-3 border-primary border-t-transparent rounded-full animate-spin" />
          <p className="text-slate-400 text-sm">Loading…</p>
        </div>
      </div>
    )
  }

  if (!user) return <Navigate to="/login" replace />
  return <>{children}</>
}

export default function App() {
  return (
    <BrowserRouter>
      <Toaster
        position="top-right"
        toastOptions={{
          style: {
            background: '#1E293B',
            color: '#F1F5F9',
            border: '1px solid #334155',
            borderRadius: '0.75rem',
            fontSize: '0.9rem',
          },
          success: { iconTheme: { primary: '#10B981', secondary: '#0F172A' } },
          error:   { iconTheme: { primary: '#EF4444', secondary: '#0F172A' } },
        }}
      />

      <Routes>
        {/* Auth */}
        <Route path="/login" element={<LoginPage />} />

        {/* Admin — protected */}
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <AdminLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<Navigate to="/dashboard" replace />} />
          <Route path="dashboard"  element={<DashboardPage />} />
          <Route path="checkin"    element={<CheckInPage />} />
          <Route path="delegates"  element={<DelegatesPage />} />
          <Route path="alerts"     element={<AlertsPage />} />
          <Route path="sponsors"   element={<SponsorsPage />} />
          <Route path="swag-bag"   element={<SwagBagPage />} />
        </Route>

        {/* 404 */}
        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  )
}
