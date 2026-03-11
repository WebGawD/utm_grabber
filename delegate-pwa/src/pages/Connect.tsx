import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { UserPlus, Users, CheckCircle, Loader2 } from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import type { ScannedContact } from '@/types'

const REST = 'https://staging.lubabalo.co.za/awsisa/wp-json/awsisa/v1'

const CONTACTS_KEY = 'awsisa_contacts'

interface DelegateInfo {
  delegateId:   string
  name:         string
  organisation: string | null
  email:        string | null
  delegateType: string
  qrToken:      string
}

const TYPE_COLORS: Record<string, string> = {
  delegate:   '#0D9488',
  speaker:    '#7C3AED',
  sponsor:    '#D97706',
  media:      '#0284C7',
  staff:      '#6B7280',
  vip:        '#B45309',
}

function getContacts(): ScannedContact[] {
  try { return JSON.parse(localStorage.getItem(CONTACTS_KEY) || '[]') }
  catch { return [] }
}

function saveContact(c: ScannedContact) {
  const existing = getContacts()
  const updated  = [c, ...existing.filter(x => x.delegateId !== c.delegateId)]
  localStorage.setItem(CONTACTS_KEY, JSON.stringify(updated))
}

export default function ConnectPage() {
  const { token }                     = useParams<{ token: string }>()
  const navigate                      = useNavigate()
  const { isLoggedIn, session }       = useAuth()

  const [delegate, setDelegate]       = useState<DelegateInfo | null>(null)
  const [loading, setLoading]         = useState(true)
  const [error, setError]             = useState<string | null>(null)
  const [saved, setSaved]             = useState(false)

  useEffect(() => {
    if (!token) {
      setError('Invalid link.')
      setLoading(false)
      return
    }

    fetch(`${REST}/delegate/auth/qr/${encodeURIComponent(token)}`)
      .then(r => r.ok ? r.json() : Promise.reject(r.status))
      .then(data => {
        const d = data.delegate
        const info: DelegateInfo = {
          delegateId:   d.id,
          name:         `${d.first_name} ${d.last_name}`.trim(),
          organisation: d.organisation || null,
          email:        d.email || null,
          delegateType: d.delegate_type || 'delegate',
          qrToken:      d.qr_code_token,
        }
        setDelegate(info)
        // Pre-check if already saved
        if (getContacts().some(c => c.delegateId === d.id)) setSaved(true)
      })
      .catch(() => setError('This link is invalid or the badge was not found.'))
      .finally(() => setLoading(false))
  }, [token])

  function handleAdd() {
    if (!delegate) return
    saveContact({
      delegateId:   delegate.delegateId,
      name:         delegate.name,
      organisation: delegate.organisation,
      email:        delegate.email,
      qrToken:      delegate.qrToken,
      scannedAt:    Date.now(),
    })
    setSaved(true)
  }

  const typeColor = TYPE_COLORS[delegate?.delegateType ?? ''] ?? '#0D9488'
  const isSelf    = session?.delegateId === delegate?.delegateId

  return (
    <div style={{
      minHeight: '100dvh',
      background: 'linear-gradient(160deg, #0F172A 0%, #115E59 100%)',
      display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      padding: '1.5rem',
    }}>

      {/* Header */}
      <div style={{ marginBottom: '2rem', textAlign: 'center' }}>
        <div style={{ fontSize: '0.75rem', color: '#5EEAD4', textTransform: 'uppercase', letterSpacing: '0.1em', fontWeight: 700 }}>
          AWSISA Watersan Dialogue 2026
        </div>
        <div style={{ fontSize: '0.7rem', color: '#94A3B8', marginTop: '0.25rem' }}>
          Networking · Badge Connect
        </div>
      </div>

      {/* Loading */}
      {loading && (
        <div style={{ textAlign: 'center', color: '#CCFBF1' }}>
          <Loader2 size={36} style={{ animation: 'spin 1s linear infinite', margin: '0 auto 1rem', display: 'block' }} />
          <p style={{ fontSize: '0.875rem', opacity: 0.7, margin: 0 }}>Looking up badge…</p>
          <style>{`@keyframes spin { to { transform: rotate(360deg) } }`}</style>
        </div>
      )}

      {/* Error */}
      {error && !loading && (
        <div style={{ textAlign: 'center', color: '#CCFBF1', maxWidth: 320 }}>
          <div style={{ fontSize: '3rem', marginBottom: '1rem' }}>🔗</div>
          <p style={{ fontWeight: 700, fontSize: '1.1rem', margin: '0 0 0.5rem', fontFamily: 'Outfit,sans-serif' }}>Badge not found</p>
          <p style={{ fontSize: '0.875rem', opacity: 0.7, margin: '0 0 2rem' }}>{error}</p>
          <button
            onClick={() => navigate(isLoggedIn ? '/home' : '/login')}
            style={{ background: '#0D9488', color: '#fff', border: 'none', borderRadius: '0.75rem', padding: '0.75rem 1.75rem', fontWeight: 700, cursor: 'pointer' }}
          >
            {isLoggedIn ? 'Go to Home' : 'Open App'}
          </button>
        </div>
      )}

      {/* Delegate card */}
      {delegate && !loading && (
        <div style={{
          background: '#fff', borderRadius: '1.25rem', padding: '2rem',
          width: '100%', maxWidth: 360,
          boxShadow: '0 24px 64px rgba(0,0,0,0.35)',
        }}>

          {/* Avatar + info */}
          <div style={{ textAlign: 'center', marginBottom: '1.5rem' }}>
            <div style={{
              width: 80, height: 80, borderRadius: '50%',
              background: `linear-gradient(135deg, ${typeColor}cc, ${typeColor})`,
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              color: '#fff', fontWeight: 800, fontSize: '2rem',
              fontFamily: 'Outfit,sans-serif', margin: '0 auto 1rem',
              boxShadow: `0 6px 24px ${typeColor}44`,
            }}>
              {delegate.name.charAt(0).toUpperCase()}
            </div>

            <h2 style={{ margin: 0, fontWeight: 800, fontSize: '1.3rem', color: '#0F172A', fontFamily: 'Outfit,sans-serif' }}>
              {delegate.name}
            </h2>
            {delegate.organisation && (
              <p style={{ margin: '0.25rem 0 0', color: '#64748B', fontSize: '0.875rem' }}>
                {delegate.organisation}
              </p>
            )}
            {delegate.email && (
              <p style={{ margin: '0.25rem 0 0', color: '#0D9488', fontSize: '0.8rem' }}>
                {delegate.email}
              </p>
            )}

            {/* Type badge */}
            <div style={{ marginTop: '0.75rem' }}>
              <span style={{
                display: 'inline-block', padding: '0.2rem 0.75rem', borderRadius: '9999px',
                fontSize: '0.7rem', fontWeight: 700,
                background: `${typeColor}18`, color: typeColor,
                textTransform: 'uppercase', letterSpacing: '0.06em',
              }}>
                {delegate.delegateType}
              </span>
            </div>
          </div>

          <div style={{ borderTop: '1px solid #F1F5F9', marginBottom: '1.5rem' }} />

          {/* CTA */}
          {!isLoggedIn ? (
            /* Not logged in — prompt to login */
            <div style={{ textAlign: 'center' }}>
              <p style={{ color: '#64748B', fontSize: '0.875rem', margin: '0 0 1rem', lineHeight: 1.5 }}>
                Log in to save this contact to your network
              </p>
              <button
                onClick={() => navigate('/login', { state: { returnTo: `/connect/${token}` } })}
                style={{
                  width: '100%', padding: '0.875rem',
                  background: 'linear-gradient(135deg, #0D9488, #115E59)',
                  color: '#fff', border: 'none', borderRadius: '0.875rem',
                  fontWeight: 700, fontSize: '0.95rem', cursor: 'pointer',
                  display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.5rem',
                  fontFamily: 'Outfit,sans-serif',
                }}
              >
                <UserPlus size={18} /> Log in to Connect
              </button>
            </div>

          ) : isSelf ? (
            /* Scanning own badge */
            <p style={{ textAlign: 'center', color: '#94A3B8', fontSize: '0.875rem', margin: 0 }}>
              This is your own badge
            </p>

          ) : saved ? (
            /* Already saved / just saved */
            <div style={{ textAlign: 'center' }}>
              <div style={{
                color: '#0D9488', display: 'flex', alignItems: 'center',
                justifyContent: 'center', gap: '0.5rem', fontWeight: 700,
                marginBottom: '1rem', fontSize: '0.95rem',
              }}>
                <CheckCircle size={20} /> Contact saved!
              </div>
              <button
                onClick={() => navigate('/networking')}
                style={{
                  width: '100%', padding: '0.875rem',
                  background: '#F8FAFC', color: '#0D9488',
                  border: '1.5px solid #0D9488', borderRadius: '0.875rem',
                  fontWeight: 700, fontSize: '0.95rem', cursor: 'pointer',
                  display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.5rem',
                  fontFamily: 'Outfit,sans-serif',
                }}
              >
                <Users size={18} /> View Contacts
              </button>
            </div>

          ) : (
            /* Add contact button */
            <button
              onClick={handleAdd}
              style={{
                width: '100%', padding: '0.875rem',
                background: 'linear-gradient(135deg, #0D9488, #115E59)',
                color: '#fff', border: 'none', borderRadius: '0.875rem',
                fontWeight: 700, fontSize: '0.95rem', cursor: 'pointer',
                display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.5rem',
                fontFamily: 'Outfit,sans-serif',
              }}
            >
              <UserPlus size={18} /> Add to Contacts
            </button>
          )}
        </div>
      )}

      <p style={{ color: '#475569', fontSize: '0.7rem', marginTop: '2rem', textAlign: 'center' }}>
        AWSISA 2026 · Powered by Lubabalo
      </p>
    </div>
  )
}
