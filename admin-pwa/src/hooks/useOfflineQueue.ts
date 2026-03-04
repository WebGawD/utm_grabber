/**
 * Offline Check-In Queue
 *
 * Persists check-in records to IndexedDB when offline and
 * automatically syncs them to Supabase when connectivity restores.
 *
 * Uses the Background Sync API where available, with a fallback
 * to polling on navigator.onLine changes.
 */

import { useEffect, useRef, useCallback } from 'react'
import { openDB, type IDBPDatabase } from 'idb'
import { supabase } from '@/lib/supabase'
import type { OfflineCheckinRecord } from '@/types'

const DB_NAME    = 'awsisa-offline'
const DB_VERSION = 1
const STORE      = 'checkin_queue'

let dbPromise: Promise<IDBPDatabase> | null = null

function getDB() {
  if (!dbPromise) {
    dbPromise = openDB(DB_NAME, DB_VERSION, {
      upgrade(db) {
        if (!db.objectStoreNames.contains(STORE)) {
          db.createObjectStore(STORE, { keyPath: 'id' })
        }
      },
    })
  }
  return dbPromise
}

/** Enqueue a check-in record to IndexedDB */
export async function enqueueCheckin(record: Omit<OfflineCheckinRecord, 'retries'>) {
  const db = await getDB()
  await db.put(STORE, { ...record, retries: 0 })
  console.log('[OfflineQueue] Enqueued:', record.delegate_id)

  // Request background sync if available.
  if ('serviceWorker' in navigator && 'SyncManager' in window) {
    const reg = await navigator.serviceWorker.ready
    await (reg as unknown as { sync: { register: (tag: string) => Promise<void> } })
      .sync.register('checkin-sync')
  }
}

/** Attempt to flush all queued records to Supabase */
export async function flushQueue(deviceId: string): Promise<number> {
  const db      = await getDB()
  const records: OfflineCheckinRecord[] = await db.getAll(STORE)
  if (records.length === 0) return 0

  let synced = 0

  for (const record of records) {
    try {
      // Mark delegate as checked in.
      const { error: delegateError } = await supabase
        .from('delegates')
        .update({
          checked_in:    true,
          checked_in_at: record.created_at,
          updated_at:    new Date().toISOString(),
        })
        .eq('id', record.delegate_id)
        .eq('checked_in', false) // Only if not already checked in (idempotent).

      if (delegateError) throw delegateError

      // Write to checkin_log.
      const { error: logError } = await supabase
        .from('checkin_log')
        .upsert({
          id:            record.id,
          created_at:    record.created_at,
          delegate_id:   record.delegate_id,
          method:        record.method,
          device_id:     deviceId,
          synced_at:     new Date().toISOString(),
        }, { onConflict: 'id' }) // Idempotent on re-sync.

      if (logError) throw logError

      // Remove from queue.
      await db.delete(STORE, record.id)
      synced++
      console.log('[OfflineQueue] Synced:', record.delegate_id)
    } catch (err) {
      console.warn('[OfflineQueue] Failed to sync:', record.delegate_id, err)
      // Increment retry counter; remove after 10 failed attempts.
      if (record.retries >= 10) {
        await db.delete(STORE, record.id)
        console.error('[OfflineQueue] Abandoned after 10 retries:', record.delegate_id)
      } else {
        await db.put(STORE, { ...record, retries: record.retries + 1 })
      }
    }
  }

  return synced
}

/** Returns the current queue size */
export async function getQueueSize(): Promise<number> {
  const db = await getDB()
  return db.count(STORE)
}

/** React hook: auto-syncs queue when navigator.onLine becomes true */
export function useOfflineQueue(deviceId: string) {
  const deviceIdRef = useRef(deviceId)
  deviceIdRef.current = deviceId

  const trySync = useCallback(async () => {
    if (!navigator.onLine) return
    const count = await flushQueue(deviceIdRef.current)
    if (count > 0) {
      console.log(`[OfflineQueue] Auto-synced ${count} check-in(s)`)
    }
  }, [])

  useEffect(() => {
    // Try syncing immediately.
    trySync()

    window.addEventListener('online', trySync)
    return () => window.removeEventListener('online', trySync)
  }, [trySync])

  return { enqueueCheckin, flushQueue, getQueueSize }
}
