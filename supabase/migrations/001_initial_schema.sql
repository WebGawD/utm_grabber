-- ============================================================
-- Awsisa Watersan Dialogue 2026 - Initial Schema
-- ============================================================

-- Enable UUID generation
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ============================================================
-- STAFF USERS (extends auth.users)
-- ============================================================
CREATE TABLE staff_users (
  id         uuid PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  full_name  text,
  role       text NOT NULL DEFAULT 'staff' CHECK (role IN ('super_admin','admin','staff','volunteer')),
  created_at timestamptz DEFAULT now()
);

-- ============================================================
-- DELEGATES
-- ============================================================
CREATE TABLE delegates (
  id                  uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at          timestamptz DEFAULT now(),
  updated_at          timestamptz DEFAULT now(),
  email               text UNIQUE NOT NULL,
  first_name          text NOT NULL,
  last_name           text NOT NULL,
  organisation        text,
  job_title           text,
  country             text NOT NULL,
  phone               text,
  delegate_type       text NOT NULL CHECK (delegate_type IN (
                        'government','utility','private_sector','ngo',
                        'academic','media','exhibitor','sponsor'
                      )),
  registration_status text NOT NULL DEFAULT 'pending' CHECK (registration_status IN (
                        'pending','confirmed','cancelled'
                      )),
  payment_ref         text,
  payment_status      text NOT NULL DEFAULT 'unpaid' CHECK (payment_status IN (
                        'unpaid','paid','waived','partial'
                      )),
  checked_in          boolean DEFAULT false,
  checked_in_at       timestamptz,
  qr_code_token       text UNIQUE DEFAULT encode(gen_random_bytes(16), 'hex'),
  popia_consent       boolean NOT NULL DEFAULT false,
  popia_consented_at  timestamptz,
  marketing_consent   boolean DEFAULT false,
  profile_public      boolean DEFAULT true,
  avatar_url          text,
  dietary_requirements text,
  accessibility_needs  text,
  notes               text
);

CREATE INDEX idx_delegates_email ON delegates(email);
CREATE INDEX idx_delegates_qr_token ON delegates(qr_code_token);
CREATE INDEX idx_delegates_type ON delegates(delegate_type);
CREATE INDEX idx_delegates_checkin ON delegates(checked_in);

-- Auto-update updated_at
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN NEW.updated_at = now(); RETURN NEW; END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER delegates_updated_at
  BEFORE UPDATE ON delegates
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ============================================================
-- ACCOMMODATION PACKAGES
-- ============================================================
CREATE TABLE accommodation_packages (
  id             uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name           text NOT NULL,
  hotel_name     text NOT NULL,
  description    text,
  price_zar      numeric(10,2) NOT NULL,
  price_usd      numeric(10,2),
  nights         int NOT NULL DEFAULT 4,
  max_occupancy  int DEFAULT 2,
  amenities      text[],
  available_from date DEFAULT '2026-11-09',
  available_to   date DEFAULT '2026-11-12',
  total_rooms    int NOT NULL DEFAULT 50,
  booked_count   int NOT NULL DEFAULT 0,
  is_active      boolean DEFAULT true,
  image_url      text,
  sort_order     int DEFAULT 0
);

-- ============================================================
-- ACCOMMODATION BOOKINGS
-- ============================================================
CREATE TABLE accommodation_bookings (
  id               uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at       timestamptz DEFAULT now(),
  delegate_id      uuid REFERENCES delegates(id) ON DELETE CASCADE,
  package_id       uuid REFERENCES accommodation_packages(id),
  check_in_date    date NOT NULL DEFAULT '2026-11-09',
  check_out_date   date NOT NULL DEFAULT '2026-11-12',
  guests           int DEFAULT 1,
  total_price      numeric(10,2),
  payment_status   text DEFAULT 'pending' CHECK (payment_status IN ('pending','paid','cancelled','refunded')),
  payment_ref      text,
  special_requests text
);

CREATE INDEX idx_bookings_delegate ON accommodation_bookings(delegate_id);

-- ============================================================
-- AGENDA SESSIONS
-- ============================================================
CREATE TABLE agenda_sessions (
  id           uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  day          date NOT NULL,
  start_time   time NOT NULL,
  end_time     time NOT NULL,
  title        text NOT NULL,
  description  text,
  speaker_name text,
  speaker_org  text,
  speaker_bio  text,
  speaker_avatar text,
  room         text,
  track        text CHECK (track IN ('plenary','water','sanitation','innovation','policy','networking')),
  session_type text DEFAULT 'session' CHECK (session_type IN ('keynote','panel','workshop','break','networking','plenary')),
  is_published boolean DEFAULT false,
  sort_order   int DEFAULT 0
);

CREATE INDEX idx_agenda_day ON agenda_sessions(day);
CREATE INDEX idx_agenda_track ON agenda_sessions(track);

-- ============================================================
-- SPONSORS
-- ============================================================
CREATE TABLE sponsors (
  id             uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name           text NOT NULL,
  tier           text NOT NULL CHECK (tier IN ('platinum','gold','silver','bronze','exhibitor','partner')),
  logo_url       text,
  website_url    text,
  booth_number   text,
  booth_nfc_slug text UNIQUE,
  description    text,
  contact_name   text,
  contact_email  text,
  is_active      boolean DEFAULT true,
  sort_order     int DEFAULT 0
);

-- ============================================================
-- DIGITAL SWAG BAG ITEMS
-- ============================================================
CREATE TABLE swag_bag_items (
  id             uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at     timestamptz DEFAULT now(),
  sponsor_id     uuid REFERENCES sponsors(id) ON DELETE CASCADE,
  title          text NOT NULL,
  description    text,
  file_url       text NOT NULL,
  file_size_kb   int,
  file_type      text DEFAULT 'pdf',
  thumbnail_url  text,
  download_count int DEFAULT 0,
  is_active      boolean DEFAULT true
);

CREATE INDEX idx_swag_sponsor ON swag_bag_items(sponsor_id);

-- ============================================================
-- DONATIONS ("Legacy" Initiative)
-- ============================================================
CREATE TABLE donations (
  id             uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at     timestamptz DEFAULT now(),
  donor_name     text NOT NULL,
  donor_email    text NOT NULL,
  donor_phone    text,
  amount_zar     numeric(10,2) NOT NULL,
  currency       text DEFAULT 'ZAR',
  payment_method text CHECK (payment_method IN ('payfast','peachpayments','eft','card')),
  payment_ref    text,
  payment_status text DEFAULT 'pending' CHECK (payment_status IN ('pending','completed','failed','refunded')),
  is_anonymous   boolean DEFAULT false,
  message        text,
  project_area   text,
  popia_consent  boolean NOT NULL DEFAULT false,
  receipt_sent   boolean DEFAULT false,
  receipt_url    text
);

-- ============================================================
-- NFC / NETWORKING TAPS
-- ============================================================
CREATE TABLE nfc_taps (
  id          uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at  timestamptz DEFAULT now(),
  tap_type    text NOT NULL CHECK (tap_type IN ('delegate_view','booth_tap','swag_download')),
  delegate_id uuid REFERENCES delegates(id) ON DELETE SET NULL,
  sponsor_id  uuid REFERENCES sponsors(id) ON DELETE SET NULL,
  visitor_token text,
  user_agent  text,
  country     text
);

CREATE INDEX idx_taps_delegate ON nfc_taps(delegate_id);
CREATE INDEX idx_taps_sponsor ON nfc_taps(sponsor_id);
CREATE INDEX idx_taps_type ON nfc_taps(tap_type);

-- ============================================================
-- FLASH ALERTS
-- ============================================================
CREATE TABLE flash_alerts (
  id          uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at  timestamptz DEFAULT now(),
  created_by  uuid REFERENCES auth.users(id),
  title       text NOT NULL,
  body        text NOT NULL,
  audience    text DEFAULT 'all' CHECK (audience IN (
                'all','checked_in','government','utility','private_sector','ngo','academic','media'
              )),
  channels    text[] DEFAULT '{push}',
  sent_at     timestamptz,
  sent_count  int DEFAULT 0,
  status      text DEFAULT 'draft' CHECK (status IN ('draft','sending','sent','failed'))
);

-- ============================================================
-- CHECK-IN LOG (audit trail, offline-sync ready)
-- ============================================================
CREATE TABLE checkin_log (
  id            uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at    timestamptz DEFAULT now(),
  delegate_id   uuid REFERENCES delegates(id) ON DELETE CASCADE,
  checked_in_by uuid REFERENCES auth.users(id),
  method        text DEFAULT 'qr' CHECK (method IN ('qr','manual','nfc')),
  device_id     text,
  synced_at     timestamptz DEFAULT now()
);

CREATE INDEX idx_checkin_delegate ON checkin_log(delegate_id);
CREATE INDEX idx_checkin_by ON checkin_log(checked_in_by);

-- ============================================================
-- POPIA CONSENT LOG (append-only, immutable audit trail)
-- ============================================================
CREATE TABLE popia_consents (
  id               uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  created_at       timestamptz DEFAULT now(),
  entity_type      text NOT NULL CHECK (entity_type IN ('delegate','donor','nfc_tap','booth_visitor')),
  entity_id        uuid NOT NULL,
  consent_version  text NOT NULL DEFAULT '1.0',
  ip_address       text,
  user_agent       text,
  consented_fields text[]
);

CREATE INDEX idx_popia_entity ON popia_consents(entity_type, entity_id);
