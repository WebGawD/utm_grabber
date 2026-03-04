import type { RegFormData } from '../types'

interface Props {
  form: RegFormData
  update: (p: Partial<RegFormData>) => void
  onNext: () => void
  onBack: () => void
}

const DELEGATE_TYPES = [
  { value: 'government',     label: 'Government',     desc: 'Government officials & regulators',     icon: '🏛️' },
  { value: 'utility',        label: 'Utility',         desc: 'Water & sanitation utility professionals', icon: '💧' },
  { value: 'private_sector', label: 'Private Sector',  desc: 'Industry & technology companies',        icon: '🏢' },
  { value: 'ngo',            label: 'NGO / NPO',       desc: 'Non-governmental organisations',         icon: '🤲' },
  { value: 'academic',       label: 'Academic',        desc: 'Researchers, students, educators',       icon: '🎓' },
  { value: 'media',          label: 'Media',           desc: 'Journalists & content creators',         icon: '📰' },
  { value: 'exhibitor',      label: 'Exhibitor',       desc: 'Exhibition booth representatives',       icon: '🖥️' },
  { value: 'sponsor',        label: 'Sponsor',         desc: 'Conference sponsors',                    icon: '🌟' },
]

export default function Step2Type({ form, update, onNext, onBack }: Props) {
  const pricing = window.awsisaReg?.pricing ?? {}

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!form.delegate_type) return
    onNext()
  }

  return (
    <form onSubmit={handleSubmit} noValidate>
      <h2 style={heading}>Delegate Type</h2>
      <p style={sub}>Select the category that best describes your participation.</p>

      <div style={{
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fill, minmax(240px, 1fr))',
        gap: '1rem',
        marginBottom: '1.75rem',
      }}>
        {DELEGATE_TYPES.map(t => {
          const selected = form.delegate_type === t.value
          const price    = pricing[t.value]
          return (
            <button
              key={t.value}
              type="button"
              onClick={() => update({ delegate_type: t.value })}
              style={{
                border: `2px solid ${selected ? '#0D9488' : '#E2E8F0'}`,
                background: selected ? '#F0FDFA' : '#fff',
                borderRadius: 12,
                padding: '1rem',
                textAlign: 'left',
                cursor: 'pointer',
                transition: 'all .15s',
              }}
            >
              <div style={{ fontSize: '1.5rem', marginBottom: '.375rem' }}>{t.icon}</div>
              <div style={{ fontWeight: 700, color: '#0F172A', marginBottom: '.25rem' }}>{t.label}</div>
              <div style={{ fontSize: '.8rem', color: '#64748B', marginBottom: price ? '.5rem' : 0 }}>{t.desc}</div>
              {price && (
                <div style={{ fontSize: '.85rem', fontWeight: 700, color: '#0D9488' }}>
                  R {price.zar.toLocaleString()} <span style={{ color: '#94A3B8', fontWeight: 400 }}>/ ~${price.usd} USD</span>
                </div>
              )}
            </button>
          )
        })}
      </div>

      {!form.delegate_type && (
        <p style={{ color: '#EF4444', fontSize: '.875rem', margin: '0 0 1rem' }}>
          Please select a delegate type to continue.
        </p>
      )}

      <div style={{ display: 'flex', gap: '1rem' }}>
        <button type="button" onClick={onBack} className="btn btn--outline" style={{ flex: 1 }}>
          ← Back
        </button>
        <button type="submit" className="btn btn--primary" style={{ flex: 2 }} disabled={!form.delegate_type}>
          Continue to Accommodation →
        </button>
      </div>
    </form>
  )
}

const heading = { fontSize: '1.375rem', fontWeight: 700, color: '#0F172A', margin: '0 0 .5rem' }
const sub     = { fontSize: '.9rem', color: '#64748B', margin: '0 0 1.75rem' }
