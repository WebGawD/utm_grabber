import { useNavigate } from 'react-router-dom'
import { LogOut, QrCode, Mail, Building2, BadgeCheck } from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'

const DELEGATE_TYPE_LABELS: Record<string, string> = {
  government:     'Government',
  utility:        'Utility',
  private_sector: 'Private Sector',
  ngo:            'NGO / NPO',
  academic:       'Academic',
  media:          'Media',
  exhibitor:      'Exhibitor',
  sponsor:        'Sponsor',
}

export default function ProfilePage() {
  const { session, logout } = useAuth()
  const navigate            = useNavigate()

  if (!session) return null

  const typeLabel = DELEGATE_TYPE_LABELS[session.type] ?? session.type

  function handleLogout() {
    logout()
    navigate('/login', { replace: true })
  }

  const infoRows = [
    { icon: Mail,      label: 'Email',         value: session.email },
    { icon: Building2, label: 'Organisation',   value: session.organisation || '—' },
    { icon: BadgeCheck, label: 'Delegate Type', value: typeLabel },
  ]

  return (
    <div style={{ padding: '1rem', maxWidth: 480, margin: '0 auto' }}>
      {/* Avatar & name */}
      <div style={{ textAlign: 'center', padding: '1.5rem 0 1.25rem' }}>
        <div style={{
          width: 72, height: 72, borderRadius: '50%',
          background: 'linear-gradient(135deg,#0D9488,#115E59)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          color: '#fff', fontWeight: 800, fontSize: '2rem',
          fontFamily: 'Outfit,sans-serif', margin: '0 auto 0.875rem',
        }}>
          {session.firstName.charAt(0).toUpperCase()}
        </div>
        <h1 style={{ fontFamily: 'Outfit,sans-serif', fontSize: '1.375rem', fontWeight: 800, color: '#0F172A', margin: '0 0 0.375rem' }}>
          {session.name}
        </h1>
        <span style={{
          display: 'inline-block',
          background: '#F0FDFA', color: '#0D9488',
          fontSize: '0.75rem', fontWeight: 700,
          padding: '0.25rem 0.875rem', borderRadius: '999px',
          border: '1px solid #99F6E4',
        }}>
          {typeLabel}
        </span>
      </div>

      {/* Info card */}
      <div style={{ background: '#fff', border: '1px solid #E2E8F0', borderRadius: '1rem', marginBottom: '1rem', overflow: 'hidden' }}>
        {infoRows.map(({ icon: Icon, label, value }) => (
          <div
            key={label}
            style={{
              display: 'flex', alignItems: 'center', gap: '0.875rem',
              padding: '0.875rem 1rem', borderBottom: '1px solid #F1F5F9',
            }}
          >
            <div style={{
              width: 32, height: 32, borderRadius: '0.5rem', background: '#F0FDFA',
              display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0,
            }}>
              <Icon size={16} style={{ color: '#0D9488' }} />
            </div>
            <div style={{ minWidth: 0 }}>
              <div style={{ fontSize: '0.7rem', color: '#94A3B8', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                {label}
              </div>
              <div style={{ fontSize: '0.9rem', color: '#0F172A', fontWeight: 500, wordBreak: 'break-all' }}>
                {value}
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* QR Code shortcut */}
      <button
        onClick={() => navigate('/networking')}
        style={{
          width: '100%', background: '#fff', border: '1px solid #E2E8F0',
          borderRadius: '1rem', padding: '0.875rem 1rem',
          display: 'flex', alignItems: 'center', gap: '0.875rem',
          cursor: 'pointer', marginBottom: '0.75rem', textAlign: 'left',
        }}
      >
        <div style={{ width: 32, height: 32, borderRadius: '0.5rem', background: '#EDE9FE', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
          <QrCode size={16} style={{ color: '#8B5CF6' }} />
        </div>
        <div>
          <div style={{ fontWeight: 700, fontSize: '0.9rem', color: '#0F172A' }}>My QR Code</div>
          <div style={{ fontSize: '0.75rem', color: '#64748B' }}>Show to other delegates to connect</div>
        </div>
      </button>

      {/* Virtual Room placeholder */}
      <div style={{
        background: '#F8FAFC', border: '1px dashed #E2E8F0',
        borderRadius: '1rem', padding: '1rem', marginBottom: '1rem', textAlign: 'center',
      }}>
        <div style={{ fontSize: '0.875rem', fontWeight: 600, color: '#94A3B8', marginBottom: '0.25rem' }}>
          🎥 Virtual Room
        </div>
        <div style={{ fontSize: '0.75rem', color: '#CBD5E1' }}>Webinar link coming soon</div>
      </div>

      {/* Logout */}
      <button
        onClick={handleLogout}
        style={{
          width: '100%', background: '#FEF2F2', border: '1px solid #FECACA',
          borderRadius: '1rem', padding: '0.875rem 1rem',
          display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.625rem',
          cursor: 'pointer', color: '#DC2626', fontWeight: 700, fontSize: '0.9rem',
        }}
      >
        <LogOut size={18} /> Sign Out
      </button>

      <p style={{ textAlign: 'center', color: '#CBD5E1', fontSize: '0.7rem', marginTop: '1.5rem' }}>
        AWSISA Watersan Dialogue 2026 · ICC Durban
      </p>
    </div>
  )
}
