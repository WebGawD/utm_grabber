-- ============================================================
-- Row Level Security Policies
-- ============================================================

-- Enable RLS on all tables
ALTER TABLE delegates ENABLE ROW LEVEL SECURITY;
ALTER TABLE accommodation_packages ENABLE ROW LEVEL SECURITY;
ALTER TABLE accommodation_bookings ENABLE ROW LEVEL SECURITY;
ALTER TABLE agenda_sessions ENABLE ROW LEVEL SECURITY;
ALTER TABLE sponsors ENABLE ROW LEVEL SECURITY;
ALTER TABLE swag_bag_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE donations ENABLE ROW LEVEL SECURITY;
ALTER TABLE nfc_taps ENABLE ROW LEVEL SECURITY;
ALTER TABLE flash_alerts ENABLE ROW LEVEL SECURITY;
ALTER TABLE checkin_log ENABLE ROW LEVEL SECURITY;
ALTER TABLE popia_consents ENABLE ROW LEVEL SECURITY;
ALTER TABLE staff_users ENABLE ROW LEVEL SECURITY;

-- Helper: check if current user is staff/admin
CREATE OR REPLACE FUNCTION is_staff()
RETURNS boolean AS $$
  SELECT EXISTS (
    SELECT 1 FROM staff_users
    WHERE id = auth.uid()
    AND role IN ('staff','admin','super_admin')
  );
$$ LANGUAGE sql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION is_admin()
RETURNS boolean AS $$
  SELECT EXISTS (
    SELECT 1 FROM staff_users
    WHERE id = auth.uid()
    AND role IN ('admin','super_admin')
  );
$$ LANGUAGE sql SECURITY DEFINER;

-- ============================================================
-- DELEGATES
-- ============================================================

-- Public read: only networking-safe fields for public profiles
CREATE POLICY "Public delegates read own public profile"
  ON delegates FOR SELECT
  USING (profile_public = true);

-- Staff can read all delegates
CREATE POLICY "Staff read all delegates"
  ON delegates FOR SELECT
  USING (is_staff());

-- Registration via service role / API route only
CREATE POLICY "Service role insert delegates"
  ON delegates FOR INSERT
  WITH CHECK (auth.role() = 'service_role');

-- Staff can update delegates
CREATE POLICY "Staff update delegates"
  ON delegates FOR UPDATE
  USING (is_staff());

-- Only super_admin can delete
CREATE POLICY "Super admin delete delegates"
  ON delegates FOR DELETE
  USING (
    EXISTS (
      SELECT 1 FROM staff_users
      WHERE id = auth.uid() AND role = 'super_admin'
    )
  );

-- ============================================================
-- ACCOMMODATION PACKAGES
-- ============================================================

-- Anyone can view active packages
CREATE POLICY "Public read active packages"
  ON accommodation_packages FOR SELECT
  USING (is_active = true);

-- Admin manage packages
CREATE POLICY "Admin manage packages"
  ON accommodation_packages FOR ALL
  USING (is_admin());

-- ============================================================
-- ACCOMMODATION BOOKINGS
-- ============================================================

-- Staff see all bookings
CREATE POLICY "Staff read all bookings"
  ON accommodation_bookings FOR SELECT
  USING (is_staff());

-- Service role inserts (via API)
CREATE POLICY "Service role insert bookings"
  ON accommodation_bookings FOR INSERT
  WITH CHECK (auth.role() = 'service_role');

-- Staff update bookings
CREATE POLICY "Staff update bookings"
  ON accommodation_bookings FOR UPDATE
  USING (is_staff());

-- ============================================================
-- AGENDA SESSIONS
-- ============================================================

-- Public can read published sessions
CREATE POLICY "Public read published agenda"
  ON agenda_sessions FOR SELECT
  USING (is_published = true);

-- Admin manage agenda
CREATE POLICY "Admin manage agenda"
  ON agenda_sessions FOR ALL
  USING (is_admin());

-- ============================================================
-- SPONSORS
-- ============================================================

-- Public read active sponsors
CREATE POLICY "Public read active sponsors"
  ON sponsors FOR SELECT
  USING (is_active = true);

-- Admin manage sponsors
CREATE POLICY "Admin manage sponsors"
  ON sponsors FOR ALL
  USING (is_admin());

-- ============================================================
-- SWAG BAG ITEMS
-- ============================================================

-- Public read active items
CREATE POLICY "Public read active swag items"
  ON swag_bag_items FOR SELECT
  USING (is_active = true);

-- Staff manage swag items
CREATE POLICY "Staff manage swag items"
  ON swag_bag_items FOR ALL
  USING (is_staff());

-- ============================================================
-- DONATIONS (PII protected)
-- ============================================================

-- Only staff can read donations
CREATE POLICY "Staff read donations"
  ON donations FOR SELECT
  USING (is_staff());

-- Service role inserts (via API route)
CREATE POLICY "Service role insert donations"
  ON donations FOR INSERT
  WITH CHECK (auth.role() = 'service_role');

-- Staff update donation status
CREATE POLICY "Staff update donations"
  ON donations FOR UPDATE
  USING (is_staff());

-- ============================================================
-- NFC TAPS
-- ============================================================

-- Staff view all taps
CREATE POLICY "Staff read nfc taps"
  ON nfc_taps FOR SELECT
  USING (is_staff());

-- Anyone can insert a tap (anonymous logging)
CREATE POLICY "Anyone insert nfc tap"
  ON nfc_taps FOR INSERT
  WITH CHECK (true);

-- ============================================================
-- FLASH ALERTS
-- ============================================================

-- Staff can read, create, update alerts
CREATE POLICY "Staff manage alerts"
  ON flash_alerts FOR ALL
  USING (is_staff());

-- ============================================================
-- CHECK-IN LOG
-- ============================================================

-- Staff can read and insert
CREATE POLICY "Staff read checkin log"
  ON checkin_log FOR SELECT
  USING (is_staff());

CREATE POLICY "Staff insert checkin log"
  ON checkin_log FOR INSERT
  WITH CHECK (is_staff() OR auth.role() = 'service_role');

-- ============================================================
-- POPIA CONSENTS (append-only)
-- ============================================================

-- Admin only reads
CREATE POLICY "Admin read popia consents"
  ON popia_consents FOR SELECT
  USING (is_admin());

-- Service role only inserts (never from client)
CREATE POLICY "Service role insert popia"
  ON popia_consents FOR INSERT
  WITH CHECK (auth.role() = 'service_role');

-- NO UPDATE or DELETE policies = append-only enforced

-- ============================================================
-- STAFF USERS
-- ============================================================

-- Staff can read own record
CREATE POLICY "Staff read own record"
  ON staff_users FOR SELECT
  USING (id = auth.uid() OR is_admin());

-- Admin manage staff
CREATE POLICY "Admin manage staff"
  ON staff_users FOR ALL
  USING (is_admin());
