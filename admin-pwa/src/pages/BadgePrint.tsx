import { useEffect, useState, useMemo } from 'react'
import { supabase } from '@/lib/supabase'
import QRCode from 'qrcode'
import { Printer, RefreshCw, Search, ChevronDown } from 'lucide-react'
import type { Delegate, DelegateType } from '@/types'

// ── Colour map ────────────────────────────────────────────────────────────────

const TYPE_META: Record<DelegateType, { bg: string; text: string; label: string }> = {
  government:     { bg: '#1E40AF', text: '#DBEAFE', label: 'Government' },
  utility:        { bg: '#0E7490', text: '#CFFAFE', label: 'Utility' },
  private_sector: { bg: '#6D28D9', text: '#EDE9FE', label: 'Private Sector' },
  ngo:            { bg: '#065F46', text: '#D1FAE5', label: 'NGO' },
  academic:       { bg: '#92400E', text: '#FEF3C7', label: 'Academic' },
  media:          { bg: '#9A3412', text: '#FFEDD5', label: 'Media' },
  exhibitor:      { bg: '#334155', text: '#E2E8F0', label: 'Exhibitor' },
  sponsor:        { bg: '#78350F', text: '#FEF9C3', label: 'Sponsor' },
}

// ── Types ─────────────────────────────────────────────────────────────────────

type FilterType = 'all' | DelegateType

// ── Component ─────────────────────────────────────────────────────────────────

export default function BadgePrintPage() {
  const [delegates, setDelegates]   = useState<Delegate[]>([])
  const [qrMap, setQrMap]           = useState<Record<string, string>>({})
  const [loading, setLoading]       = useState(true)
  const [error, setError]           = useState<string | null>(null)
  const [search, setSearch]         = useState('')
  const [typeFilter, setTypeFilter] = useState<FilterType>('all')
  const [checkedOnly, setCheckedOnly] = useState(false)

  // ── Fetch delegates ──────────────────────────────────────────────────────────

  async function fetchDelegates() {
    setLoading(true)
    setError(null)

    const { data, error: err } = await supabase
      .from('delegates')
      .select('id, first_name, last_name, organisation, delegate_type, qr_code_token, checked_in, registration_status')
      .eq('registration_status', 'confirmed')
      .order('last_name', { ascending: true })

    if (err) {
      setError(err.message)
      setLoading(false)
      return
    }

    setDelegates((data ?? []) as Delegate[])
    setLoading(false)
  }

  useEffect(() => { fetchDelegates() }, [])

  // ── Generate QR codes ────────────────────────────────────────────────────────

  useEffect(() => {
    if (delegates.length === 0) return

    const generate = async () => {
      const entries = await Promise.all(
        delegates.map(async (d) => {
          const url = await QRCode.toDataURL(d.qr_code_token, {
            width: 120,
            margin: 1,
            color: { dark: '#000000', light: '#FFFFFF' },
          })
          return [d.id, url] as [string, string]
        })
      )
      setQrMap(Object.fromEntries(entries))
    }

    generate()
  }, [delegates])

  // ── Filtered list ────────────────────────────────────────────────────────────

  const filtered = useMemo(() => {
    const q = search.toLowerCase().trim()
    return delegates.filter(d => {
      if (typeFilter !== 'all' && d.delegate_type !== typeFilter) return false
      if (checkedOnly && !d.checked_in) return false
      if (q) {
        const hay = `${d.first_name} ${d.last_name} ${d.organisation ?? ''}`.toLowerCase()
        if (!hay.includes(q)) return false
      }
      return true
    })
  }, [delegates, search, typeFilter, checkedOnly])

  const qrReady = filtered.every(d => qrMap[d.id])

  // ── Print handler ────────────────────────────────────────────────────────────

  function handlePrint() {
    window.print()
  }

  // ── Render ───────────────────────────────────────────────────────────────────

  return (
    <>
      {/* ── Print styles ── injected into <head> at runtime ─────────────────── */}
      <style>{`
        @media print {
          @page { size: A4 portrait; margin: 8mm; }

          body * { visibility: hidden !important; }
          #badge-print-area,
          #badge-print-area * { visibility: visible !important; }

          #badge-print-area {
            position: fixed !important;
            top: 0; left: 0;
            width: 100%;
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 4mm;
            padding: 0;
            margin: 0;
          }

          .badge-card {
            width: 90mm;
            height: 58mm;
            page-break-inside: avoid;
            break-inside: avoid;
          }
        }
      `}</style>

      {/* ── Screen: controls ─────────────────────────────────────────────────── */}
      <div className="print:hidden">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-heading font-bold text-white">Badge Print</h1>
            <p className="text-slate-400 text-sm mt-1">
              {loading ? 'Loading…' : `${filtered.length} of ${delegates.length} confirmed delegates`}
            </p>
          </div>
          <div className="flex items-center gap-3">
            <button
              onClick={fetchDelegates}
              disabled={loading}
              className="btn-secondary flex items-center gap-2"
              title="Refresh delegate list"
            >
              <RefreshCw size={15} className={loading ? 'animate-spin' : ''} />
              Refresh
            </button>
            <button
              onClick={handlePrint}
              disabled={loading || filtered.length === 0 || !qrReady}
              className="btn-primary flex items-center gap-2"
            >
              <Printer size={15} />
              Print {filtered.length > 0 ? `(${filtered.length})` : ''}
            </button>
          </div>
        </div>

        {/* Filters */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-center">
          {/* Search */}
          <div className="relative flex-1 min-w-48">
            <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" />
            <input
              type="text"
              className="input pl-9"
              placeholder="Search name or organisation…"
              value={search}
              onChange={e => setSearch(e.target.value)}
            />
          </div>

          {/* Type filter */}
          <div className="relative">
            <select
              className="input appearance-none pr-8 cursor-pointer"
              value={typeFilter}
              onChange={e => setTypeFilter(e.target.value as FilterType)}
            >
              <option value="all">All types</option>
              {Object.entries(TYPE_META).map(([key, { label }]) => (
                <option key={key} value={key}>{label}</option>
              ))}
            </select>
            <ChevronDown size={14} className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none" />
          </div>

          {/* Checked-in only toggle */}
          <label className="flex items-center gap-2 text-sm text-slate-300 cursor-pointer select-none">
            <input
              type="checkbox"
              className="w-4 h-4 accent-primary rounded"
              checked={checkedOnly}
              onChange={e => setCheckedOnly(e.target.checked)}
            />
            Checked-in only
          </label>
        </div>

        {/* Error */}
        {error && (
          <div className="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm mb-6">
            Error: {error}
          </div>
        )}

        {/* QR generation progress */}
        {!loading && filtered.length > 0 && !qrReady && (
          <div className="text-slate-500 text-sm mb-4 flex items-center gap-2">
            <RefreshCw size={13} className="animate-spin" />
            Generating QR codes…
          </div>
        )}

        {/* Badge preview grid */}
        {!loading && filtered.length === 0 && (
          <div className="text-center py-16 text-slate-600">
            No confirmed delegates match the current filter.
          </div>
        )}
      </div>

      {/* ── Badge grid (visible on screen + print) ───────────────────────────── */}
      <div
        id="badge-print-area"
        className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
      >
        {filtered.map(delegate => (
          <BadgeCard
            key={delegate.id}
            delegate={delegate}
            qrDataUrl={qrMap[delegate.id] ?? null}
          />
        ))}
      </div>
    </>
  )
}

// ── BadgeCard ──────────────────────────────────────────────────────────────────

function BadgeCard({ delegate, qrDataUrl }: { delegate: Delegate; qrDataUrl: string | null }) {
  const typeMeta = TYPE_META[delegate.delegate_type] ?? TYPE_META.exhibitor

  const fullName = `${delegate.first_name} ${delegate.last_name}`
  const org      = delegate.organisation ?? ''

  return (
    <div
      className="badge-card bg-white rounded-lg overflow-hidden shadow-lg border border-slate-200 flex flex-col"
      style={{ width: '90mm', height: '58mm', fontFamily: 'Arial, sans-serif' }}
    >
      {/* Header strip */}
      <div
        className="px-3 py-1.5 flex items-center justify-between flex-shrink-0"
        style={{ backgroundColor: '#0D9488' }}
      >
        <span style={{ color: '#fff', fontSize: '7pt', fontWeight: '600', letterSpacing: '0.03em' }}>
          AWSISA Watersan Dialogue 2026
        </span>
        <span style={{ color: '#99f6e4', fontSize: '6.5pt' }}>
          ICC Durban · 9–12 Nov
        </span>
      </div>

      {/* Main body */}
      <div className="flex flex-1 overflow-hidden">

        {/* Left: name + org */}
        <div className="flex-1 px-3 py-2 flex flex-col justify-center overflow-hidden">
          <div
            style={{
              fontSize: fullName.length > 22 ? '12pt' : '14pt',
              fontWeight: '800',
              color: '#0F172A',
              lineHeight: 1.15,
              wordBreak: 'break-word',
            }}
          >
            {delegate.first_name}
          </div>
          <div
            style={{
              fontSize: fullName.length > 22 ? '12pt' : '14pt',
              fontWeight: '800',
              color: '#0F172A',
              lineHeight: 1.15,
              marginBottom: '4pt',
              wordBreak: 'break-word',
            }}
          >
            {delegate.last_name}
          </div>
          {org && (
            <div
              style={{
                fontSize: org.length > 30 ? '7.5pt' : '8.5pt',
                color: '#475569',
                lineHeight: 1.2,
                wordBreak: 'break-word',
              }}
            >
              {org}
            </div>
          )}
        </div>

        {/* Right: QR code */}
        <div
          className="flex-shrink-0 flex items-center justify-center p-2"
          style={{ width: '32mm' }}
        >
          {qrDataUrl ? (
            <img
              src={qrDataUrl}
              alt={`QR code for ${fullName}`}
              style={{ width: '28mm', height: '28mm', display: 'block' }}
            />
          ) : (
            <div
              style={{
                width: '28mm', height: '28mm',
                backgroundColor: '#F1F5F9',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
              }}
            >
              <span style={{ fontSize: '6pt', color: '#94A3B8' }}>…</span>
            </div>
          )}
        </div>
      </div>

      {/* Footer: delegate type */}
      <div
        className="flex-shrink-0 px-3 py-1 flex items-center"
        style={{ backgroundColor: typeMeta.bg }}
      >
        <span style={{ color: typeMeta.text, fontSize: '7pt', fontWeight: '700', textTransform: 'uppercase', letterSpacing: '0.08em' }}>
          {typeMeta.label}
        </span>
      </div>
    </div>
  )
}
