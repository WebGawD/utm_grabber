export interface DelegateSession {
  delegateId:  string
  email:       string
  name:        string
  firstName:   string
  lastName:    string
  type:        string
  organisation: string
  qrToken:     string
  expiresAt:   number  // Unix timestamp ms
}

export interface AgendaSession {
  id:           string
  day:          string   // '2026-11-09'
  start_time:   string   // '09:00:00'
  end_time:     string   // '10:00:00'
  title:        string
  description:  string | null
  speaker_name: string | null
  speaker_org:  string | null
  room:         string | null
  track:        'plenary' | 'water' | 'sanitation' | 'innovation' | 'policy' | 'networking' | null
  session_type: 'keynote' | 'panel' | 'workshop' | 'break' | 'networking' | 'plenary' | null
  is_published: boolean
  sort_order:   number
}

export interface FlashAlert {
  id:        string
  title:     string
  body:      string
  audience:  string
  status:    string
  sent_at:   string
}

export interface ScannedContact {
  delegateId:   string
  name:         string
  organisation: string | null
  email:        string | null
  qrToken:      string
  scannedAt:    number
}
