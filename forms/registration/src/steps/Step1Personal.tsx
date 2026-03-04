import type { RegFormData } from '../types'
import { FormField } from '../components/FormField'
import { COUNTRIES } from '../data/countries'

interface Props {
  form: RegFormData
  update: (p: Partial<RegFormData>) => void
  onNext: () => void
}

export default function Step1Personal({ form, update, onNext }: Props) {
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onNext()
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <h2 style={heading}>Personal Details</h2>
      <p style={sub}>Tell us about yourself so we can prepare your badge and QR code.</p>

      <div style={grid2}>
        <FormField label="First Name" required>
          <input
            type="text"
            className="form-control"
            value={form.first_name}
            onChange={e => update({ first_name: e.target.value })}
            required
            autoComplete="given-name"
          />
        </FormField>
        <FormField label="Last Name" required>
          <input
            type="text"
            className="form-control"
            value={form.last_name}
            onChange={e => update({ last_name: e.target.value })}
            required
            autoComplete="family-name"
          />
        </FormField>
      </div>

      <FormField label="Email Address" required hint="Your QR code and confirmation will be sent here.">
        <input
          type="email"
          className="form-control"
          value={form.email}
          onChange={e => update({ email: e.target.value })}
          required
          autoComplete="email"
        />
      </FormField>

      <FormField label="Phone Number" hint="Include country code, e.g. +27 82 000 0000">
        <input
          type="tel"
          className="form-control"
          value={form.phone}
          onChange={e => update({ phone: e.target.value })}
          autoComplete="tel"
        />
      </FormField>

      <div style={grid2}>
        <FormField label="Organisation">
          <input
            type="text"
            className="form-control"
            value={form.organisation}
            onChange={e => update({ organisation: e.target.value })}
            autoComplete="organization"
          />
        </FormField>
        <FormField label="Job Title">
          <input
            type="text"
            className="form-control"
            value={form.job_title}
            onChange={e => update({ job_title: e.target.value })}
            autoComplete="organization-title"
          />
        </FormField>
      </div>

      <FormField label="Country" required>
        <select
          className="form-control"
          value={form.country}
          onChange={e => update({ country: e.target.value })}
          required
        >
          <option value="">— Select your country —</option>
          {COUNTRIES.map(c => (
            <option key={c.code} value={c.code}>{c.name}</option>
          ))}
        </select>
      </FormField>

      <FormField label="Dietary Requirements" hint="Vegetarian, halal, kosher, allergies, etc.">
        <input
          type="text"
          className="form-control"
          value={form.dietary_requirements}
          onChange={e => update({ dietary_requirements: e.target.value })}
          placeholder="None"
        />
      </FormField>

      <FormField label="Accessibility Needs" hint="Wheelchair access, hearing loop, large print, etc.">
        <input
          type="text"
          className="form-control"
          value={form.accessibility_needs}
          onChange={e => update({ accessibility_needs: e.target.value })}
          placeholder="None"
        />
      </FormField>

      <button type="submit" className="btn btn--primary" style={{ width: '100%', marginTop: '1.5rem' }}>
        Continue to Delegate Type →
      </button>
    </form>
  )
}

const heading = { fontSize: '1.375rem', fontWeight: 700, color: '#0F172A', margin: '0 0 .5rem' }
const sub     = { fontSize: '.9rem', color: '#64748B', margin: '0 0 1.75rem' }
const grid2   = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } as React.CSSProperties
