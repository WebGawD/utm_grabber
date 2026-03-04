export interface DonateConfig {
  supabaseUrl: string
  supabaseKey: string
  restUrl: string
  nonce: string
  payfastMerchant: string
  payfastKey: string
  payfastSandbox: boolean
  privacyUrl: string
  successUrl: string
  cancelUrl: string
}

declare global {
  interface Window {
    awsisaDonate?: DonateConfig
  }
}
