export interface RegFormData {
  first_name: string
  last_name: string
  email: string
  phone: string
  organisation: string
  job_title: string
  country: string
  delegate_type: string
  dietary_requirements: string
  accessibility_needs: string
  wants_accommodation: boolean
  accommodation_package_id: string
  accommodation_check_in: string
  accommodation_check_out: string
  accommodation_notes: string
  popia_data: boolean
  popia_marketing: boolean
  popia_profile_public: boolean
}

declare global {
  interface Window {
    awsisaReg?: {
      supabaseUrl: string
      supabaseKey: string
      restUrl: string
      nonce: string
      successUrl: string
      privacyUrl: string
      pricing: Record<string, { zar: number; usd: number }>
    }
  }
}
