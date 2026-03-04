import { Users, CheckSquare, TrendingUp, Building2, Wifi, Zap } from 'lucide-react'
import { useRealtimeDelegates } from '@/hooks/useRealtimeDelegates'

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

const TYPE_COLOURS: Record<string, string> = {
  government:     'bg-blue-500',
  utility:        'bg-primary',
  private_sector: 'bg-purple-500',
  ngo:            'bg-green-500',
  academic:       'bg-amber-500',
  media:          'bg-pink-500',
  exhibitor:      'bg-orange-500',
  sponsor:        'bg-indigo-500',
}

function StatCard({
  label, value, sub, icon: Icon, colour,
}: {
  label:  string
  value:  string | number
  sub?:   string
  icon:   React.ElementType
  colour: string
}) {
  return (
    <div className="stat-card flex items-center gap-4">
      <div className={`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${colour}`}>
        <Icon size={22} className="text-white" />
      </div>
      <div>
        <div className="text-2xl font-heading font-bold text-white">{value}</div>
        <div className="text-sm text-slate-400 font-medium">{label}</div>
        {sub && <div className="text-xs text-slate-500 mt-0.5">{sub}</div>}
      </div>
    </div>
  )
}

function Skeleton() {
  return (
    <div className="stat-card">
      <div className="flex items-center gap-4">
        <div className="skeleton w-12 h-12 rounded-xl" />
        <div className="space-y-2">
          <div className="skeleton h-7 w-20 rounded" />
          <div className="skeleton h-4 w-32 rounded" />
        </div>
      </div>
    </div>
  )
}

export default function DashboardPage() {
  const { stats, loading } = useRealtimeDelegates()

  const checkinRate = stats
    ? Math.round((stats.checked_in / Math.max(stats.total_delegates, 1)) * 100)
    : 0

  return (
    <div className="space-y-8 max-w-7xl">
      <div>
        <h1 className="text-2xl font-heading font-bold text-white">Dashboard</h1>
        <p className="text-slate-400 text-sm mt-1">
          Real-time overview · AWSISA Watersan Dialogue 2026
        </p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {loading ? (
          Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} />)
        ) : (
          <>
            <StatCard
              label="Total Delegates"
              value={stats?.total_delegates.toLocaleString() ?? 0}
              icon={Users}
              colour="bg-primary"
            />
            <StatCard
              label="Checked In"
              value={stats?.checked_in.toLocaleString() ?? 0}
              sub={`${checkinRate}% of registered`}
              icon={CheckSquare}
              colour="bg-green-600"
            />
            <StatCard
              label="Legacy Donations"
              value={`R ${(stats?.total_donations_zar ?? 0).toLocaleString('en-ZA')}`}
              icon={TrendingUp}
              colour="bg-legacy"
            />
            <StatCard
              label="Accommodation Booked"
              value={stats?.total_bookings ?? 0}
              icon={Building2}
              colour="bg-purple-600"
            />
          </>
        )}
      </div>

      {/* Check-in progress bar */}
      {!loading && stats && (
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
          <div className="flex items-center justify-between mb-3">
            <span className="text-sm font-semibold text-slate-300">Check-In Progress</span>
            <span className="text-sm text-slate-400">
              {stats.checked_in.toLocaleString()} / {stats.total_delegates.toLocaleString()}
            </span>
          </div>
          <div className="h-3 bg-slate-800 rounded-full overflow-hidden">
            <div
              className="h-full bg-gradient-to-r from-primary to-primary-light rounded-full transition-all duration-500"
              style={{ width: `${checkinRate}%` }}
              role="progressbar"
              aria-valuenow={checkinRate}
              aria-valuemin={0}
              aria-valuemax={100}
              aria-label={`${checkinRate}% checked in`}
            />
          </div>
          <div className="flex justify-between mt-2 text-xs text-slate-500">
            <span>0</span>
            <span className="font-semibold text-primary-light">{checkinRate}%</span>
            <span>{stats.total_delegates.toLocaleString()}</span>
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {/* Delegates by type */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
          <h2 className="text-base font-semibold text-white mb-4">Delegates by Type</h2>
          {loading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="flex items-center gap-3">
                  <div className="skeleton h-4 w-32 rounded" />
                  <div className="skeleton h-4 flex-1 rounded" />
                  <div className="skeleton h-4 w-10 rounded" />
                </div>
              ))}
            </div>
          ) : stats?.total_delegates ? (
            <div className="space-y-3">
              {Object.entries(stats.by_type)
                .sort(([, a], [, b]) => b - a)
                .map(([type, count]) => {
                  const pct = Math.round((count / stats.total_delegates) * 100)
                  return (
                    <div key={type} className="flex items-center gap-3">
                      <div className="text-xs text-slate-400 w-32 flex-shrink-0 truncate">
                        {TYPE_LABELS[type] ?? type}
                      </div>
                      <div className="flex-1 h-2.5 bg-slate-800 rounded-full overflow-hidden">
                        <div
                          className={`h-full rounded-full ${TYPE_COLOURS[type] ?? 'bg-slate-500'}`}
                          style={{ width: `${pct}%` }}
                        />
                      </div>
                      <div className="text-xs font-semibold text-slate-300 w-12 text-right">
                        {count.toLocaleString()}
                      </div>
                    </div>
                  )
                })
              }
            </div>
          ) : (
            <p className="text-slate-500 text-sm">No delegates registered yet.</p>
          )}
        </div>

        {/* Top countries */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
          <h2 className="text-base font-semibold text-white mb-4">Top Countries</h2>
          {loading ? (
            <div className="space-y-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <div key={i} className="flex items-center gap-3">
                  <div className="skeleton h-4 w-32 rounded" />
                  <div className="skeleton h-4 flex-1 rounded" />
                  <div className="skeleton h-4 w-10 rounded" />
                </div>
              ))}
            </div>
          ) : stats?.by_country.length ? (
            <div className="space-y-3">
              {stats.by_country.map(({ country, count }) => {
                const pct = Math.round((count / stats.total_delegates) * 100)
                return (
                  <div key={country} className="flex items-center gap-3">
                    <div className="text-xs text-slate-400 w-32 flex-shrink-0 truncate">{country}</div>
                    <div className="flex-1 h-2.5 bg-slate-800 rounded-full overflow-hidden">
                      <div className="h-full bg-primary/70 rounded-full" style={{ width: `${pct}%` }} />
                    </div>
                    <div className="text-xs font-semibold text-slate-300 w-12 text-right">
                      {count.toLocaleString()}
                    </div>
                  </div>
                )
              })}
            </div>
          ) : (
            <p className="text-slate-500 text-sm">No delegates registered yet.</p>
          )}
        </div>
      </div>

      {/* NFC stats quick cards */}
      {!loading && stats && (
        <div className="grid grid-cols-2 gap-4">
          <div className="stat-card flex items-center gap-3">
            <div className="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
              <Wifi size={18} className="text-indigo-400" />
            </div>
            <div>
              <div className="text-xl font-heading font-bold text-white">{stats.nfc_taps.toLocaleString()}</div>
              <div className="text-xs text-slate-400">NFC Taps / Networking Events</div>
            </div>
          </div>
          <div className="stat-card flex items-center gap-3">
            <div className="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
              <Zap size={18} className="text-amber-400" />
            </div>
            <div>
              <div className="text-xl font-heading font-bold text-white">–</div>
              <div className="text-xs text-slate-400">Flash Alerts Sent Today</div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
