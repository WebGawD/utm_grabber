export interface AccomConfig {
  supabaseUrl: string
  supabaseKey: string
  restUrl: string
  nonce: string
  successUrl: string
  privacyUrl: string
}

export interface AccomPackage {
  id: string
  hotel_name: string
  description: string
  price_zar: number
  price_usd: number
  total_rooms: number
  booked_count: number
  amenities: string[] | string
}

declare global {
  interface Window {
    awsisaAccom?: AccomConfig
  }
}
