import type { RegFormData } from '../types'

interface Props {
  delegateId: string | null
  form: RegFormData
}

export default function Step5Confirmation({ delegateId, form }: Props) {
  const qrUrl = delegateId
    ? `https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=${encodeURIComponent(delegateId)}&bgcolor=ffffff&color=0D9488&margin=10`
    : null

  return (
    <div style={{ textAlign: 'center', padding: '1rem 0 2rem' }}>
      {/* Success icon */}
      <div style={{
        width: 72, height: 72, borderRadius: '50%',
        background: 'linear-gradient(135deg, #0D9488, #16A34A)',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        margin: '0 auto 1.5rem', fontSize: '2rem',
      }}>
        ✓
      </div>

      <h2 style={{ fontSize: '1.75rem', fontWeight: 800, color: '#0F172A', margin: '0 0 .5rem' }}>
        You're Registered!
      </h2>
      <p style={{ fontSize: '1rem', color: '#64748B', margin: '0 0 2rem' }}>
        Welcome to the AWSISA Watersan Dialogue 2026, <strong>{form.first_name}</strong>!
      </p>

      {/* QR code */}
      {qrUrl && (
        <div style={{ marginBottom: '2rem' }}>
          <p style={{ fontSize: '.875rem', color: '#475569', marginBottom: '.75rem' }}>
            Your personal conference QR code — save or screenshot this.
          </p>
          <img
            src={qrUrl}
            alt={`QR code for delegate ${delegateId}`}
            style={{ width: 200, height: 200, borderRadius: 12, border: '4px solid #F0FDFA', display: 'block', margin: '0 auto' }}
          />
          <p style={{ fontSize: '.75rem', color: '#94A3B8', marginTop: '.5rem' }}>
            Scan at registration desk for express check-in
          </p>
        </div>
      )}

      {/* Confirmation box */}
      <div style={{
        background: '#F0FDFA', border: '1px solid #99F6E4', borderRadius: 16,
        padding: '1.5rem', marginBottom: '2rem', textAlign: 'left', maxWidth: 500, margin: '0 auto 2rem',
      }}>
        <h3 style={{ fontSize: '1rem', fontWeight: 700, color: '#0F172A', margin: '0 0 1rem' }}>📧 What Happens Next</h3>
        <ul style={{ margin: 0, padding: '0 0 0 1.125rem', fontSize: '.875rem', color: '#374151', lineHeight: 2 }}>
          <li>Confirmation email sent to <strong>{form.email}</strong> (check spam if not received)</li>
          <li>QR code for express check-in included in the email</li>
          {form.wants_accommodation && form.accommodation_package_id && (
            <li>Accommodation booking reference included in the email</li>
          )}
          <li>Conference app details and schedule shared 2 weeks before the event</li>
          <li>Badge and materials ready for collection on arrival</li>
        </ul>
      </div>

      {/* CTAs */}
      <div style={{ display: 'flex', flexWrap: 'wrap', gap: '1rem', justifyContent: 'center' }}>
        <a href="/agenda/" className="btn btn--outline">View Programme</a>
        {!form.wants_accommodation && (
          <a href="/accommodation/" className="btn btn--primary">Book Accommodation</a>
        )}
        <a href="/donate/" className="btn btn--outline" style={{ borderColor: '#16A34A', color: '#16A34A' }}>
          💚 Support the Legacy Initiative
        </a>
      </div>

      {/* Social share nudge */}
      <div style={{ marginTop: '2.5rem', padding: '1.25rem', background: '#F8FAFC', borderRadius: 12 }}>
        <p style={{ fontSize: '.875rem', color: '#475569', margin: '0 0 .75rem' }}>
          Share that you're attending Watersan Dialogue 2026!
        </p>
        <div style={{ display: 'flex', gap: '.75rem', justifyContent: 'center', flexWrap: 'wrap' }}>
          <a
            href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(`I'm attending the AWSISA Watersan Dialogue 2026 at Emperors Palace, Johannesburg — 9–12 Nov 2026. Join 5,000+ water & sanitation professionals! #WatersanDialogue2026 #AWSISA`)}`}
            target="_blank"
            rel="noopener"
            className="btn btn--outline"
            style={{ fontSize: '.8rem', padding: '.5rem 1rem' }}
          >
            Share on 𝕏
          </a>
          <a
            href={`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.origin)}`}
            target="_blank"
            rel="noopener"
            className="btn btn--outline"
            style={{ fontSize: '.8rem', padding: '.5rem 1rem' }}
          >
            Share on LinkedIn
          </a>
        </div>
      </div>
    </div>
  )
}
