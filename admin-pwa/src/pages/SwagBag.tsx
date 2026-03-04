import { useState, useEffect, useRef } from 'react'
import { supabase } from '@/lib/supabase'
import type { SwagBagItem, Sponsor } from '@/types'
import { Upload, FileText, Trash2, Eye, EyeOff } from 'lucide-react'
import toast from 'react-hot-toast'

export default function SwagBagPage() {
  const [items, setItems]       = useState<SwagBagItem[]>([])
  const [sponsors, setSponsors] = useState<Sponsor[]>([])
  const [loading, setLoading]   = useState(true)
  const [uploading, setUploading] = useState(false)
  const fileInputRef            = useRef<HTMLInputElement>(null)

  // Upload form state.
  const [sponsorId, setSponsorId] = useState('')
  const [title, setTitle]         = useState('')
  const [description, setDesc]    = useState('')

  async function fetchItems() {
    const [itemsRes, sponsorsRes] = await Promise.all([
      supabase
        .from('swag_bag_items')
        .select('*, sponsor:sponsors(name, logo_url, tier)')
        .order('created_at', { ascending: false }),
      supabase.from('sponsors').select('id, name, tier').eq('is_active', true).order('sort_order'),
    ])
    setItems((itemsRes.data ?? []) as SwagBagItem[])
    setSponsors((sponsorsRes.data ?? []) as Sponsor[])
    setLoading(false)
  }

  useEffect(() => { fetchItems() }, [])

  async function handleUpload(e: React.FormEvent) {
    e.preventDefault()
    if (!fileInputRef.current?.files?.[0]) {
      toast.error('Please select a file.')
      return
    }
    if (!sponsorId || !title.trim()) {
      toast.error('Sponsor and title are required.')
      return
    }

    const file     = fileInputRef.current.files[0]
    const filePath = `sponsors/${sponsorId}/${Date.now()}_${file.name}`

    setUploading(true)

    // Upload to Supabase Storage.
    const { error: uploadError } = await supabase.storage
      .from('swag-bag')
      .upload(filePath, file, { contentType: file.type })

    if (uploadError) {
      toast.error(`Upload failed: ${uploadError.message}`)
      setUploading(false)
      return
    }

    const { data: { publicUrl } } = supabase.storage
      .from('swag-bag')
      .getPublicUrl(filePath)

    // Insert metadata record.
    const { error: insertError } = await supabase.from('swag_bag_items').insert({
      sponsor_id:   sponsorId,
      title:        title.trim(),
      description:  description.trim() || null,
      file_url:     publicUrl,
      file_size_kb: Math.round(file.size / 1024),
      file_type:    file.name.split('.').pop()?.toLowerCase() ?? 'pdf',
      is_active:    true,
    })

    if (insertError) {
      toast.error(`Record failed: ${insertError.message}`)
    } else {
      toast.success('Brochure uploaded successfully!')
      setSponsorId('')
      setTitle('')
      setDesc('')
      if (fileInputRef.current) fileInputRef.current.value = ''
      await fetchItems()
    }
    setUploading(false)
  }

  async function toggleVisibility(item: SwagBagItem) {
    const { error } = await supabase
      .from('swag_bag_items')
      .update({ is_active: !item.is_active })
      .eq('id', item.id)

    if (error) {
      toast.error(error.message)
    } else {
      setItems(prev => prev.map(i => i.id === item.id ? { ...i, is_active: !i.is_active } : i))
    }
  }

  async function deleteItem(item: SwagBagItem) {
    if (!confirm(`Delete "${item.title}"? This cannot be undone.`)) return

    const { error } = await supabase
      .from('swag_bag_items')
      .delete()
      .eq('id', item.id)

    if (error) {
      toast.error(error.message)
    } else {
      toast.success('Item deleted.')
      setItems(prev => prev.filter(i => i.id !== item.id))
    }
  }

  return (
    <div className="space-y-6 max-w-5xl">
      <div>
        <h1 className="text-2xl font-heading font-bold text-white">Digital Swag Bag</h1>
        <p className="text-slate-400 text-sm mt-1">Manage sponsor brochures and downloadable materials</p>
      </div>

      {/* Upload form */}
      <div className="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 className="text-base font-semibold text-white mb-4 flex items-center gap-2">
          <Upload size={18} className="text-primary-light" />
          Upload New Brochure
        </h2>

        <form onSubmit={handleUpload} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="label" htmlFor="sponsor-select">Sponsor</label>
              <select
                id="sponsor-select"
                className="input"
                value={sponsorId}
                onChange={e => setSponsorId(e.target.value)}
                required
              >
                <option value="">Select sponsor…</option>
                {sponsors.map(s => (
                  <option key={s.id} value={s.id}>{s.name} ({s.tier})</option>
                ))}
              </select>
            </div>
            <div>
              <label className="label" htmlFor="item-title">Title</label>
              <input
                id="item-title"
                type="text"
                className="input"
                placeholder="e.g. Product Catalogue 2026"
                value={title}
                onChange={e => setTitle(e.target.value)}
                required
              />
            </div>
          </div>

          <div>
            <label className="label" htmlFor="item-desc">Description (optional)</label>
            <textarea
              id="item-desc"
              className="input min-h-[80px]"
              placeholder="Brief description of the content…"
              value={description}
              onChange={e => setDesc(e.target.value)}
            />
          </div>

          <div>
            <label className="label" htmlFor="file-input">File (PDF, PNG, or JPEG)</label>
            <input
              id="file-input"
              type="file"
              ref={fileInputRef}
              accept=".pdf,.png,.jpg,.jpeg"
              className="block w-full text-sm text-slate-400
                file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                file:bg-slate-700 file:text-slate-200 file:font-semibold
                hover:file:bg-slate-600 file:transition-colors cursor-pointer"
              required
            />
          </div>

          <div className="flex justify-end">
            <button type="submit" disabled={uploading} className="btn-primary">
              {uploading
                ? <span className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                : <Upload size={16} />
              }
              {uploading ? 'Uploading…' : 'Upload Brochure'}
            </button>
          </div>
        </form>
      </div>

      {/* Items list */}
      <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
          <h2 className="text-base font-semibold text-white">
            All Brochures ({items.length})
          </h2>
        </div>

        {loading ? (
          <div className="p-5 space-y-3">
            {Array.from({ length: 4 }).map((_, i) => <div key={i} className="skeleton h-16 rounded-lg" />)}
          </div>
        ) : items.length === 0 ? (
          <div className="px-5 py-12 text-center text-slate-600 text-sm">
            No brochures uploaded yet.
          </div>
        ) : (
          <ul className="divide-y divide-slate-800">
            {items.map(item => (
              <li key={item.id} className={`px-5 py-4 flex items-center gap-4 ${!item.is_active ? 'opacity-50' : ''}`}>
                <div className="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center flex-shrink-0">
                  <FileText size={20} className="text-primary-light" />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="font-semibold text-white truncate">{item.title}</div>
                  <div className="text-xs text-slate-500">
                    {item.sponsor?.name ?? 'Unknown sponsor'} ·{' '}
                    {item.file_size_kb ? `${item.file_size_kb} KB` : ''} ·{' '}
                    {item.download_count} downloads
                  </div>
                </div>
                <div className="flex items-center gap-2 flex-shrink-0">
                  <a
                    href={item.file_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn-ghost py-1.5 px-2 text-xs"
                  >
                    Preview
                  </a>
                  <button
                    onClick={() => toggleVisibility(item)}
                    className="btn-ghost py-1.5 px-2"
                    title={item.is_active ? 'Hide from delegates' : 'Show to delegates'}
                  >
                    {item.is_active ? <Eye size={16} /> : <EyeOff size={16} />}
                  </button>
                  <button
                    onClick={() => deleteItem(item)}
                    className="btn-ghost py-1.5 px-2 text-red-400 hover:text-red-300"
                    title="Delete permanently"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  )
}
