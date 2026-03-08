import { useEffect, useState } from 'react'
import type { RegFormData } from '../types'

interface AccomPackage {
  id: string
  name: string
  hotel_name: string
  description: string
  price_zar: number
  price_usd: number
  nights: number
  total_rooms: number
  booked_count: number
  amenities: string[]
}

interface Props {
  form: RegFormData
  update: (p: Partial<RegFormData>) => void
  onNext: () => void
  onBack: () => void
}

const NIGHTS = 4 // 8–12 Nov default

export default function Step3Accommodation({ form, update, onNext, onBack }: Props) {
  const [packages, setPackages]   = useState<AccomPackage[]>([])
  const [loading, setLoading]     = useState(true)

  useEffect(() => {
    const cfg = window.awsisaReg
    if (!cfg?.supabaseUrl) { setLoading(false); return }

    fetch(`${cfg.supabaseUrl}/rest/v1/accommodation_packages?is_active=eq.true&order=price_zar.asc`, {
      headers: { apikey: cfg.supabaseKey, Authorization: `Bearer ${cfg.supabaseKey}` },
    })
      .then(r => r.json())
      .then((data: unknown) => { if (Array.isArray(data)) setPackages(data) })
      .catch(() => {/* show empty state */})
      .finally(() => setLoading(false))
  }, [])

  const nights = (() => {
    if (!form.accommodation_check_in || !form.accommodation_check_out) return NIGHTS
    const a = new Date(form.accommodation_check_in)
    const b = new Date(form.accommodation_check_out)
    return Math.max(1, Math.round((b.getTime() - a.getTime()) / 86400000))
  })()

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onNext()
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <h2 style={heading}>Accommodation</h2>
      <p style={sub}>All packages include breakfast and are per room per night. You will settle payment directly at the hotel.</p>

      {/* Toggle */}
      <div style={{ marginBottom: '1.5rem' }}>
        <label style={{ display: 'flex', alignItems: 'center', gap: '.75rem', cursor: 'pointer', padding: '1rem', background: '#F8FAFC', borderRadius: 10, border: '2px solid #E2E8F0' }}>
          <input
            type="checkbox"
            checked={form.wants_accommodation}
            onChange={e => update({ wants_accommodation: e.target.checked, accommodation_package_id: '' })}
            style={{ width: 20, height: 20, accentColor: '#0D9488' }}
          />
          <span style={{ fontWeight: 600, color: '#0F172A' }}>I need accommodation during the conference</span>
        </label>
      </div>

      {form.wants_accommodation && (
        <>
          {/* Date range */}
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1.5rem' }}>
            <div>
              <label style={labelStyle}>Check-in Date</label>
              <input
                type="date"
                className="form-control"
                value={form.accommodation_check_in}
                min="2026-11-08"
                max="2026-11-12"
                onChange={e => update({ accommodation_check_in: e.target.value })}
                required={form.wants_accommodation}
              />
            </div>
            <div>
              <label style={labelStyle}>Check-out Date</label>
              <input
                type="date"
                className="form-control"
                value={form.accommodation_check_out}
                min="2026-11-09"
                max="2026-11-14"
                onChange={e => update({ accommodation_check_out: e.target.value })}
                required={form.wants_accommodation}
              />
            </div>
          </div>

          {/* Packages */}
          {loading ? (
            <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem', marginBottom: '1.5rem' }}>
              {[1, 2, 3].map(i => (
                <div key={i} className="skeleton" style={{ height: 120, borderRadius: 12 }} />
              ))}
            </div>
          ) : packages.length === 0 ? (
            <p style={{ color: '#64748B', textAlign: 'center', padding: '2rem', background: '#F8FAFC', borderRadius: 12 }}>
              Packages not yet available — check back closer to the event.
            </p>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem', marginBottom: '1.5rem' }}>
              {packages.map(pkg => {
                const available = pkg.total_rooms - pkg.booked_count
                const soldOut   = available <= 0
                const selected  = form.accommodation_package_id === pkg.id
                let amenities: string[] = []
                try {
                  amenities = Array.isArray(pkg.amenities)
                    ? pkg.amenities
                    : JSON.parse(pkg.amenities || '[]')
                } catch {
                  // Supabase may return PostgreSQL text[] as "{item1,item2}" — parse it manually
                  amenities = typeof (pkg.amenities as unknown) === 'string'
                    ? (pkg.amenities as unknown as string).replace(/^\{|\}$/g, '').split(',').map((s: string) => s.trim()).filter(Boolean)
                    : []
                }
                const pkgNights = pkg.nights || nights

                return (
                  <button
                    key={pkg.id}
                    type="button"
                    disabled={soldOut}
                    onClick={() => update({ accommodation_package_id: selected ? '' : pkg.id })}
                    style={{
                      border: `2px solid ${selected ? '#0D9488' : soldOut ? '#E2E8F0' : '#E2E8F0'}`,
                      background: selected ? '#F0FDFA' : soldOut ? '#F8FAFC' : '#fff',
                      borderRadius: 12,
                      padding: '1.125rem',
                      textAlign: 'left',
                      cursor: soldOut ? 'not-allowed' : 'pointer',
                      opacity: soldOut ? .55 : 1,
                      transition: 'all .15s',
                      width: '100%',
                    }}
                  >
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '.5rem' }}>
                      <div style={{ flex: 1 }}>
                        <div style={{ fontWeight: 700, color: '#0F172A', marginBottom: '.25rem' }}>{pkg.hotel_name}</div>
                        <div style={{ fontSize: '.8rem', color: '#64748B', marginBottom: '.625rem' }}>{pkg.description}</div>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '.375rem' }}>
                          {amenities.map((a: string) => (
                            <span key={a} style={{ fontSize: '.7rem', padding: '.2rem .5rem', background: '#F0FDFA', color: '#0D9488', borderRadius: 20, fontWeight: 600 }}>{a}</span>
                          ))}
                        </div>
                      </div>
                      <div style={{ textAlign: 'right', flexShrink: 0 }}>
                        <div style={{ fontSize: '.75rem', color: '#94A3B8' }}>{pkgNights} nights</div>
                        <div style={{ fontWeight: 800, fontSize: '1.25rem', color: '#0D9488' }}>R {(pkg.price_zar ?? 0).toLocaleString()}</div>
                        {(pkg.price_usd ?? 0) > 0 && <div style={{ fontSize: '.7rem', color: '#94A3B8' }}>~${pkg.price_usd.toLocaleString()} USD</div>}
                        {!soldOut && available <= 15 && (
                          <div style={{ fontSize: '.7rem', color: '#F59E0B', fontWeight: 700, marginTop: '.25rem' }}>Only {available} left</div>
                        )}
                        {soldOut && <div style={{ fontSize: '.7rem', color: '#EF4444', fontWeight: 700 }}>Sold Out</div>}
                      </div>
                    </div>
                    {selected && (
                      <div style={{ marginTop: '.75rem', paddingTop: '.75rem', borderTop: '1px solid #99F6E4', color: '#0D9488', fontSize: '.8rem', fontWeight: 600 }}>
                        ✓ Selected — you'll receive a booking reference with your registration confirmation
                      </div>
                    )}
                  </button>
                )
              })}
            </div>
          )}

          {/* Notes */}
          <div style={{ marginBottom: '1.5rem' }}>
            <label style={labelStyle}>Special Requests <span style={{ color: '#94A3B8', fontWeight: 400 }}>(optional)</span></label>
            <textarea
              className="form-control"
              rows={2}
              placeholder="Accessibility needs, twin beds, high floor, etc."
              value={form.accommodation_notes}
              onChange={e => update({ accommodation_notes: e.target.value })}
            />
          </div>
        </>
      )}

      <div style={{ display: 'flex', gap: '1rem' }}>
        <button type="button" onClick={onBack} className="btn btn--outline" style={{ flex: 1 }}>← Back</button>
        <button type="submit" className="btn btn--primary" style={{ flex: 2 }}>
          {form.wants_accommodation && !form.accommodation_package_id
            ? 'Skip Accommodation →'
            : 'Review Registration →'}
        </button>
      </div>
    </form>
  )
}

const heading   = { fontSize: '1.375rem', fontWeight: 700, color: '#0F172A', margin: '0 0 .5rem' } as React.CSSProperties
const sub       = { fontSize: '.9rem', color: '#64748B', margin: '0 0 1.75rem' } as React.CSSProperties
const labelStyle = { display: 'block', fontWeight: 600, fontSize: '.875rem', color: '#374151', marginBottom: '.375rem' } as React.CSSProperties
