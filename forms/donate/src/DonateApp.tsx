import { useRef, useState } from 'react'
import './types'

type Stage = 'form' | 'processing' | 'success' | 'error'

const TIERS = [
  { amount: 100,  label: 'R 100',   impact: '500 litres of clean water for one month' },
  { amount: 500,  label: 'R 500',   impact: '2 hand-washing stations for a school' },
  { amount: 1000, label: 'R 1 000', impact: 'One month of WASH training for a health worker' },
  { amount: 5000, label: 'R 5 000', impact: 'Co-sponsors a university scholarship' },
]

interface FormState {
  first_name: string
  last_name: string
  email: string
  organisation: string
  amount_zar: string
  is_anonymous: boolean
  popia_consent: boolean
}

export default function DonateApp() {
  const [stage, setStage]   = useState<Stage>('form')
  const [errMsg, setErrMsg] = useState('')
  const [form, setForm]     = useState<FormState>({
    first_name:    '',
    last_name:     '',
    email:         '',
    organisation:  '',
    amount_zar:    '',
    is_anonymous:  false,
    popia_consent: false,
  })

  const payfastFormRef = useRef<HTMLFormElement>(null)

  const update = <K extends keyof FormState>(key: K, value: FormState[K]) =>
    setForm(prev => ({ ...prev, [key]: value }))

  const selectTier = (amount: number) =>
    update('amount_zar', String(amount))

  /* ── submit ── */
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    const cfg = window.awsisaDonate
    if (!cfg) { setErrMsg('Page configuration missing. Please reload.'); setStage('error'); return }

    const amount = parseFloat(form.amount_zar)
    if (!amount || amount < 10) { setErrMsg('Minimum donation is R 10.'); return }
    if (!form.popia_consent)    { setErrMsg('Please accept the privacy consent to continue.'); return }

    setStage('processing')
    setErrMsg('')

    try {
      // 1. Record donation in Supabase via WP REST API
      const res = await fetch(`${cfg.restUrl}/donate`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
        body: JSON.stringify({
          first_name:    form.first_name,
          last_name:     form.last_name,
          email:         form.email,
          organisation:  form.organisation || null,
          amount_zar:    amount,
          is_anonymous:  form.is_anonymous,
          popia_consent: form.popia_consent,
        }),
      })

      if (!res.ok) {
        const j = await res.json().catch(() => ({}))
        throw new Error((j as { message?: string }).message ?? 'Server error')
      }

      const { donation_id } = await res.json() as { donation_id: string }

      // 2. Build PayFast form and auto-submit
      if (cfg.payfastMerchant) {
        submitPayfast(cfg, amount, donation_id)
      } else {
        // Placeholder: no payment credentials yet — show success
        setStage('success')
      }
    } catch (err) {
      setErrMsg(err instanceof Error ? err.message : 'An unexpected error occurred.')
      setStage('form')
    }
  }

  const submitPayfast = (
    cfg: NonNullable<typeof window.awsisaDonate>,
    amount: number,
    donationId: string,
  ) => {
    const host = cfg.payfastSandbox
      ? 'https://sandbox.payfast.co.za/eng/process'
      : 'https://www.payfast.co.za/eng/process'

    const fields: Record<string, string> = {
      merchant_id:   cfg.payfastMerchant,
      merchant_key:  cfg.payfastKey,
      return_url:    cfg.successUrl,
      cancel_url:    cfg.cancelUrl,
      notify_url:    `${window.location.origin}/wp-json/awsisa/v1/payment/payfast`,
      name_first:    form.first_name,
      name_last:     form.last_name,
      email_address: form.email,
      m_payment_id:  donationId,
      amount:        amount.toFixed(2),
      item_name:     'AWSISA Legacy Initiative Donation',
      item_description: `Watersan Dialogue 2026 — Legacy donation`,
      email_confirmation: '1',
      confirmation_address: form.email,
    }

    const pf = payfastFormRef.current!
    pf.action = host
    pf.innerHTML = ''
    Object.entries(fields).forEach(([name, value]) => {
      const input = document.createElement('input')
      input.type  = 'hidden'
      input.name  = name
      input.value = value
      pf.appendChild(input)
    })
    pf.submit()
  }

  /* ── UI ── */
  if (stage === 'processing') {
    return (
      <div style={{ textAlign: 'center', padding: '4rem 2rem' }}>
        <div style={{ fontSize: '2.5rem', marginBottom: '1rem', animation: 'spin 1s linear infinite' }}>⏳</div>
        <p style={{ color: '#475569', fontWeight: 600 }}>Processing your donation…</p>
        <p style={{ color: '#94A3B8', fontSize: '.875rem' }}>You may be redirected to PayFast to complete payment.</p>
        {/* Hidden PayFast form */}
        <form ref={payfastFormRef} method="POST" style={{ display: 'none' }} />
      </div>
    )
  }

  if (stage === 'success') {
    return (
      <div style={{ textAlign: 'center', padding: '3rem 2rem' }}>
        <div style={{ width: 72, height: 72, borderRadius: '50%', background: 'linear-gradient(135deg,#16A34A,#0D9488)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 1.5rem', fontSize: '2rem', color: '#fff' }}>✓</div>
        <h2 style={{ fontSize: '1.5rem', fontWeight: 800, color: '#0F172A', margin: '0 0 .5rem' }}>Thank You!</h2>
        <p style={{ color: '#475569', marginBottom: '1.5rem' }}>Your donation has been recorded. A confirmation and Section 18A certificate will be sent to <strong>{form.email}</strong>.</p>
        <a href="/" className="btn btn--primary">Back to Home</a>
      </div>
    )
  }

  if (stage === 'error') {
    return (
      <div style={{ textAlign: 'center', padding: '3rem 2rem' }}>
        <div style={{ fontSize: '3rem', marginBottom: '1rem' }}>❌</div>
        <h2 style={{ color: '#0F172A', margin: '0 0 .5rem' }}>Something Went Wrong</h2>
        <p style={{ color: '#475569', marginBottom: '1.5rem' }}>{errMsg}</p>
        <button onClick={() => setStage('form')} className="btn btn--primary">Try Again</button>
      </div>
    )
  }

  return (
    <div style={{ maxWidth: 580, margin: '0 auto' }}>
      <form onSubmit={handleSubmit} noValidate>

        {/* Tier buttons */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: '.625rem', marginBottom: '1.375rem' }}>
          {TIERS.map(t => (
            <button
              key={t.amount}
              type="button"
              onClick={() => selectTier(t.amount)}
              style={{
                border: `2px solid ${form.amount_zar === String(t.amount) ? '#16A34A' : '#E2E8F0'}`,
                background: form.amount_zar === String(t.amount) ? '#F0FDF4' : '#fff',
                borderRadius: 10,
                padding: '.625rem .25rem',
                fontWeight: 700,
                fontSize: '.875rem',
                color: form.amount_zar === String(t.amount) ? '#16A34A' : '#374151',
                cursor: 'pointer',
                transition: 'all .15s',
              }}
            >
              {t.label}
            </button>
          ))}
        </div>

        {/* Impact hint */}
        {form.amount_zar && (
          <div style={{ fontSize: '.8rem', color: '#16A34A', background: '#F0FDF4', border: '1px solid #BBF7D0', borderRadius: 8, padding: '.625rem .875rem', marginBottom: '1.25rem', fontWeight: 600 }}>
            💧 {TIERS.find(t => t.amount === parseFloat(form.amount_zar))?.impact ?? 'Your donation makes a direct impact.'}
          </div>
        )}

        {/* Custom amount */}
        <div className="form-group" style={{ marginBottom: '1.125rem' }}>
          <label style={lbl}>Donation Amount (ZAR) <span style={{ color: '#EF4444' }}>*</span></label>
          <div style={{ position: 'relative' }}>
            <span style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', color: '#64748B', fontWeight: 600 }}>R</span>
            <input
              type="number"
              className="form-control"
              style={{ paddingLeft: '2.5rem' }}
              placeholder="Enter amount"
              min="10"
              step="10"
              value={form.amount_zar}
              onChange={e => update('amount_zar', e.target.value)}
              required
            />
          </div>
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1.125rem' }}>
          <div>
            <label style={lbl}>First Name <span style={{ color: '#EF4444' }}>*</span></label>
            <input type="text" className="form-control" value={form.first_name} onChange={e => update('first_name', e.target.value)} required autoComplete="given-name" />
          </div>
          <div>
            <label style={lbl}>Last Name <span style={{ color: '#EF4444' }}>*</span></label>
            <input type="text" className="form-control" value={form.last_name} onChange={e => update('last_name', e.target.value)} required autoComplete="family-name" />
          </div>
        </div>

        <div className="form-group" style={{ marginBottom: '1.125rem' }}>
          <label style={lbl}>Email Address <span style={{ color: '#EF4444' }}>*</span></label>
          <input type="email" className="form-control" value={form.email} onChange={e => update('email', e.target.value)} required autoComplete="email" />
        </div>

        <div className="form-group" style={{ marginBottom: '1.125rem' }}>
          <label style={lbl}>Organisation <span style={{ color: '#94A3B8', fontWeight: 400 }}>(optional)</span></label>
          <input type="text" className="form-control" value={form.organisation} onChange={e => update('organisation', e.target.value)} autoComplete="organization" />
        </div>

        <label style={{ display: 'flex', alignItems: 'flex-start', gap: '.5rem', cursor: 'pointer', marginBottom: '1.125rem' }}>
          <input type="checkbox" checked={form.is_anonymous} onChange={e => update('is_anonymous', e.target.checked)} style={{ marginTop: 3, accentColor: '#16A34A' }} />
          <span style={{ fontSize: '.875rem', color: '#475569' }}>Make my donation anonymous</span>
        </label>

        {/* POPIA */}
        <div className="popia-block" style={{ marginBottom: '1.25rem' }}>
          <label style={{ display: 'flex', alignItems: 'flex-start', gap: '.5rem', cursor: 'pointer' }}>
            <input type="checkbox" checked={form.popia_consent} onChange={e => update('popia_consent', e.target.checked)} required style={{ marginTop: 2, accentColor: '#16A34A', flexShrink: 0 }} />
            <span style={{ fontSize: '.8rem', color: '#374151' }}>
              I consent to AWSISA processing my personal information to process this donation and issue a tax certificate.{' '}
              <a href={window.awsisaDonate?.privacyUrl ?? '#'} target="_blank" rel="noopener" style={{ color: '#0D9488' }}>Privacy Policy</a>
            </span>
          </label>
        </div>

        {errMsg && (
          <p role="alert" style={{ color: '#EF4444', fontSize: '.875rem', marginBottom: '1rem', padding: '.75rem', background: '#FEF2F2', borderRadius: 8 }}>⚠️ {errMsg}</p>
        )}

        <button type="submit" className="btn" style={{ width: '100%', background: '#16A34A', borderColor: '#16A34A', color: '#fff', fontWeight: 700 }}>
          Donate via PayFast
        </button>

        <p style={{ fontSize: '.75rem', color: '#94A3B8', textAlign: 'center', marginTop: '.625rem' }}>
          Secure payment · Section 18A tax certificate issued within 30 days
        </p>

        {/* Hidden PayFast form for redirect */}
        <form ref={payfastFormRef} method="POST" style={{ display: 'none' }} />
      </form>
    </div>
  )
}

const lbl = { display: 'block', fontWeight: 600, fontSize: '.875rem', color: '#374151', marginBottom: '.375rem' } as React.CSSProperties
