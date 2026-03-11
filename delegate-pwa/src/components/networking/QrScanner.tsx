import { useEffect, useRef, useState } from 'react'
import { Html5Qrcode } from 'html5-qrcode'
import toast from 'react-hot-toast'
import { Camera, CheckCircle, XCircle } from 'lucide-react'
import { WP_REST } from '@/lib/supabase'
import type { ScannedContact } from '@/types'

interface Props {
  onContact: (contact: ScannedContact) => void
}

// 'processing' = camera stopped, waiting for API lookup result
type ScanState = 'idle' | 'scanning' | 'processing' | 'success' | 'error'

export default function QrScanner({ onContact }: Props) {
  const scannerRef = useRef<Html5Qrcode | null>(null)
  const [state, setState]       = useState<ScanState>('idle')
  const [contact, setContact]   = useState<ScannedContact | null>(null)
  const [error, setError]       = useState('')
  const [started, setStarted]   = useState(false)

  useEffect(() => {
    return () => {
      scannerRef.current?.stop().catch(() => {})
    }
  }, [])

  async function startScanner() {
    setStarted(true)
    setState('scanning')
    try {
      const scanner = new Html5Qrcode('qr-reader')
      scannerRef.current = scanner
      await scanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 240, height: 240 } },
        async (decodedText) => {
          await scanner.stop()
          setStarted(false)
          await handleScan(decodedText)
        },
        () => { /* quiet per-frame decode errors */ }
      )
    } catch (e) {
      const msg = (e as Error).message || 'Camera access denied'
      setError(msg)
      setState('error')
      setStarted(false)
    }
  }

  async function handleScan(raw: string) {
    // Show spinner immediately — no more "blank screen" while the lookup runs
    setState('processing')

    // Extract bare token whether the QR contains a full URL or just the token.
    // NFC badges encode the full connect URL; printed badges encode just the token.
    const token = raw.includes('/connect/')
      ? raw.split('/connect/').pop()!.split('?')[0]
      : raw.trim()

    try {
      // Use the same WP REST endpoint as Connect.tsx — runs with the service
      // key on the server side so RLS on the delegates table is not an issue.
      const res = await fetch(`${WP_REST}/delegate/auth/qr/${encodeURIComponent(token)}`)
      if (!res.ok) throw new Error('Delegate not found')
      const data = await res.json()

      // Fire-and-forget NFC tap log
      fetch(`${WP_REST}/nfc/tap`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tap_type: 'delegate_view', visitor_token: token }),
      }).catch(() => {})

      const scanned: ScannedContact = {
        delegateId:   data.id,
        name:         `${data.first_name} ${data.last_name}`.trim(),
        organisation: data.organisation || null,
        email:        data.profile_public ? data.email : null,
        qrToken:      token,
        scannedAt:    Date.now(),
      }
      setContact(scanned)
      setState('success')
      onContact(scanned)
      toast.success(`Connected with ${data.first_name}!`)
    } catch (e) {
      setError((e as Error).message || 'Delegate not found')
      setState('error')
    }
  }

  function reset() {
    setContact(null)
    setError('')
    setState('idle')
    setStarted(false)
  }

  // ── Processing (spinner) ────────────────────────────────────────────────────
  if (state === 'processing') {
    return (
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '4rem 1rem', textAlign: 'center' }}>
        <div style={{ width: 52, height: 52, border: '4px solid #E2E8F0', borderTopColor: '#0D9488', borderRadius: '50%', animation: 'qr-spin 0.8s linear infinite', marginBottom: '1.25rem' }} />
        <p style={{ color: '#0D9488', fontWeight: 700, fontSize: '1rem', margin: '0 0 0.25rem' }}>Looking up delegate…</p>
        <p style={{ color: '#94A3B8', fontSize: '0.8rem' }}>Just a moment</p>
        <style>{`@keyframes qr-spin { to { transform: rotate(360deg) } }`}</style>
      </div>
    )
  }

  // ── Success ─────────────────────────────────────────────────────────────────
  if (state === 'success' && contact) {
    return (
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: '2rem 1rem', textAlign: 'center' }}>
        <CheckCircle size={56} style={{ color: '#16A34A', marginBottom: '1rem' }} />
        <h2 style={{ fontFamily: 'Outfit,sans-serif', fontWeight: 700, fontSize: '1.25rem', color: '#0F172A', margin: '0 0 0.375rem' }}>
          Connected!
        </h2>
        <div style={{ background: '#fff', border: '1px solid #E2E8F0', borderRadius: '1rem', padding: '1rem', margin: '1rem 0', width: '100%', maxWidth: 280 }}>
          <div style={{ fontWeight: 700, fontSize: '1rem', color: '#0F172A', marginBottom: '0.25rem' }}>{contact.name}</div>
          {contact.organisation && <div style={{ fontSize: '0.875rem', color: '#64748B', marginBottom: '0.25rem' }}>{contact.organisation}</div>}
          {contact.email && <div style={{ fontSize: '0.8rem', color: '#0D9488' }}>{contact.email}</div>}
        </div>
        <p style={{ color: '#64748B', fontSize: '0.875rem', marginBottom: '1.25rem' }}>
          Saved to your contacts.
        </p>
        <button
          onClick={reset}
          style={{ background: '#0D9488', color: '#fff', fontWeight: 700, fontSize: '0.9rem', padding: '0.75rem 2rem', borderRadius: '0.75rem', border: 'none', cursor: 'pointer' }}
        >
          Scan Another
        </button>
      </div>
    )
  }

  // ── Error ────────────────────────────────────────────────────────────────────
  if (state === 'error') {
    return (
      <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: '2rem 1rem', textAlign: 'center' }}>
        <XCircle size={48} style={{ color: '#EF4444', marginBottom: '1rem' }} />
        <p style={{ color: '#64748B', marginBottom: '1.25rem' }}>{error || 'Could not read this QR code. Please try again.'}</p>
        <button onClick={reset} style={{ background: '#0D9488', color: '#fff', fontWeight: 700, padding: '0.75rem 2rem', borderRadius: '0.75rem', border: 'none', cursor: 'pointer' }}>
          Try Again
        </button>
      </div>
    )
  }

  // ── Idle / Scanning ──────────────────────────────────────────────────────────
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: '1.25rem 1rem' }}>
      {/* Camera viewfinder */}
      <div style={{ position: 'relative', width: '100%', maxWidth: 320, marginBottom: '1rem' }}>
        <div
          id="qr-reader"
          style={{
            width: '100%', borderRadius: '1rem', overflow: 'hidden',
            background: '#0F172A',
            minHeight: started ? 280 : 0,
            border: started ? '2px solid #0D9488' : 'none',
          }}
        />
        {started && (
          <div style={{
            position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%,-50%)',
            width: 220, height: 220, borderRadius: '0.75rem',
            border: '2px solid #0D9488', pointerEvents: 'none', boxShadow: '0 0 0 9999px rgba(0,0,0,0.5)',
          }} />
        )}
      </div>

      {!started ? (
        <div style={{ textAlign: 'center' }}>
          <div style={{ width: 80, height: 80, borderRadius: '50%', background: '#F0FDFA', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 1rem' }}>
            <Camera size={36} style={{ color: '#0D9488' }} />
          </div>
          <h3 style={{ fontFamily: 'Outfit,sans-serif', fontWeight: 700, fontSize: '1.1rem', color: '#0F172A', margin: '0 0 0.5rem' }}>
            Scan a Delegate's QR
          </h3>
          <p style={{ color: '#64748B', fontSize: '0.875rem', margin: '0 0 1.25rem', maxWidth: 260, lineHeight: 1.5 }}>
            Point your camera at another delegate's QR code card to connect and exchange details.
          </p>
          <button
            onClick={startScanner}
            style={{ background: '#0D9488', color: '#fff', fontWeight: 700, fontSize: '0.95rem', padding: '0.875rem 2rem', borderRadius: '0.875rem', border: 'none', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '0.5rem', margin: '0 auto' }}
          >
            <Camera size={18} /> Open Camera
          </button>
          <p style={{ color: '#94A3B8', fontSize: '0.72rem', marginTop: '0.75rem' }}>
            Camera permission required
          </p>
        </div>
      ) : (
        <div style={{ textAlign: 'center' }}>
          <p style={{ color: '#0D9488', fontWeight: 600, fontSize: '0.875rem', margin: '0 0 0.75rem', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '0.375rem' }}>
            <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#16A34A', display: 'inline-block', animation: 'pulse 1s ease-in-out infinite' }} />
            Scanning…
          </p>
          <button
            onClick={() => { scannerRef.current?.stop().catch(() => {}); reset() }}
            style={{ color: '#EF4444', fontWeight: 600, fontSize: '0.8rem', background: 'none', border: 'none', cursor: 'pointer' }}
          >
            Cancel
          </button>
        </div>
      )}
      <style>{`@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }`}</style>
    </div>
  )
}
