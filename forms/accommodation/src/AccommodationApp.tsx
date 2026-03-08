import { useEffect, useState } from 'react'
import type { AccomPackage } from './types'
import './types'

type Stage = 'select' | 'details' | 'processing' | 'success' | 'error'

interface BookingForm {
  delegate_email:  string
  check_in_date:   string
  check_out_date:  string
  notes:           string
  popia_consent:   boolean
}

const DEFAULT_FORM: BookingForm = {
  delegate_email: '',
  check_in_date:  '2026-11-08',
  check_out_date: '2026-11-13',
  notes:          '',
  popia_consent:  false,
}

export default function AccommodationApp() {
  const [stage, setStage]               = useState<Stage>('select')
  const [packages, setPackages]         = useState<AccomPackage[]>([])
  const [loadingPkgs, setLoadingPkgs]   = useState(true)
  const [selected, setSelected]         = useState<AccomPackage | null>(null)
  const [form, setForm]                 = useState<BookingForm>(DEFAULT_FORM)
  const [submitting, setSubmitting]     = useState(false)
  const [errMsg, setErrMsg]             = useState('')
  const [reference, setReference]       = useState('')

  const update = <K extends keyof BookingForm>(key: K, value: BookingForm[K]) =>
    setForm(prev => ({ ...prev, [key]: value }))

  const cfg = window.awsisaAccom

  /* ── fetch packages ── */
  useEffect(() => {
    if (!cfg?.supabaseUrl) { setLoadingPkgs(false); return }

    fetch(`${cfg.supabaseUrl}/rest/v1/accommodation_packages?is_active=eq.true&order=price_zar.asc`, {
      headers: { apikey: cfg.supabaseKey, Authorization: `Bearer ${cfg.supabaseKey}` },
    })
      .then(r => r.json())
      .then((d: unknown) => setPackages(Array.isArray(d) ? d as AccomPackage[] : []))
      .catch(() => {})
      .finally(() => setLoadingPkgs(false))
  }, [])

  /* ── derived ── */
  const nights = (() => {
    const a = new Date(form.check_in_date)
    const b = new Date(form.check_out_date)
    return Math.max(1, Math.round((b.getTime() - a.getTime()) / 86400000))
  })()

  /* ── submit booking ── */
  const handleBook = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!selected || !cfg) return
    if (!form.popia_consent) { setErrMsg('Please accept the privacy consent.'); return }

    setSubmitting(true)
    setErrMsg('')

    try {
      const res = await fetch(`${cfg.restUrl}/accommodation/book`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
        body: JSON.stringify({
          package_id:       selected.id,
          delegate_email:   form.delegate_email,
          check_in_date:    form.check_in_date,
          check_out_date:   form.check_out_date,
          special_requests: form.notes || null,
          popia_consent:    form.popia_consent,
        }),
      })

      const json = await res.json().catch(() => ({})) as { booking_id?: string; message?: string }

      if (!res.ok) throw new Error(json.message ?? 'Booking failed')

      setReference(json.booking_id ?? 'AWSISA-' + Date.now())
      setStage('success')
    } catch (err) {
      setErrMsg(err instanceof Error ? err.message : 'An unexpected error occurred.')
    } finally {
      setSubmitting(false)
    }
  }

  /* ── stage: success ── */
  if (stage === 'success') {
    return (
      <div style={{ textAlign: 'center', padding: '3rem 1rem', maxWidth: 560, margin: '0 auto' }}>
        <div style={{ width: 72, height: 72, borderRadius: '50%', background: 'linear-gradient(135deg,#0D9488,#0369A1)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 1.5rem', fontSize: '2rem', color: '#fff' }}>✓</div>
        <h2 style={{ fontSize: '1.5rem', fontWeight: 800, color: '#0F172A', margin: '0 0 .5rem' }}>Booking Confirmed!</h2>
        <p style={{ color: '#475569', marginBottom: '1.5rem' }}>
          Your room at <strong>{selected?.hotel_name}</strong> has been reserved. Check-in{' '}
          <strong>{form.check_in_date}</strong> → <strong>{form.check_out_date}</strong>.
        </p>
        {reference && (
          <div style={{ background: '#F0FDFA', border: '2px solid #99F6E4', borderRadius: 12, padding: '1rem', marginBottom: '1.5rem', display: 'inline-block' }}>
            <div style={{ fontSize: '.75rem', color: '#94A3B8', marginBottom: '.25rem' }}>Booking Reference</div>
            <div style={{ fontWeight: 800, fontSize: '1.25rem', color: '#0D9488', letterSpacing: '.05em' }}>{reference}</div>
          </div>
        )}
        <p style={{ fontSize: '.875rem', color: '#64748B', marginBottom: '2rem' }}>
          Booking details have been sent to <strong>{form.delegate_email}</strong>. Payment is settled directly at the hotel on arrival.
        </p>
        <a href="/" className="btn btn--primary">Back to Home</a>
      </div>
    )
  }

  /* ── stage: details ── */
  if (stage === 'details' && selected) {
    const totalZar = selected.price_zar * nights
    const totalUsd = selected.price_usd * nights
    let amenities: string[] = []
    try {
      amenities = Array.isArray(selected.amenities)
        ? selected.amenities
        : JSON.parse((selected.amenities as unknown as string) || '[]')
    } catch {
      amenities = typeof selected.amenities === 'string'
        ? (selected.amenities as unknown as string).replace(/^\{|\}$/g, '').split(',').map(s => s.trim()).filter(Boolean)
        : []
    }

    return (
      <div style={{ maxWidth: 560, margin: '0 auto' }}>
        {/* Selected package summary */}
        <div style={{ border: '2px solid #0D9488', borderRadius: 12, padding: '1.125rem', marginBottom: '1.75rem', background: '#F0FDFA' }}>
          <div style={{ fontWeight: 700, color: '#0F172A', marginBottom: '.25rem' }}>{selected.hotel_name}</div>
          <div style={{ fontSize: '.8rem', color: '#64748B', marginBottom: '.625rem' }}>{selected.description}</div>
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: '.375rem', marginBottom: '.75rem' }}>
            {amenities.map(a => (
              <span key={a} style={{ fontSize: '.7rem', padding: '.2rem .5rem', background: '#CCFBF1', color: '#0D9488', borderRadius: 20, fontWeight: 600 }}>{a}</span>
            ))}
          </div>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <button type="button" onClick={() => { setSelected(null); setStage('select') }} style={{ fontSize: '.8rem', color: '#64748B', background: 'none', border: 'none', cursor: 'pointer' }}>← Change room</button>
            <div style={{ textAlign: 'right' }}>
              <div style={{ fontWeight: 800, fontSize: '1.25rem', color: '#0D9488' }}>R {totalZar.toLocaleString()}</div>
              <div style={{ fontSize: '.75rem', color: '#94A3B8' }}>~${totalUsd.toLocaleString()} USD · {nights} nights</div>
            </div>
          </div>
        </div>

        <form onSubmit={handleBook} noValidate>
          <h3 style={{ fontSize: '1.125rem', fontWeight: 700, color: '#0F172A', margin: '0 0 1.25rem' }}>Your Details</h3>

          <div style={{ marginBottom: '1.125rem' }}>
            <label style={lbl}>Registration Email <span style={{ color: '#EF4444' }}>*</span></label>
            <input
              type="email"
              className="form-control"
              placeholder="The email used to register as a delegate"
              value={form.delegate_email}
              onChange={e => update('delegate_email', e.target.value)}
              required
              autoComplete="email"
            />
            <p style={{ fontSize: '.75rem', color: '#94A3B8', margin: '.3rem 0 0' }}>Must match your delegate registration email</p>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1.125rem' }}>
            <div>
              <label style={lbl}>Check-in Date</label>
              <input
                type="date"
                className="form-control"
                value={form.check_in_date}
                min="2026-11-08"
                max="2026-11-12"
                onChange={e => update('check_in_date', e.target.value)}
                required
              />
            </div>
            <div>
              <label style={lbl}>Check-out Date</label>
              <input
                type="date"
                className="form-control"
                value={form.check_out_date}
                min="2026-11-09"
                max="2026-11-14"
                onChange={e => update('check_out_date', e.target.value)}
                required
              />
            </div>
          </div>

          <div style={{ marginBottom: '1.25rem' }}>
            <label style={lbl}>Special Requests <span style={{ color: '#94A3B8', fontWeight: 400 }}>(optional)</span></label>
            <textarea
              className="form-control"
              rows={2}
              placeholder="Accessibility needs, twin beds, quiet room, etc."
              value={form.notes}
              onChange={e => update('notes', e.target.value)}
            />
          </div>

          {/* POPIA */}
          <div className="popia-block" style={{ marginBottom: '1.25rem' }}>
            <label style={{ display: 'flex', alignItems: 'flex-start', gap: '.5rem', cursor: 'pointer' }}>
              <input
                type="checkbox"
                checked={form.popia_consent}
                onChange={e => update('popia_consent', e.target.checked)}
                required
                style={{ marginTop: 2, accentColor: '#0D9488', flexShrink: 0 }}
              />
              <span style={{ fontSize: '.8rem', color: '#374151' }}>
                I consent to AWSISA processing my personal information to facilitate this accommodation booking.{' '}
                <a href={window.awsisaAccom?.privacyUrl ?? '#'} target="_blank" rel="noopener" style={{ color: '#0D9488' }}>Privacy Policy</a>
              </span>
            </label>
          </div>

          {errMsg && (
            <p role="alert" style={{ color: '#EF4444', fontSize: '.875rem', padding: '.75rem', background: '#FEF2F2', borderRadius: 8, marginBottom: '1rem' }}>⚠️ {errMsg}</p>
          )}

          <button type="submit" className="btn btn--primary" style={{ width: '100%' }} disabled={submitting}>
            {submitting ? 'Confirming Booking…' : 'Confirm Booking'}
          </button>

          <p style={{ fontSize: '.75rem', color: '#94A3B8', textAlign: 'center', marginTop: '.625rem' }}>
            Payment is settled directly at the hotel — no card required now.
          </p>
        </form>
      </div>
    )
  }

  /* ── stage: select ── */
  return (
    <div style={{ maxWidth: 800, margin: '0 auto' }}>
      <h2 style={{ fontSize: '1.375rem', fontWeight: 700, color: '#0F172A', margin: '0 0 .5rem' }}>Choose Your Package</h2>
      <p style={{ fontSize: '.9rem', color: '#64748B', margin: '0 0 1.75rem' }}>All rates are per room per night and include breakfast.</p>

      {loadingPkgs ? (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          {[1, 2, 3].map(i => (
            <div key={i} style={{ height: 130, borderRadius: 12, background: '#F1F5F9', animation: 'pulse 1.5s ease-in-out infinite' }} />
          ))}
        </div>
      ) : packages.length === 0 ? (
        <div style={{ padding: '3rem', textAlign: 'center', background: '#F8FAFC', borderRadius: 12 }}>
          <p style={{ color: '#64748B' }}>Accommodation packages are not yet available. Please check back later.</p>
        </div>
      ) : (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          {packages.map(pkg => {
            const available = pkg.total_rooms - pkg.booked_count
            const soldOut   = available <= 0
            let amenities: string[] = []
            try {
              amenities = Array.isArray(pkg.amenities)
                ? pkg.amenities
                : JSON.parse((pkg.amenities as unknown as string) || '[]')
            } catch {
              amenities = typeof pkg.amenities === 'string'
                ? (pkg.amenities as unknown as string).replace(/^\{|\}$/g, '').split(',').map(s => s.trim()).filter(Boolean)
                : []
            }

            return (
              <div
                key={pkg.id}
                style={{
                  border: `2px solid ${soldOut ? '#E2E8F0' : '#E2E8F0'}`,
                  borderRadius: 12,
                  padding: '1.25rem',
                  opacity: soldOut ? .6 : 1,
                  background: '#fff',
                  display: 'flex',
                  gap: '1.25rem',
                  alignItems: 'flex-start',
                  flexWrap: 'wrap',
                }}
              >
                <div style={{ flex: 1, minWidth: 200 }}>
                  <div style={{ fontWeight: 700, color: '#0F172A', marginBottom: '.25rem' }}>{pkg.hotel_name}</div>
                  <div style={{ fontSize: '.8rem', color: '#64748B', marginBottom: '.625rem' }}>{pkg.description}</div>
                  <div style={{ display: 'flex', flexWrap: 'wrap', gap: '.375rem' }}>
                    {amenities.map(a => (
                      <span key={a} style={{ fontSize: '.7rem', padding: '.2rem .5rem', background: '#F0FDFA', color: '#0D9488', borderRadius: 20, fontWeight: 600 }}>{a}</span>
                    ))}
                  </div>
                </div>
                <div style={{ textAlign: 'right', flexShrink: 0 }}>
                  <div style={{ fontWeight: 800, fontSize: '1.375rem', color: '#0D9488' }}>
                    R {pkg.price_zar.toLocaleString()}
                  </div>
                  <div style={{ fontSize: '.75rem', color: '#94A3B8', marginBottom: '.625rem' }}>
                    /night · ~${pkg.price_usd} USD
                  </div>
                  {!soldOut && available <= 15 && (
                    <div style={{ fontSize: '.75rem', color: '#F59E0B', fontWeight: 700, marginBottom: '.5rem' }}>Only {available} left</div>
                  )}
                  {soldOut ? (
                    <button className="btn btn--outline" disabled style={{ fontSize: '.875rem' }}>Sold Out</button>
                  ) : (
                    <button
                      type="button"
                      className="btn btn--primary"
                      style={{ fontSize: '.875rem' }}
                      onClick={() => { setSelected(pkg); setStage('details') }}
                    >
                      Select Room →
                    </button>
                  )}
                </div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}

const lbl = { display: 'block', fontWeight: 600, fontSize: '.875rem', color: '#374151', marginBottom: '.375rem' } as React.CSSProperties
