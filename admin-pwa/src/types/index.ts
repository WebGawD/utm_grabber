// ============================================================
// Core Domain Types — Awsisa Watersan Dialogue 2026
// ============================================================

export type DelegateType =
  | 'government' | 'utility' | 'private_sector' | 'ngo'
  | 'academic'   | 'media'   | 'exhibitor'       | 'sponsor'

export type RegistrationStatus = 'pending' | 'confirmed' | 'cancelled'
export type PaymentStatus      = 'unpaid'  | 'paid'      | 'waived' | 'partial'

export interface Delegate {
  id:                  string
  created_at:          string
  updated_at:          string
  email:               string
  first_name:          string
  last_name:           string
  organisation:        string | null
  job_title:           string | null
  country:             string
  phone:               string | null
  delegate_type:       DelegateType
  registration_status: RegistrationStatus
  payment_ref:         string | null
  payment_status:      PaymentStatus
  checked_in:          boolean
  checked_in_at:       string | null
  qr_code_token:       string
  popia_consent:       boolean
  popia_consented_at:  string | null
  marketing_consent:   boolean
  profile_public:      boolean
  avatar_url:          string | null
  dietary_requirements: string | null
  accessibility_needs:  string | null
  notes:               string | null
}

export interface AccommodationPackage {
  id:             string
  name:           string
  hotel_name:     string
  description:    string | null
  price_zar:      number
  price_usd:      number | null
  nights:         number
  max_occupancy:  number
  amenities:      string[]
  total_rooms:    number
  booked_count:   number
  is_active:      boolean
  image_url:      string | null
  sort_order:     number
}

export interface AccommodationBooking {
  id:              string
  created_at:      string
  delegate_id:     string
  package_id:      string
  check_in_date:   string
  check_out_date:  string
  guests:          number
  total_price:     number | null
  payment_status:  'pending' | 'paid' | 'cancelled' | 'refunded'
  payment_ref:     string | null
  special_requests: string | null
}

export interface AgendaSession {
  id:             string
  day:            string
  start_time:     string
  end_time:       string
  title:          string
  description:    string | null
  speaker_name:   string | null
  speaker_org:    string | null
  speaker_bio:    string | null
  speaker_avatar: string | null
  room:           string | null
  track:          'plenary' | 'water' | 'sanitation' | 'innovation' | 'policy' | 'networking' | null
  session_type:   'keynote' | 'panel' | 'workshop' | 'break' | 'networking' | 'plenary'
  is_published:   boolean
  sort_order:     number
}

export interface Sponsor {
  id:             string
  name:           string
  tier:           'platinum' | 'gold' | 'silver' | 'bronze' | 'exhibitor' | 'partner'
  logo_url:       string | null
  website_url:    string | null
  booth_number:   string | null
  booth_nfc_slug: string | null
  description:    string | null
  contact_name:   string | null
  contact_email:  string | null
  is_active:      boolean
  sort_order:     number
}

export interface SwagBagItem {
  id:             string
  created_at:     string
  sponsor_id:     string
  title:          string
  description:    string | null
  file_url:       string
  file_size_kb:   number | null
  file_type:      string
  thumbnail_url:  string | null
  download_count: number
  is_active:      boolean
  sponsor?:       Pick<Sponsor, 'name' | 'logo_url' | 'tier'>
}

export interface Donation {
  id:             string
  created_at:     string
  donor_name:     string
  donor_email:    string
  donor_phone:    string | null
  amount_zar:     number
  currency:       string
  payment_method: 'payfast' | 'peachpayments' | 'eft' | 'card' | null
  payment_ref:    string | null
  payment_status: 'pending' | 'completed' | 'failed' | 'refunded'
  is_anonymous:   boolean
  message:        string | null
  project_area:   string | null
  popia_consent:  boolean
  receipt_sent:   boolean
  receipt_url:    string | null
}

export interface NfcTap {
  id:            string
  created_at:    string
  tap_type:      'delegate_view' | 'booth_tap' | 'swag_download'
  delegate_id:   string | null
  sponsor_id:    string | null
  visitor_token: string | null
  user_agent:    string | null
  country:       string | null
}

export interface FlashAlert {
  id:          string
  created_at:  string
  created_by:  string | null
  title:       string
  body:        string
  audience:    'all' | 'checked_in' | 'government' | 'utility' | 'private_sector' | 'ngo' | 'academic' | 'media'
  channels:    string[]
  sent_at:     string | null
  sent_count:  number
  status:      'draft' | 'sending' | 'sent' | 'failed'
}

export interface CheckinLogEntry {
  id:            string
  created_at:    string
  delegate_id:   string
  checked_in_by: string | null
  method:        'qr' | 'manual' | 'nfc'
  device_id:     string | null
  synced_at:     string | null
  delegate?:     Pick<Delegate, 'first_name' | 'last_name' | 'organisation' | 'delegate_type'>
}

export interface StaffUser {
  id:         string
  full_name:  string | null
  role:       'super_admin' | 'admin' | 'staff' | 'volunteer'
  created_at: string
}

// ============================================================
// Offline Queue Types
// ============================================================
export interface OfflineCheckinRecord {
  id:          string  // local UUID
  delegate_id: string
  method:      'qr' | 'manual' | 'nfc'
  device_id:   string
  created_at:  string  // ISO string
  retries:     number
}

// ============================================================
// Dashboard Stats
// ============================================================
export interface DashboardStats {
  total_delegates:     number
  checked_in:          number
  total_donations_zar: number
  total_bookings:      number
  nfc_taps:            number
  by_type:             Record<DelegateType, number>
  by_country:          Array<{ country: string; count: number }>
}
