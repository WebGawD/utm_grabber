-- ============================================================
-- Seed Data - Awsisa Watersan Dialogue 2026
-- ============================================================

-- ============================================================
-- ACCOMMODATION PACKAGES
-- ============================================================
INSERT INTO accommodation_packages (name, hotel_name, description, price_zar, price_usd, nights, amenities, total_rooms, sort_order) VALUES
(
  'Standard Room - 4 Nights',
  'Emperors Palace Hotel',
  'Comfortable standard room at the conference venue with daily breakfast included. Walking distance to all conference facilities.',
  4800.00, 265.00, 4,
  ARRAY['Breakfast included','Free WiFi','Gym access','Shuttle to venue'],
  80, 1
),
(
  'Deluxe Room - 4 Nights',
  'Emperors Palace Hotel',
  'Upgraded deluxe room with garden view, enhanced amenities, and access to the executive lounge for evening drinks.',
  7200.00, 398.00, 4,
  ARRAY['Breakfast & dinner','Free WiFi','Executive lounge access','Gym & spa access','Shuttle to venue','Late checkout'],
  40, 2
),
(
  'Suite - 4 Nights',
  'Emperors Palace Hotel',
  'Premium suite with separate living area, perfect for senior delegates requiring workspace and privacy.',
  12500.00, 692.00, 4,
  ARRAY['All meals included','Free WiFi','Executive lounge access','Spa treatments','Dedicated concierge','Airport transfer'],
  10, 3
),
(
  'Standard Room - 4 Nights',
  'Birchwood Hotel & OR Tambo Conference Centre',
  'Comfortable accommodation 10 minutes from the venue with shuttle service. Budget-friendly option.',
  3200.00, 177.00, 4,
  ARRAY['Breakfast included','Free WiFi','Conference shuttle (08:00 & 17:30)'],
  120, 4
);

-- ============================================================
-- AGENDA SESSIONS (Sample Programme)
-- ============================================================
INSERT INTO agenda_sessions (day, start_time, end_time, title, track, session_type, is_published, sort_order) VALUES
-- Day 1 - 9 November 2026
('2026-11-09', '07:30', '08:30', 'Delegate Registration & Welcome Coffee', 'networking', 'networking', true, 1),
('2026-11-09', '08:30', '09:00', 'Opening Ceremony & Cultural Performance', 'plenary', 'plenary', true, 2),
('2026-11-09', '09:00', '10:00', 'Keynote: Water Security in the Global South - A 2030 Imperative', 'plenary', 'keynote', true, 3),
('2026-11-09', '10:00', '10:30', 'Tea & Networking Break', 'networking', 'break', true, 4),
('2026-11-09', '10:30', '12:30', 'Panel: National Water Strategies - Lessons from Africa', 'water', 'panel', true, 5),
('2026-11-09', '10:30', '12:30', 'Workshop: Sanitation Finance - Blended Mechanisms for Rural Access', 'sanitation', 'workshop', true, 6),
('2026-11-09', '12:30', '14:00', 'Networking Lunch', 'networking', 'break', true, 7),
('2026-11-09', '14:00', '15:30', 'Session: Innovation in Water Technology - IoT & Smart Metering', 'innovation', 'panel', true, 8),
('2026-11-09', '14:00', '15:30', 'Session: Policy & Regulation - WASH Governance Frameworks', 'policy', 'panel', true, 9),
('2026-11-09', '15:30', '17:00', 'Exhibition Hall Open & Sponsor Showcase', 'networking', 'networking', true, 10),
('2026-11-09', '19:00', '22:00', 'Welcome Gala Dinner - The Legacy Table', 'networking', 'networking', true, 11),

-- Day 2 - 10 November 2026
('2026-11-10', '08:00', '08:30', 'Morning Coffee', 'networking', 'break', true, 20),
('2026-11-10', '08:30', '10:00', 'Keynote: Climate Change & Water Resilience in Sub-Saharan Africa', 'plenary', 'keynote', true, 21),
('2026-11-10', '10:00', '10:30', 'Tea Break & Exhibition', 'networking', 'break', true, 22),
('2026-11-10', '10:30', '12:30', 'Technical Stream: Non-Revenue Water Reduction Strategies', 'water', 'workshop', true, 23),
('2026-11-10', '10:30', '12:30', 'Policy Stream: Sanitation as Human Right - Legal Frameworks', 'policy', 'panel', true, 24),
('2026-11-10', '12:30', '14:00', 'Lunch & NFC Networking Session', 'networking', 'break', true, 25),
('2026-11-10', '14:00', '16:00', 'Afternoon Sessions & Workshops', 'water', 'panel', true, 26),
('2026-11-10', '16:00', '17:00', 'Young Water Professionals Forum', 'networking', 'networking', true, 27),

-- Day 3 - 11 November 2026
('2026-11-11', '08:00', '08:30', 'Morning Coffee', 'networking', 'break', true, 30),
('2026-11-11', '08:30', '10:00', 'Keynote: Financing WASH - DFIs, Bonds & Blended Finance', 'plenary', 'keynote', true, 31),
('2026-11-11', '10:30', '12:30', 'Country Dialogues: National WASH Progress Reports', 'policy', 'panel', true, 32),
('2026-11-11', '12:30', '14:00', 'Networking Lunch', 'networking', 'break', true, 33),
('2026-11-11', '14:00', '16:00', 'Innovation Showcase & Startup Pitch Competition', 'innovation', 'panel', true, 34),
('2026-11-11', '19:00', '22:00', 'Awards Dinner & Legacy Initiative Launch', 'networking', 'networking', true, 35),

-- Day 4 - 12 November 2026
('2026-11-12', '08:00', '08:30', 'Morning Coffee', 'networking', 'break', true, 40),
('2026-11-12', '08:30', '10:00', 'Working Groups: The Johannesburg Declaration Drafting', 'policy', 'workshop', true, 41),
('2026-11-12', '10:00', '10:30', 'Tea Break', 'networking', 'break', true, 42),
('2026-11-12', '10:30', '12:00', 'Closing Plenary: The Johannesburg Declaration - Adoption Ceremony', 'plenary', 'plenary', true, 43),
('2026-11-12', '12:00', '12:30', 'Closing Remarks & Vote of Thanks', 'plenary', 'plenary', true, 44),
('2026-11-12', '12:30', '14:00', 'Farewell Lunch', 'networking', 'break', true, 45);

-- ============================================================
-- SPONSORS (Placeholder entries)
-- ============================================================
INSERT INTO sponsors (name, tier, website_url, booth_number, booth_nfc_slug, description, is_active, sort_order) VALUES
('Department of Water & Sanitation - South Africa', 'platinum', 'https://www.dws.gov.za', 'P1', 'dws-sa', 'Lead government partner and host department for the 2026 Dialogue.', true, 1),
('African Development Bank', 'platinum', 'https://www.afdb.org', 'P2', 'afdb', 'Financing water and sanitation infrastructure across Africa.', true, 2),
('GIZ - Deutsche Gesellschaft für Internationale Zusammenarbeit', 'gold', 'https://www.giz.de', 'G1', 'giz', 'German development cooperation supporting WASH in Africa.', true, 3),
('UNICEF', 'gold', 'https://www.unicef.org', 'G2', 'unicef', 'Global leader in child-centred WASH programming.', true, 4),
('Water Research Commission', 'gold', 'https://www.wrc.org.za', 'G3', 'wrc', 'South Africa''s leading water research institution.', true, 5),
('Grundfos', 'silver', 'https://www.grundfos.com', 'S1', 'grundfos', 'Global leader in advanced pump solutions for water.', true, 6),
('Xylem', 'silver', 'https://www.xylem.com', 'S2', 'xylem', 'Technology-led water solutions for utilities and municipalities.', true, 7),
('Standard Bank', 'bronze', 'https://www.standardbank.com', 'B1', 'standard-bank', 'Banking partner for sustainable infrastructure finance.', true, 8);
