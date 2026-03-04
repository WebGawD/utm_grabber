import { useState, useEffect } from 'react'
import { supabase } from '@/lib/supabase'
import type { FlashAlert } from '@/types'
import { Send, Clock, CheckCircle, AlertCircle, Bell } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'
import toast from 'react-hot-toast'

const AUDIENCE_LABELS: Record<string, string> = {
  all:            'All Delegates',
  checked_in:     'Checked-In Only',
  government:     'Government',
  utility:        'Utility',
  private_sector: 'Private Sector',
  ngo:            'NGO / NPO',
  academic:       'Academic',
  media:          'Media',
}

type AlertAudience = FlashAlert['audience']

export default function AlertsPage() {
  const [alerts, setAlerts] = useState<FlashAlert[]>([])
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  // Composer form state.
  const [title,    setTitle]    = useState('')
  const [body,     setBody]     = useState('')
  const [audience, setAudience] = useState<AlertAudience>('all')
  const [channels, setChannels] = useState({ push: true, sms: false })

  async function fetchAlerts() {
    const { data } = await supabase
      .from('flash_alerts')
      .select('*')
      .order('created_at', { ascending: false })
      .limit(50)
    setAlerts((data ?? []) as FlashAlert[])
    setLoading(false)
  }

  useEffect(() => { fetchAlerts() }, [])

  async function handleSend(asDraft: boolean) {
    if (!title.trim() || !body.trim()) {
      toast.error('Title and message are required.')
      return
    }
    setSubmitting(true)

    const selectedChannels = Object.entries(channels)
      .filter(([, v]) => v)
      .map(([k]) => k)

    const { data, error } = await supabase
      .from('flash_alerts')
      .insert({
        title,
        body,
        audience,
        channels: selectedChannels,
        status:   asDraft ? 'draft' : 'sending',
      })
      .select()
      .single()

    if (error) {
      toast.error(`Failed to create alert: ${error.message}`)
    } else {
      toast.success(asDraft ? 'Alert saved as draft.' : 'Alert sent!')
      setTitle('')
      setBody('')
      setAudience('all')
      setChannels({ push: true, sms: false })
      setAlerts(prev => [data as FlashAlert, ...prev])
    }
    setSubmitting(false)
  }

  function statusIcon(status: FlashAlert['status']) {
    switch (status) {
      case 'sent':    return <CheckCircle size={14} className="text-green-400" />
      case 'sending': return <Clock size={14} className="text-amber-400 animate-spin" />
      case 'failed':  return <AlertCircle size={14} className="text-red-400" />
      default:        return <Clock size={14} className="text-slate-500" />
    }
  }

  const bodyChars = body.length
  const MAX_CHARS  = 160 // SMS limit

  return (
    <div className="space-y-6 max-w-5xl">
      <div>
        <h1 className="text-2xl font-heading font-bold text-white">Flash Alerts</h1>
        <p className="text-slate-400 text-sm mt-1">Send instant notifications to delegates</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {/* Composer */}
        <div className="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
          <h2 className="text-base font-semibold text-white flex items-center gap-2">
            <Bell size={18} className="text-primary-light" />
            Compose Alert
          </h2>

          <div>
            <label className="label" htmlFor="alert-title">Title</label>
            <input
              id="alert-title"
              type="text"
              className="input"
              placeholder="e.g. Session change — Room B moved to Hall 3"
              value={title}
              onChange={e => setTitle(e.target.value)}
              maxLength={100}
            />
          </div>

          <div>
            <div className="flex justify-between items-center mb-1.5">
              <label className="label" htmlFor="alert-body">Message</label>
              <span className={`text-xs ${bodyChars > MAX_CHARS ? 'text-red-400' : 'text-slate-500'}`}>
                {bodyChars}/{MAX_CHARS}
              </span>
            </div>
            <textarea
              id="alert-body"
              className="input min-h-[120px] resize-y"
              placeholder="Your message to delegates…"
              value={body}
              onChange={e => setBody(e.target.value)}
              maxLength={500}
            />
            {bodyChars > MAX_CHARS && (
              <p className="text-xs text-amber-400 mt-1">
                ⚠ Message exceeds SMS limit ({MAX_CHARS} chars). SMS may be truncated.
              </p>
            )}
          </div>

          <div>
            <label className="label" htmlFor="alert-audience">Audience</label>
            <select
              id="alert-audience"
              className="input"
              value={audience}
              onChange={e => setAudience(e.target.value as AlertAudience)}
            >
              {Object.entries(AUDIENCE_LABELS).map(([v, l]) => (
                <option key={v} value={v}>{l}</option>
              ))}
            </select>
          </div>

          <div>
            <div className="label mb-2">Delivery Channels</div>
            <div className="flex gap-4">
              {(['push', 'sms'] as const).map(ch => (
                <label key={ch} className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={channels[ch]}
                    onChange={e => setChannels(prev => ({ ...prev, [ch]: e.target.checked }))}
                    className="w-4 h-4 accent-teal-500"
                  />
                  <span className="text-sm text-slate-300 capitalize">
                    {ch === 'push' ? 'Push Notification' : 'SMS'}
                  </span>
                </label>
              ))}
            </div>
            <p className="text-xs text-slate-500 mt-1.5">
              SMS requires Africa's Talking API credentials configured server-side.
            </p>
          </div>

          <div className="flex gap-3 pt-2">
            <button
              onClick={() => handleSend(false)}
              disabled={submitting || !title || !body}
              className="btn-primary flex-1 justify-center"
            >
              {submitting
                ? <span className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                : <Send size={16} />
              }
              Send Now
            </button>
            <button
              onClick={() => handleSend(true)}
              disabled={submitting || !title || !body}
              className="btn-secondary"
            >
              Save Draft
            </button>
          </div>
        </div>

        {/* Alert history */}
        <div className="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
          <div className="px-5 py-4 border-b border-slate-800">
            <h2 className="text-base font-semibold text-white">Sent Alerts</h2>
          </div>
          {loading ? (
            <div className="p-5 space-y-3">
              {Array.from({ length: 4 }).map((_, i) => (
                <div key={i} className="skeleton h-16 rounded-lg" />
              ))}
            </div>
          ) : alerts.length === 0 ? (
            <div className="px-5 py-10 text-center text-slate-600 text-sm">No alerts sent yet.</div>
          ) : (
            <ul className="divide-y divide-slate-800 max-h-[480px] overflow-y-auto">
              {alerts.map(alert => (
                <li key={alert.id} className="px-5 py-4">
                  <div className="flex items-start gap-2">
                    {statusIcon(alert.status)}
                    <div className="flex-1 min-w-0">
                      <div className="text-sm font-semibold text-white truncate">{alert.title}</div>
                      <div className="text-xs text-slate-500 truncate mt-0.5">{alert.body}</div>
                      <div className="flex items-center gap-2 mt-1.5">
                        <span className="badge badge-neutral text-xs">{AUDIENCE_LABELS[alert.audience] ?? alert.audience}</span>
                        <span className="text-xs text-slate-600">
                          {formatDistanceToNow(new Date(alert.created_at), { addSuffix: true })}
                        </span>
                      </div>
                      {alert.sent_count > 0 && (
                        <div className="text-xs text-slate-500 mt-0.5">
                          {alert.sent_count.toLocaleString()} recipients
                        </div>
                      )}
                    </div>
                  </div>
                </li>
              ))}
            </ul>
          )}
        </div>
      </div>
    </div>
  )
}
