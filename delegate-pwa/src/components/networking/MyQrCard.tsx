import { useEffect, useRef } from 'react'
import QRCode from 'qrcode'
import type { DelegateSession } from '@/types'

interface Props {
  session: DelegateSession | null
}

export default function MyQrCard({ session }: Props) {
  const canvasRef = useRef<HTMLCanvasElement>(null)

  useEffect(() => {
    if (!session?.qrToken || !canvasRef.current) return
    QRCode.toCanvas(canvasRef.current, session.qrToken, {
      width:           260,
      margin:          2,
      color:           { dark: '#0F172A', light: '#FFFFFF' },
      errorCorrectionLevel: 'M',
    }).catch(console.error)
  }, [session?.qrToken])

  if (!session) {
    return (
      <div style={{ textAlign: 'center', padding: '3rem 1rem', color: '#94A3B8' }}>
        Please sign in to view your QR code.
      </div>
    )
  }

  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: '1.5rem 1rem' }}>
      {/* Card */}
      <div style={{
        background: '#fff', border: '1px solid #E2E8F0', borderRadius: '1.25rem',
        padding: '1.5rem', textAlign: 'center', boxShadow: '0 4px 20px rgba(0,0,0,0.06)',
        maxWidth: 320, width: '100%',
      }}>
        {/* Header */}
        <div style={{ background: 'linear-gradient(135deg,#115E59,#0D9488)', borderRadius: '0.875rem', padding: '0.875rem', marginBottom: '1.25rem' }}>
          <div style={{ color: '#fff', fontFamily: 'Outfit,sans-serif', fontWeight: 800, fontSize: '1rem' }}>
            AWSISA Watersan 2026
          </div>
          <div style={{ color: '#5EEAD4', fontSize: '0.75rem', marginTop: '0.125rem' }}>
            ICC Durban · 9–12 November
          </div>
        </div>

        {/* QR Code */}
        <div style={{ display: 'flex', justifyContent: 'center', marginBottom: '1.25rem' }}>
          <div style={{ border: '6px solid #F0FDFA', borderRadius: '0.875rem', overflow: 'hidden', display: 'inline-block' }}>
            <canvas ref={canvasRef} style={{ display: 'block' }} />
          </div>
        </div>

        {/* Name & org */}
        <div style={{ fontFamily: 'Outfit,sans-serif', fontWeight: 800, fontSize: '1.125rem', color: '#0F172A', marginBottom: '0.25rem' }}>
          {session.name}
        </div>
        {session.organisation && (
          <div style={{ fontSize: '0.875rem', color: '#64748B', marginBottom: '0.5rem' }}>
            {session.organisation}
          </div>
        )}
        <span style={{
          display: 'inline-block',
          background: '#F0FDFA', color: '#0D9488',
          fontSize: '0.72rem', fontWeight: 700,
          padding: '0.2rem 0.75rem', borderRadius: '999px',
          border: '1px solid #99F6E4', textTransform: 'capitalize',
        }}>
          {session.type?.replace('_', ' ')}
        </span>
      </div>

      <p style={{ color: '#94A3B8', fontSize: '0.75rem', marginTop: '1rem', textAlign: 'center', maxWidth: 280 }}>
        Ask another delegate to scan this code — or scan theirs using the Scan tab.
      </p>
    </div>
  )
}
