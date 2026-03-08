-- Migration 004: Delegate OTP table for PWA authentication
-- Run this on the Supabase dashboard → SQL Editor

-- ============================================================
-- Table: delegate_otps
-- Stores one-time passwords for delegate app login.
-- Accessed only via WordPress plugin with service key (no public RLS).
-- ============================================================
CREATE TABLE IF NOT EXISTS delegate_otps (
  id           UUID        PRIMARY KEY DEFAULT gen_random_uuid(),
  email        TEXT        NOT NULL,
  otp_hash     TEXT        NOT NULL,          -- PHP password_hash() of 6-digit code
  expires_at   TIMESTAMPTZ NOT NULL DEFAULT (NOW() + INTERVAL '15 minutes'),
  used_at      TIMESTAMPTZ,                   -- NULL = not yet used
  delegate_id  UUID        REFERENCES delegates(id) ON DELETE CASCADE,
  created_at   TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Index for efficient lookup by email (only unused OTPs)
CREATE INDEX IF NOT EXISTS idx_delegate_otps_email
  ON delegate_otps(email)
  WHERE used_at IS NULL;

-- Enable RLS — no public policies (service key only)
ALTER TABLE delegate_otps ENABLE ROW LEVEL SECURITY;

-- ============================================================
-- RLS Policy: allow public SELECT on broadcast flash_alerts
-- Required so the Supabase realtime channel works for anon delegates
-- ============================================================
DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_policies
    WHERE schemaname = 'public'
      AND tablename  = 'flash_alerts'
      AND policyname = 'Public can read broadcast alerts'
  ) THEN
    EXECUTE $policy$
      CREATE POLICY "Public can read broadcast alerts"
        ON flash_alerts
        FOR SELECT
        USING (status = 'sent' AND audience = 'all');
    $policy$;
  END IF;
END$$;

-- ============================================================
-- Cleanup helper: automatically expire old OTPs
-- (Optional) Create a scheduled function via Supabase Cron extension
-- or just let the PHP layer filter by expires_at.
-- ============================================================
