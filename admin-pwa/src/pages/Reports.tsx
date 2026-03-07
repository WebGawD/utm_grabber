import { useEffect, useState } from 'react'
import { supabase } from '../lib/supabase'
import { format } from 'date-fns'
import type { Delegate, Donation } from '../types'

type ReportTab = 'delegates' | 'donations' | 'accommodation' | 'checkin'

interface CountryRow { country: string; count: number }
interface TypeRow    { type: string;    count: number }

interface ReportSummary {
  totalDelegates:    number
  checkedIn:         number
  byType:            TypeRow[]
  byCountry:         CountryRow[]
  totalDonations:    number
  donationAmountZar: number
  accommodationBooked: number
  nfcTaps:           number
}

const TYPE_LABELS: Record<string, string> = {
  government:     'Government',
  utility:        'Utility',
  private_sector: 'Private Sector',
  ngo:            'NGO / NPO',
  academic:       'Academic',
  media:          'Media',
  exhibitor:      'Exhibitor',
  sponsor:        'Sponsor',
}

export default function Reports() {
  const [tab, setTab]           = useState<ReportTab>('delegates')
  const [summary, setSummary]   = useState<ReportSummary | null>(null)
  const [delegates, setDelegates] = useState<Delegate[]>([])
  const [donations, setDonations] = useState<Donation[]>([])
  const [loading, setLoading]   = useState(true)
  const [exporting, setExporting] = useState(false)

  useEffect(() => {
    Promise.all([
      supabase.from('delegates').select('*').order('created_at', { ascending: false }),
      supabase.from('donations').select('*').order('created_at', { ascending: false }),
      supabase.from('accommodation_bookings').select('id').eq('status', 'confirmed'),
      supabase.from('nfc_taps').select('id'),
    ]).then(([d, don, acc, taps]) => {
      const rows  = (d.data ?? []) as Delegate[]
      const dons  = (don.data ?? []) as Donation[]
      setDelegates(rows)
      setDonations(dons)

      // Build summary
      const byType: Record<string, number>    = {}
      const byCountry: Record<string, number> = {}
      rows.forEach(r => {
        byType[r.delegate_type]   = (byType[r.delegate_type] ?? 0) + 1
        byCountry[r.country]      = (byCountry[r.country] ?? 0) + 1
      })

      setSummary({
        totalDelegates:    rows.length,
        checkedIn:         rows.filter(r => r.checked_in).length,
        byType:            Object.entries(byType).map(([type, count]) => ({ type, count })).sort((a, b) => b.count - a.count),
        byCountry:         Object.entries(byCountry).map(([country, count]) => ({ country, count })).sort((a, b) => b.count - a.count),
        totalDonations:    dons.length,
        donationAmountZar: dons.reduce((s, x) => s + (x.amount_zar ?? 0), 0),
        accommodationBooked: acc.data?.length ?? 0,
        nfcTaps:           taps.data?.length ?? 0,
      })
    }).finally(() => setLoading(false))
  }, [])

  /* ── CSV export ── */
  const exportCSV = async (type: ReportTab) => {
    setExporting(true)
    try {
      let rows: Record<string, unknown>[] = []
      let filename = ''

      if (type === 'delegates') {
        rows = delegates.map(d => ({
          id:             d.id,
          first_name:     d.first_name,
          last_name:      d.last_name,
          email:          d.email,
          phone:          d.phone ?? '',
          organisation:   d.organisation ?? '',
          job_title:      d.job_title ?? '',
          country:        d.country,
          delegate_type:  d.delegate_type,
          checked_in:     d.checked_in ? 'Yes' : 'No',
          dietary:        d.dietary_requirements ?? '',
          accessibility:  d.accessibility_needs ?? '',
          registered_at:  format(new Date(d.created_at), 'yyyy-MM-dd HH:mm'),
        }))
        filename = `awsisa-delegates-${format(new Date(), 'yyyyMMdd')}.csv`
      } else if (type === 'donations') {
        rows = donations.map(d => ({
          id:           d.id,
          donor_name:   d.is_anonymous ? 'Anonymous' : d.donor_name,
          donor_email:  d.is_anonymous ? '' : d.donor_email,
          amount_zar:   d.amount_zar,
          is_anonymous: d.is_anonymous ? 'Yes' : 'No',
          status:       d.payment_status,
          donated_at:   format(new Date(d.created_at), 'yyyy-MM-dd HH:mm'),
        }))
        filename = `awsisa-donations-${format(new Date(), 'yyyyMMdd')}.csv`
      } else if (type === 'checkin') {
        const { data } = await supabase
          .from('checkin_log')
          .select('*, delegates(first_name, last_name, email, delegate_type)')
          .order('checked_in_at', { ascending: true })
        rows = (data ?? []).map((r: Record<string, unknown>) => {
          const del = r['delegates'] as Record<string, string> | null
          return {
            delegate_email: del?.email ?? '',
            name:           `${del?.first_name ?? ''} ${del?.last_name ?? ''}`.trim(),
            type:           del?.delegate_type ?? '',
            device_id:      r['device_id'] ?? '',
            checked_in_at:  r['checked_in_at'] ? format(new Date(r['checked_in_at'] as string), 'yyyy-MM-dd HH:mm:ss') : '',
          }
        })
        filename = `awsisa-checkins-${format(new Date(), 'yyyyMMdd')}.csv`
      } else {
        const { data } = await supabase
          .from('accommodation_bookings')
          .select('*, accommodation_packages(hotel_name), delegates(first_name, last_name, email)')
          .order('created_at', { ascending: false })
        rows = (data ?? []).map((r: Record<string, unknown>) => {
          const del = r['delegates'] as Record<string, string> | null
          const pkg = r['accommodation_packages'] as Record<string, string> | null
          return {
            booking_id:    r['id'],
            delegate:      `${del?.first_name ?? ''} ${del?.last_name ?? ''}`.trim(),
            email:         del?.email ?? '',
            hotel:         pkg?.hotel_name ?? '',
            check_in:      r['check_in_date'],
            check_out:     r['check_out_date'],
            status:        r['status'],
            booked_at:     r['created_at'] ? format(new Date(r['created_at'] as string), 'yyyy-MM-dd HH:mm') : '',
          }
        })
        filename = `awsisa-accommodation-${format(new Date(), 'yyyyMMdd')}.csv`
      }

      if (!rows.length) { alert('No data to export.'); return }

      const headers = Object.keys(rows[0])
      const csv = [
        headers.join(','),
        ...rows.map(r =>
          headers.map(h => {
            const v = String(r[h] ?? '')
            return v.includes(',') || v.includes('"') || v.includes('\n')
              ? `"${v.replace(/"/g, '""')}"` : v
          }).join(',')
        ),
      ].join('\n')

      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
      const url  = URL.createObjectURL(blob)
      const a    = document.createElement('a')
      a.href     = url
      a.download = filename
      a.click()
      URL.revokeObjectURL(url)
    } finally {
      setExporting(false)
    }
  }

  if (loading) {
    return (
      <div className="p-8">
        <div className="skeleton h-8 w-48 mb-6 rounded-lg" />
        <div className="grid grid-cols-4 gap-4 mb-8">
          {[1,2,3,4].map(i => <div key={i} className="skeleton h-28 rounded-xl" />)}
        </div>
        <div className="skeleton h-64 rounded-xl" />
      </div>
    )
  }

  const s = summary!

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white">Reports &amp; Export</h1>
          <p className="text-slate-400 text-sm mt-0.5">Download CSV exports or review summary breakdowns.</p>
        </div>
        <button
          onClick={() => exportCSV(tab)}
          disabled={exporting}
          className="btn-primary flex items-center gap-2"
        >
          <span>{exporting ? '⏳' : '⬇️'}</span>
          {exporting ? 'Exporting…' : `Export ${tab.charAt(0).toUpperCase() + tab.slice(1)} CSV`}
        </button>
      </div>

      {/* KPI strip */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <KpiCard label="Total Delegates"   value={s.totalDelegates}    icon="🎟️" />
        <KpiCard label="Checked In"        value={`${s.checkedIn} (${s.totalDelegates ? Math.round(s.checkedIn / s.totalDelegates * 100) : 0}%)`} icon="✅" />
        <KpiCard label="Legacy Donations"  value={`R ${s.donationAmountZar.toLocaleString()}`} icon="💚" sub={`${s.totalDonations} donors`} />
        <KpiCard label="Accommodation"     value={s.accommodationBooked} icon="🏨" sub={`${s.nfcTaps} NFC taps`} />
      </div>

      {/* Tabs */}
      <div className="flex gap-2 flex-wrap">
        {(['delegates','checkin','donations','accommodation'] as ReportTab[]).map(t => (
          <button
            key={t}
            onClick={() => setTab(t)}
            className={`px-4 py-1.5 rounded-full text-sm font-semibold transition-colors ${
              tab === t
                ? 'bg-primary text-white'
                : 'bg-slate-800 text-slate-400 hover:bg-slate-700'
            }`}
          >
            {t.charAt(0).toUpperCase() + t.slice(1)}
          </button>
        ))}
      </div>

      {/* Breakdown panels */}
      {tab === 'delegates' && (
        <div className="grid md:grid-cols-2 gap-6">
          {/* By type */}
          <div className="bg-slate-800 rounded-xl p-5">
            <h3 className="text-white font-bold mb-4">By Delegate Type</h3>
            <div className="space-y-2">
              {s.byType.map(row => (
                <BarRow
                  key={row.type}
                  label={TYPE_LABELS[row.type] ?? row.type}
                  value={row.count}
                  max={s.totalDelegates}
                  color="#0D9488"
                />
              ))}
            </div>
          </div>

          {/* Top 10 countries */}
          <div className="bg-slate-800 rounded-xl p-5">
            <h3 className="text-white font-bold mb-4">Top Countries</h3>
            <div className="space-y-2">
              {s.byCountry.slice(0, 12).map(row => (
                <BarRow
                  key={row.country}
                  label={row.country}
                  value={row.count}
                  max={s.byCountry[0]?.count ?? 1}
                  color="#F59E0B"
                />
              ))}
            </div>
          </div>
        </div>
      )}

      {tab === 'donations' && (
        <div className="bg-slate-800 rounded-xl p-5">
          <h3 className="text-white font-bold mb-4">Donation Summary</h3>
          <div className="grid grid-cols-3 gap-4 mb-6">
            <KpiCard label="Total Raised"   value={`R ${s.donationAmountZar.toLocaleString()}`} icon="💰" />
            <KpiCard label="Donors"         value={s.totalDonations} icon="🙌" />
            <KpiCard label="Avg Donation"   value={s.totalDonations ? `R ${Math.round(s.donationAmountZar / s.totalDonations).toLocaleString()}` : 'R 0'} icon="📊" />
          </div>
          <div className="data-table overflow-auto max-h-96">
            <table className="w-full text-sm">
              <thead>
                <tr className="text-slate-400 text-left border-b border-slate-700">
                  <th className="pb-2 pr-4">Donor</th>
                  <th className="pb-2 pr-4">Email</th>
                  <th className="pb-2 pr-4">Amount</th>
                  <th className="pb-2 pr-4">Status</th>
                  <th className="pb-2">Date</th>
                </tr>
              </thead>
              <tbody>
                {donations.map(d => (
                  <tr key={d.id} className="border-b border-slate-700/50 hover:bg-slate-700/30">
                    <td className="py-2 pr-4 text-white font-medium">
                      {d.is_anonymous ? <span className="text-slate-500 italic">Anonymous</span> : d.donor_name}
                    </td>
                    <td className="py-2 pr-4 text-slate-300">{d.is_anonymous ? '—' : d.donor_email}</td>
                    <td className="py-2 pr-4 text-emerald-400 font-bold">R {d.amount_zar?.toLocaleString()}</td>
                    <td className="py-2 pr-4">
                      <span className={`badge-${d.payment_status === 'completed' ? 'success' : 'warning'}`}>{d.payment_status}</span>
                    </td>
                    <td className="py-2 text-slate-400 text-xs">{format(new Date(d.created_at), 'dd MMM yyyy')}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {tab === 'checkin' && (
        <div className="bg-slate-800 rounded-xl p-5">
          <h3 className="text-white font-bold mb-2">Check-In Progress</h3>
          <p className="text-slate-400 text-sm mb-5">
            {s.checkedIn} of {s.totalDelegates} delegates checked in
            ({s.totalDelegates ? Math.round(s.checkedIn / s.totalDelegates * 100) : 0}%)
          </p>
          <div className="w-full bg-slate-700 rounded-full h-3 mb-6">
            <div
              className="bg-primary h-3 rounded-full transition-all"
              style={{ width: `${s.totalDelegates ? (s.checkedIn / s.totalDelegates * 100) : 0}%` }}
            />
          </div>
          <div className="space-y-2">
            {s.byType.map(row => {
              const checkedInType = delegates.filter(d => d.delegate_type === row.type && d.checked_in).length
              return (
                <BarRow
                  key={row.type}
                  label={TYPE_LABELS[row.type] ?? row.type}
                  value={checkedInType}
                  max={row.count}
                  color="#0D9488"
                  suffix={`/ ${row.count}`}
                />
              )
            })}
          </div>
        </div>
      )}

      {tab === 'accommodation' && (
        <div className="bg-slate-800 rounded-xl p-5">
          <h3 className="text-white font-bold mb-4">Accommodation Bookings</h3>
          <div className="grid grid-cols-2 gap-4 mb-6">
            <KpiCard label="Confirmed Bookings" value={s.accommodationBooked} icon="🏨" />
            <KpiCard label="Delegates w/o Accom" value={s.totalDelegates - s.accommodationBooked} icon="🏃" />
          </div>
          <p className="text-slate-400 text-sm">
            Download the CSV above for a full breakdown including check-in / check-out dates and hotel details.
          </p>
        </div>
      )}
    </div>
  )
}

/* ── helpers ── */

function KpiCard({ label, value, icon, sub }: { label: string; value: string | number; icon: string; sub?: string }) {
  return (
    <div className="stat-card">
      <div className="flex items-center gap-2 mb-1">
        <span className="text-xl">{icon}</span>
        <span className="text-slate-400 text-xs font-medium uppercase tracking-wider">{label}</span>
      </div>
      <div className="text-2xl font-bold text-white">{value}</div>
      {sub && <div className="text-xs text-slate-500 mt-0.5">{sub}</div>}
    </div>
  )
}

function BarRow({ label, value, max, color, suffix }: {
  label: string; value: number; max: number; color: string; suffix?: string
}) {
  const pct = max > 0 ? Math.round((value / max) * 100) : 0
  return (
    <div className="flex items-center gap-3">
      <span className="text-slate-300 text-sm w-36 shrink-0 truncate">{label}</span>
      <div className="flex-1 bg-slate-700 rounded-full h-2">
        <div className="h-2 rounded-full transition-all" style={{ width: `${pct}%`, background: color }} />
      </div>
      <span className="text-slate-400 text-xs w-16 text-right shrink-0">
        {value}{suffix ?? ''}
      </span>
    </div>
  )
}
