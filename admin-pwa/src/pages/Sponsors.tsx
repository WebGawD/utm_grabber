import { useState, useEffect } from 'react'
import { supabase } from '@/lib/supabase'
import type { Sponsor, NfcTap } from '@/types'
import { Wifi, ExternalLink } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'

const TIER_ORDER = ['platinum', 'gold', 'silver', 'bronze', 'exhibitor', 'partner']
const TIER_COLOURS: Record<string, string> = {
  platinum: 'badge-neutral', gold: 'badge-warning', silver: 'badge-neutral',
  bronze: 'badge-error', exhibitor: 'badge-primary', partner: 'badge-primary',
}

export default function SponsorsPage() {
  const [sponsors, setSponsors] = useState<Sponsor[]>([])
  const [taps, setTaps]         = useState<NfcTap[]>([])
  const [loading, setLoading]   = useState(true)

  useEffect(() => {
    async function load() {
      const [sponsorsRes, tapsRes] = await Promise.all([
        supabase.from('sponsors').select('*').order('sort_order'),
        supabase.from('nfc_taps').select('*').eq('tap_type', 'booth_tap').order('created_at', { ascending: false }).limit(200),
      ])
      setSponsors((sponsorsRes.data ?? []) as Sponsor[])
      setTaps((tapsRes.data ?? []) as NfcTap[])
      setLoading(false)
    }
    load()
  }, [])

  // Taps per sponsor.
  const tapsBySponsor = taps.reduce<Record<string, number>>((acc, t) => {
    if (t.sponsor_id) acc[t.sponsor_id] = (acc[t.sponsor_id] ?? 0) + 1
    return acc
  }, {})

  const sortedSponsors = [...sponsors].sort((a, b) =>
    TIER_ORDER.indexOf(a.tier) - TIER_ORDER.indexOf(b.tier)
  )

  const recentTaps = taps.slice(0, 20)

  return (
    <div className="space-y-6 max-w-7xl">
      <div>
        <h1 className="text-2xl font-heading font-bold text-white">Sponsor Lead Dashboard</h1>
        <p className="text-slate-400 text-sm mt-1">NFC booth tap analytics and sponsor management</p>
      </div>

      {/* Summary stat */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div className="stat-card">
          <div className="text-2xl font-heading font-bold text-white">{sponsors.length}</div>
          <div className="text-sm text-slate-400">Total Sponsors</div>
        </div>
        <div className="stat-card">
          <div className="text-2xl font-heading font-bold text-white">{taps.length.toLocaleString()}</div>
          <div className="text-sm text-slate-400">Booth Taps</div>
        </div>
        <div className="stat-card">
          <div className="text-2xl font-heading font-bold text-white">
            {taps.filter(t => {
              const h = new Date(t.created_at)
              const now = new Date()
              return h.toDateString() === now.toDateString()
            }).length}
          </div>
          <div className="text-sm text-slate-400">Taps Today</div>
        </div>
        <div className="stat-card">
          <div className="text-2xl font-heading font-bold text-white">
            {sponsors.filter(s => s.booth_number).length}
          </div>
          <div className="text-sm text-slate-400">Active Booths</div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {/* Sponsor table */}
        <div className="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
          <div className="px-5 py-4 border-b border-slate-800">
            <h2 className="text-base font-semibold text-white">Sponsor Booth Performance</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="data-table" aria-label="Sponsor lead analytics">
              <thead>
                <tr>
                  <th>Sponsor</th>
                  <th>Tier</th>
                  <th>Booth</th>
                  <th>NFC Taps</th>
                  <th>Link</th>
                </tr>
              </thead>
              <tbody>
                {loading
                  ? Array.from({ length: 6 }).map((_, i) => (
                    <tr key={i}>
                      {Array.from({ length: 5 }).map((_, j) => (
                        <td key={j}><div className="skeleton h-4 rounded" /></td>
                      ))}
                    </tr>
                  ))
                  : sortedSponsors.map(sponsor => (
                    <tr key={sponsor.id}>
                      <td>
                        <div className="font-semibold text-white">{sponsor.name}</div>
                        {sponsor.booth_nfc_slug && (
                          <div className="text-xs text-slate-500">/booth/{sponsor.booth_nfc_slug}</div>
                        )}
                      </td>
                      <td>
                        <span className={`badge ${TIER_COLOURS[sponsor.tier] ?? 'badge-neutral'} capitalize`}>
                          {sponsor.tier}
                        </span>
                      </td>
                      <td className="text-slate-300">{sponsor.booth_number ?? '—'}</td>
                      <td>
                        <div className="flex items-center gap-2">
                          <Wifi size={14} className="text-primary-light" />
                          <span className="font-semibold text-white">
                            {(tapsBySponsor[sponsor.id] ?? 0).toLocaleString()}
                          </span>
                        </div>
                      </td>
                      <td>
                        {sponsor.website_url && (
                          <a
                            href={sponsor.website_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-primary-light hover:text-white transition-colors"
                            aria-label={`Visit ${sponsor.name} website`}
                          >
                            <ExternalLink size={14} />
                          </a>
                        )}
                      </td>
                    </tr>
                  ))
                }
              </tbody>
            </table>
          </div>
        </div>

        {/* Recent taps feed */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
          <div className="px-5 py-4 border-b border-slate-800">
            <h2 className="text-base font-semibold text-white">Live Tap Feed</h2>
          </div>
          {loading ? (
            <div className="p-5 space-y-3">
              {Array.from({ length: 6 }).map((_, i) => <div key={i} className="skeleton h-12 rounded-lg" />)}
            </div>
          ) : recentTaps.length === 0 ? (
            <div className="px-5 py-10 text-center text-slate-600 text-sm">No taps recorded yet.</div>
          ) : (
            <ul className="divide-y divide-slate-800 max-h-[480px] overflow-y-auto">
              {recentTaps.map(tap => {
                const sponsor = sponsors.find(s => s.id === tap.sponsor_id)
                return (
                  <li key={tap.id} className="px-5 py-3 flex items-center gap-3">
                    <div className="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                      <Wifi size={14} className="text-primary-light" />
                    </div>
                    <div className="flex-1 min-w-0">
                      <div className="text-sm font-semibold text-white truncate">
                        {sponsor?.name ?? 'Unknown Booth'}
                      </div>
                      <div className="text-xs text-slate-500">
                        {formatDistanceToNow(new Date(tap.created_at), { addSuffix: true })}
                      </div>
                    </div>
                  </li>
                )
              })}
            </ul>
          )}
        </div>
      </div>
    </div>
  )
}
