import { useState } from 'react'
import { QrCode, Camera, Users } from 'lucide-react'
import { useAuth } from '@/hooks/useAuth'
import MyQrCard from '@/components/networking/MyQrCard'
import QrScanner from '@/components/networking/QrScanner'
import type { ScannedContact } from '@/types'

const CONTACTS_KEY = 'awsisa_contacts'

function getContacts(): ScannedContact[] {
  try { return JSON.parse(localStorage.getItem(CONTACTS_KEY) || '[]') }
  catch { return [] }
}

function saveContact(contact: ScannedContact) {
  const existing = getContacts()
  const updated  = [contact, ...existing.filter(c => c.delegateId !== contact.delegateId)]
  localStorage.setItem(CONTACTS_KEY, JSON.stringify(updated))
  return updated
}

type Tab = 'myqr' | 'scan' | 'contacts'

export default function NetworkingPage() {
  const { session }               = useAuth()
  const [tab, setTab]             = useState<Tab>('myqr')
  const [contacts, setContacts]   = useState<ScannedContact[]>(getContacts)

  function onContactScanned(contact: ScannedContact) {
    setContacts(saveContact(contact))
    setTab('contacts')
  }

  const tabs: { id: Tab; icon: typeof QrCode; label: string }[] = [
    { id: 'myqr',     icon: QrCode,  label: 'My QR' },
    { id: 'scan',     icon: Camera,  label: 'Scan' },
    { id: 'contacts', icon: Users,   label: `Contacts${contacts.length > 0 ? ` (${contacts.length})` : ''}` },
  ]

  return (
    <div style={{ display: 'flex', flexDirection: 'column', height: 'calc(100dvh - 112px)' }}>
      {/* Sub-tab bar */}
      <div style={{ display: 'flex', background: '#fff', borderBottom: '1px solid #E2E8F0', flexShrink: 0 }}>
        {tabs.map(({ id, icon: Icon, label }) => (
          <button key={id} onClick={() => setTab(id)} style={{ flex: 1, padding: '0.875rem 0.5rem', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '0.25rem', border: 'none', borderBottom: tab === id ? '2px solid #0D9488' : '2px solid transparent', background: 'none', color: tab === id ? '#0D9488' : '#94A3B8', cursor: 'pointer', marginBottom: '-1px' }}>
            <Icon size={18} />
            <span style={{ fontSize: '0.72rem', fontWeight: tab === id ? 700 : 500 }}>{label}</span>
          </button>
        ))}
      </div>

      {/* Content */}
      <div style={{ flex: 1, overflow: 'auto' }}>
        {tab === 'myqr' && <MyQrCard session={session} />}
        {tab === 'scan' && <QrScanner onContact={onContactScanned} />}
        {tab === 'contacts' && (
          <div style={{ padding: '1rem' }}>
            {contacts.length === 0 ? (
              <div style={{ textAlign: 'center', padding: '4rem 1rem', color: '#94A3B8' }}>
                <Users size={48} style={{ margin: '0 auto 1rem', opacity: 0.3 }} />
                <p style={{ fontWeight: 600, color: '#64748B' }}>No contacts yet</p>
                <p style={{ fontSize: '0.875rem' }}>Scan another delegate's QR code to connect</p>
              </div>
            ) : (
              contacts.map(c => (
                <div key={c.delegateId} style={{ background: '#fff', border: '1px solid #E2E8F0', borderRadius: '0.875rem', padding: '0.875rem', marginBottom: '0.625rem', display: 'flex', alignItems: 'center', gap: '0.875rem' }}>
                  <div style={{ width: 42, height: 42, borderRadius: '50%', background: 'linear-gradient(135deg,#0D9488,#115E59)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontWeight: 800, fontSize: '1.1rem', fontFamily: 'Outfit,sans-serif', flexShrink: 0 }}>
                    {c.name.charAt(0).toUpperCase()}
                  </div>
                  <div style={{ flex: 1, minWidth: 0 }}>
                    <div style={{ fontWeight: 700, fontSize: '0.9rem', color: '#0F172A' }}>{c.name}</div>
                    {c.organisation && <div style={{ fontSize: '0.75rem', color: '#64748B' }}>{c.organisation}</div>}
                    {c.email && <div style={{ fontSize: '0.72rem', color: '#0D9488' }}>{c.email}</div>}
                  </div>
                  <div style={{ fontSize: '0.65rem', color: '#CBD5E1' }}>{new Date(c.scannedAt).toLocaleDateString()}</div>
                </div>
              ))
            )}
          </div>
        )}
      </div>
    </div>
  )
}
