import { useState, useEffect, useRef, useCallback } from 'react'
import { Html5Qrcode } from 'html5-qrcode'
import { supabase } from '@/lib/supabase'
import { enqueueCheckin } from '@/hooks/useOfflineQueue'
import { useAuth } from '@/hooks/useAuth'
import type { Delegate } from '@/types'
import toast from 'react-hot-toast'
import {
  CheckCircle, XCircle, Camera, CameraOff,
  Search, UserCheck, AlertTriangle,
} from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'

type ScanResult =
  | { status: 'success'; delegate: Delegate }
  | { status: 'already_in'; delegate: Delegate }
  | { status: 'not_found' }
  | { status: 'error'; message: string }

const DELEGATE_TYPE_LABELS: Record<string, string> = {
  government: 'Government', utility: 'Utility', private_sector: 'Private Sector',
  ngo: 'NGO', academic: 'Academic', media: 'Media', exhibitor: 'Exhibitor', sponsor: 'Sponsor',
}

// Stable device ID for deduplication.
function getDeviceId() {
  let id = localStorage.getItem('awsisa_device_id')
  if (!id) {
    id = `device_${Date.now()}_${Math.random().toString(36).slice(2)}`
    localStorage.setItem('awsisa_device_id', id)
  }
  return id
}

export default function CheckInPage() {
  const { user }                            = useAuth()
  const [scannerActive, setScannerActive]   = useState(false)
  const [scanResult, setScanResult]         = useState<ScanResult | null>(null)
  const [recentCheckins, setRecentCheckins] = useState<Array<{
    delegate: Pick<Delegate, 'first_name' | 'last_name' | 'organisation' | 'delegate_type'>
    time: string
  }>>([])
  const [manualToken, setManualToken]       = useState('')
  const [processing, setProcessing]         = useState(false)

  const scannerRef = useRef<Html5Qrcode | null>(null)
  const deviceId   = getDeviceId()

  const processToken = useCallback(async (token: string) => {
    if (processing) return
    setProcessing(true)

    try {
      // Lookup delegate by QR token.
      const { data, error } = await supabase
        .from('delegates')
        .select('*')
        .eq('qr_code_token', token.trim())
        .single()

      if (error || !data) {
        setScanResult({ status: 'not_found' })
        toast.error('Delegate not found for this QR code.')
        setProcessing(false)
        return
      }

      const delegate = data as Delegate

      if (delegate.checked_in) {
        setScanResult({ status: 'already_in', delegate })
        toast('Already checked in', { icon: '⚠️' })
        setProcessing(false)
        return
      }

      if (navigator.onLine) {
        // Online: update Supabase directly.
        const { error: updateError } = await supabase
          .from('delegates')
          .update({
            checked_in:    true,
            checked_in_at: new Date().toISOString(),
            updated_at:    new Date().toISOString(),
          })
          .eq('id', delegate.id)

        if (updateError) throw updateError

        // Write to audit log.
        await supabase.from('checkin_log').insert({
          delegate_id:   delegate.id,
          checked_in_by: user?.id,
          method:        'qr',
          device_id:     deviceId,
          synced_at:     new Date().toISOString(),
        })
      } else {
        // Offline: enqueue to IndexedDB.
        await enqueueCheckin({
          id:          `${Date.now()}_${delegate.id}`,
          delegate_id: delegate.id,
          method:      'qr',
          device_id:   deviceId,
          created_at:  new Date().toISOString(),
        })
      }

      setScanResult({ status: 'success', delegate })
      toast.success(`✓ Checked in: ${delegate.first_name} ${delegate.last_name}`)

      // Add to recent list.
      setRecentCheckins(prev => [{
        delegate: {
          first_name:    delegate.first_name,
          last_name:     delegate.last_name,
          organisation:  delegate.organisation,
          delegate_type: delegate.delegate_type,
        },
        time: new Date().toISOString(),
      }, ...prev].slice(0, 20))

      // Auto-clear result after 5 s.
      setTimeout(() => setScanResult(null), 5000)

    } catch (err) {
      const msg = err instanceof Error ? err.message : 'Unknown error'
      setScanResult({ status: 'error', message: msg })
      toast.error(`Check-in error: ${msg}`)
    } finally {
      setProcessing(false)
    }
  }, [processing, user, deviceId])

  // Start the QR camera scanner.
  async function startScanner() {
    try {
      scannerRef.current = new Html5Qrcode('qr-reader')
      await scannerRef.current.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => { processToken(decodedText) },
        () => {} // suppress frame errors
      )
      setScannerActive(true)
    } catch {
      toast.error('Could not access camera. Check permissions.')
    }
  }

  async function stopScanner() {
    if (scannerRef.current?.isScanning) {
      await scannerRef.current.stop()
    }
    setScannerActive(false)
  }

  // Clean up on unmount.
  useEffect(() => {
    return () => { scannerRef.current?.stop().catch(() => {}) }
  }, [])

  async function handleManualSubmit(e: React.FormEvent) {
    e.preventDefault()
    if (!manualToken.trim()) return
    await processToken(manualToken)
    setManualToken('')
  }

  return (
    <div className="max-w-5xl space-y-6">
      <div>
        <h1 className="text-2xl font-heading font-bold text-white">QR Check-In</h1>
        <p className="text-slate-400 text-sm mt-1">Scan delegate QR codes for instant check-in</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {/* Scanner panel */}
        <div className="space-y-4">
          <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h2 className="text-base font-semibold text-white mb-4 flex items-center gap-2">
              <Camera size={18} className="text-primary-light" />
              Camera Scanner
            </h2>

            {/* QR reader target */}
            <div className="qr-viewport mb-4" aria-label="QR code scanning area">
              <div id="qr-reader" className="w-full h-full" />
              {scannerActive && <div className="scan-line" aria-hidden="true" />}

              {/* Corner markers */}
              {[
                'top-3 left-3 border-t-2 border-l-2',
                'top-3 right-3 border-t-2 border-r-2',
                'bottom-3 left-3 border-b-2 border-l-2',
                'bottom-3 right-3 border-b-2 border-r-2',
              ].map((cls, i) => (
                <div key={i} className={`qr-corner absolute ${cls}`} aria-hidden="true" />
              ))}

              {!scannerActive && (
                <div className="absolute inset-0 flex items-center justify-center text-slate-500">
                  <div className="text-center">
                    <CameraOff size={36} className="mx-auto mb-2 opacity-40" />
                    <p className="text-sm">Camera is off</p>
                  </div>
                </div>
              )}
            </div>

            <button
              onClick={scannerActive ? stopScanner : startScanner}
              className={scannerActive ? 'btn-danger w-full justify-center' : 'btn-primary w-full justify-center'}
            >
              {scannerActive ? <><CameraOff size={16} /> Stop Scanner</> : <><Camera size={16} /> Start Camera</>}
            </button>
          </div>

          {/* Manual token entry */}
          <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h2 className="text-base font-semibold text-white mb-4 flex items-center gap-2">
              <Search size={18} className="text-primary-light" />
              Manual Entry
            </h2>
            <form onSubmit={handleManualSubmit} className="flex gap-2">
              <input
                type="text"
                className="input flex-1"
                placeholder="Delegate QR token or email…"
                value={manualToken}
                onChange={e => setManualToken(e.target.value)}
                aria-label="Manual delegate token or email"
              />
              <button type="submit" disabled={!manualToken.trim() || processing} className="btn-primary">
                {processing ? (
                  <span className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                ) : <UserCheck size={16} />}
              </button>
            </form>
          </div>
        </div>

        {/* Result + recent panel */}
        <div className="space-y-4">

          {/* Scan result card */}
          {scanResult && (
            <div className={`rounded-xl p-5 border ${
              scanResult.status === 'success'
                ? 'bg-green-500/10 border-green-500/30'
                : scanResult.status === 'already_in'
                ? 'bg-amber-500/10 border-amber-500/30'
                : 'bg-red-500/10 border-red-500/30'
            }`}>
              {scanResult.status === 'success' && (
                <div className="flex items-start gap-4">
                  <CheckCircle size={32} className="text-green-400 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="text-green-400 font-bold text-lg">
                      {scanResult.delegate.first_name} {scanResult.delegate.last_name}
                    </div>
                    <div className="text-green-300/80 text-sm mt-0.5">{scanResult.delegate.organisation}</div>
                    <div className="mt-2">
                      <span className="badge badge-success text-xs">
                        {DELEGATE_TYPE_LABELS[scanResult.delegate.delegate_type] ?? scanResult.delegate.delegate_type}
                      </span>
                    </div>
                    <div className="text-green-300/60 text-xs mt-2">✓ Successfully checked in</div>
                  </div>
                </div>
              )}

              {scanResult.status === 'already_in' && (
                <div className="flex items-start gap-4">
                  <AlertTriangle size={32} className="text-amber-400 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="text-amber-400 font-bold text-lg">Already Checked In</div>
                    <div className="text-amber-300/80 text-sm">
                      {scanResult.delegate.first_name} {scanResult.delegate.last_name}
                    </div>
                    {scanResult.delegate.checked_in_at && (
                      <div className="text-amber-300/60 text-xs mt-1">
                        Checked in {formatDistanceToNow(new Date(scanResult.delegate.checked_in_at), { addSuffix: true })}
                      </div>
                    )}
                  </div>
                </div>
              )}

              {(scanResult.status === 'not_found' || scanResult.status === 'error') && (
                <div className="flex items-start gap-4">
                  <XCircle size={32} className="text-red-400 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="text-red-400 font-bold">
                      {scanResult.status === 'not_found' ? 'Delegate Not Found' : 'Error'}
                    </div>
                    <div className="text-red-300/80 text-sm mt-0.5">
                      {scanResult.status === 'error'
                        ? scanResult.message
                        : 'No delegate registered with this QR code. Try manual search.'}
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Recent check-ins */}
          <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div className="px-5 py-4 border-b border-slate-800">
              <h2 className="text-base font-semibold text-white">Recent Check-Ins</h2>
              <p className="text-slate-500 text-xs mt-0.5">This session only</p>
            </div>
            {recentCheckins.length === 0 ? (
              <div className="px-5 py-8 text-center text-slate-600 text-sm">
                No check-ins this session yet.
              </div>
            ) : (
              <ul className="divide-y divide-slate-800">
                {recentCheckins.map((entry, i) => (
                  <li key={i} className="px-5 py-3 flex items-center gap-3">
                    <div className="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                      <CheckCircle size={14} className="text-green-400" />
                    </div>
                    <div className="flex-1 min-w-0">
                      <div className="text-sm font-semibold text-white truncate">
                        {entry.delegate.first_name} {entry.delegate.last_name}
                      </div>
                      <div className="text-xs text-slate-500 truncate">{entry.delegate.organisation}</div>
                    </div>
                    <div className="text-right flex-shrink-0">
                      <span className="badge badge-success text-xs">
                        {DELEGATE_TYPE_LABELS[entry.delegate.delegate_type] ?? entry.delegate.delegate_type}
                      </span>
                      <div className="text-xs text-slate-600 mt-1">
                        {formatDistanceToNow(new Date(entry.time), { addSuffix: true })}
                      </div>
                    </div>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
