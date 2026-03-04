import { useState, useEffect, useCallback } from 'react'
import { supabase } from '@/lib/supabase'
import type { Delegate, DelegateType } from '@/types'
import { Search, UserCheck, UserX, ChevronLeft, ChevronRight, X } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'

const TYPE_LABELS: Record<string, string> = {
  government: 'Government', utility: 'Utility', private_sector: 'Private Sector',
  ngo: 'NGO', academic: 'Academic', media: 'Media', exhibitor: 'Exhibitor', sponsor: 'Sponsor',
}

const PAGE_SIZE = 50

export default function DelegatesPage() {
  const [delegates, setDelegates]       = useState<Delegate[]>([])
  const [total, setTotal]               = useState(0)
  const [page, setPage]                 = useState(0)
  const [search, setSearch]             = useState('')
  const [typeFilter, setTypeFilter]     = useState<DelegateType | ''>('')
  const [checkinFilter, setCheckinFilter] = useState<'' | 'true' | 'false'>('')
  const [loading, setLoading]           = useState(true)
  const [selected, setSelected]         = useState<Delegate | null>(null)

  const fetchDelegates = useCallback(async () => {
    setLoading(true)

    let query = supabase
      .from('delegates')
      .select('*', { count: 'exact' })
      .order('created_at', { ascending: false })
      .range(page * PAGE_SIZE, (page + 1) * PAGE_SIZE - 1)

    if (search.trim()) {
      query = query.or(
        `first_name.ilike.%${search}%,last_name.ilike.%${search}%,email.ilike.%${search}%,organisation.ilike.%${search}%`
      )
    }
    if (typeFilter)     query = query.eq('delegate_type', typeFilter)
    if (checkinFilter)  query = query.eq('checked_in', checkinFilter === 'true')

    const { data, count, error } = await query
    if (!error) {
      setDelegates(data as Delegate[])
      setTotal(count ?? 0)
    }
    setLoading(false)
  }, [page, search, typeFilter, checkinFilter])

  useEffect(() => { fetchDelegates() }, [fetchDelegates])

  // Reset page on filter change.
  useEffect(() => { setPage(0) }, [search, typeFilter, checkinFilter])

  const totalPages = Math.ceil(total / PAGE_SIZE)

  return (
    <div className="space-y-6 max-w-7xl">
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="text-2xl font-heading font-bold text-white">Delegates</h1>
          <p className="text-slate-400 text-sm mt-1">{total.toLocaleString()} registered delegates</p>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-slate-900 border border-slate-800 rounded-xl p-4 flex flex-wrap gap-3">
        <div className="relative flex-1 min-w-64">
          <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" />
          <input
            type="search"
            className="input pl-9"
            placeholder="Search name, email, organisation…"
            value={search}
            onChange={e => setSearch(e.target.value)}
            aria-label="Search delegates"
          />
        </div>

        <select
          className="input w-auto"
          value={typeFilter}
          onChange={e => setTypeFilter(e.target.value as DelegateType | '')}
          aria-label="Filter by delegate type"
        >
          <option value="">All Types</option>
          {Object.entries(TYPE_LABELS).map(([v, l]) => (
            <option key={v} value={v}>{l}</option>
          ))}
        </select>

        <select
          className="input w-auto"
          value={checkinFilter}
          onChange={e => setCheckinFilter(e.target.value as '' | 'true' | 'false')}
          aria-label="Filter by check-in status"
        >
          <option value="">All Check-In</option>
          <option value="true">Checked In</option>
          <option value="false">Not Checked In</option>
        </select>
      </div>

      {/* Table */}
      <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="data-table" aria-label="Delegate list">
            <thead>
              <tr>
                <th>Name</th>
                <th>Organisation</th>
                <th>Country</th>
                <th>Type</th>
                <th>Status</th>
                <th>Check-In</th>
                <th>Registered</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                Array.from({ length: 8 }).map((_, i) => (
                  <tr key={i}>
                    {Array.from({ length: 7 }).map((_, j) => (
                      <td key={j}><div className="skeleton h-4 rounded w-24" /></td>
                    ))}
                  </tr>
                ))
              ) : delegates.length === 0 ? (
                <tr>
                  <td colSpan={7} className="text-center py-12 text-slate-500">
                    {search || typeFilter || checkinFilter ? 'No delegates match your filters.' : 'No delegates registered yet.'}
                  </td>
                </tr>
              ) : (
                delegates.map(d => (
                  <tr
                    key={d.id}
                    className="cursor-pointer"
                    onClick={() => setSelected(d)}
                  >
                    <td>
                      <div className="font-semibold text-white">
                        {d.first_name} {d.last_name}
                      </div>
                      <div className="text-slate-500 text-xs">{d.email}</div>
                    </td>
                    <td className="text-slate-300">{d.organisation ?? '—'}</td>
                    <td className="text-slate-300">{d.country}</td>
                    <td>
                      <span className="badge badge-primary">
                        {TYPE_LABELS[d.delegate_type] ?? d.delegate_type}
                      </span>
                    </td>
                    <td>
                      <span className={`badge ${
                        d.registration_status === 'confirmed' ? 'badge-success' :
                        d.registration_status === 'cancelled' ? 'badge-error' : 'badge-warning'
                      }`}>
                        {d.registration_status}
                      </span>
                    </td>
                    <td>
                      {d.checked_in
                        ? <UserCheck size={18} className="text-green-400" aria-label="Checked in" />
                        : <UserX size={18} className="text-slate-600" aria-label="Not checked in" />
                      }
                    </td>
                    <td className="text-slate-500 text-xs">
                      {formatDistanceToNow(new Date(d.created_at), { addSuffix: true })}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="px-5 py-4 border-t border-slate-800 flex items-center justify-between">
            <span className="text-slate-500 text-sm">
              Page {page + 1} of {totalPages} · {total.toLocaleString()} total
            </span>
            <div className="flex gap-2">
              <button
                onClick={() => setPage(p => Math.max(0, p - 1))}
                disabled={page === 0}
                className="btn-ghost py-1.5 px-3"
                aria-label="Previous page"
              >
                <ChevronLeft size={16} />
              </button>
              <button
                onClick={() => setPage(p => Math.min(totalPages - 1, p + 1))}
                disabled={page >= totalPages - 1}
                className="btn-ghost py-1.5 px-3"
                aria-label="Next page"
              >
                <ChevronRight size={16} />
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Delegate detail panel */}
      {selected && (
        <div
          className="fixed inset-0 z-50 flex"
          role="dialog"
          aria-modal="true"
          aria-label={`${selected.first_name} ${selected.last_name} details`}
        >
          <div className="absolute inset-0 bg-black/70" onClick={() => setSelected(null)} aria-hidden="true" />
          <div className="absolute right-0 top-0 bottom-0 w-full max-w-md bg-slate-900 border-l border-slate-800 overflow-y-auto p-6 animate-slide-in">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-heading font-bold text-white">Delegate Profile</h2>
              <button
                onClick={() => setSelected(null)}
                className="text-slate-400 hover:text-white"
                aria-label="Close panel"
              >
                <X size={20} />
              </button>
            </div>

            {/* Avatar placeholder */}
            <div className="flex items-center gap-4 mb-6">
              <div className="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center flex-shrink-0">
                <span className="text-primary-light text-2xl font-bold">
                  {selected.first_name.charAt(0)}{selected.last_name.charAt(0)}
                </span>
              </div>
              <div>
                <div className="text-xl font-heading font-bold text-white">
                  {selected.first_name} {selected.last_name}
                </div>
                <div className="text-slate-400 text-sm">{selected.job_title}</div>
                <span className="badge badge-primary mt-1">
                  {TYPE_LABELS[selected.delegate_type] ?? selected.delegate_type}
                </span>
              </div>
            </div>

            <dl className="space-y-3 text-sm">
              {[
                ['Organisation', selected.organisation],
                ['Country', selected.country],
                ['Email', selected.email],
                ['Phone', selected.phone],
                ['Registration', selected.registration_status],
                ['Payment', selected.payment_status],
                ['Registered', formatDistanceToNow(new Date(selected.created_at), { addSuffix: true })],
                ['Dietary', selected.dietary_requirements],
                ['Accessibility', selected.accessibility_needs],
              ].map(([label, value]) => value ? (
                <div key={label} className="flex gap-3">
                  <dt className="text-slate-500 w-28 flex-shrink-0">{label}</dt>
                  <dd className="text-slate-200 flex-1">{value}</dd>
                </div>
              ) : null)}
            </dl>

            {/* Check-in status */}
            <div className={`mt-6 p-4 rounded-xl border ${
              selected.checked_in
                ? 'bg-green-500/10 border-green-500/30'
                : 'bg-slate-800 border-slate-700'
            }`}>
              <div className="flex items-center gap-3">
                {selected.checked_in
                  ? <UserCheck size={20} className="text-green-400" />
                  : <UserX size={20} className="text-slate-500" />
                }
                <div>
                  <div className={`font-semibold ${selected.checked_in ? 'text-green-400' : 'text-slate-400'}`}>
                    {selected.checked_in ? 'Checked In' : 'Not Checked In'}
                  </div>
                  {selected.checked_in_at && (
                    <div className="text-xs text-slate-500">
                      {formatDistanceToNow(new Date(selected.checked_in_at), { addSuffix: true })}
                    </div>
                  )}
                </div>
              </div>
            </div>

            {/* QR token (for manual entry) */}
            <div className="mt-4 p-3 bg-slate-800 rounded-lg">
              <div className="text-xs text-slate-500 mb-1">QR Token</div>
              <div className="text-xs font-mono text-slate-300 break-all">{selected.qr_code_token}</div>
            </div>

            {selected.notes && (
              <div className="mt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                <div className="text-xs text-amber-400 font-semibold mb-1">Staff Notes</div>
                <div className="text-sm text-slate-300">{selected.notes}</div>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  )
}
