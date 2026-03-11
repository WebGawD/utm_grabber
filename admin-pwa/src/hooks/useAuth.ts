// Re-exported from the shared AuthContext so all existing imports continue
// to work unchanged. getSession() now runs only once for the whole app.
export { useAuth } from '@/contexts/AuthContext'
