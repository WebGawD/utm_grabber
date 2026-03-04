import { useState } from 'react'
import Step1Personal from './steps/Step1Personal'
import Step2Type from './steps/Step2Type'
import Step3Accommodation from './steps/Step3Accommodation'
import Step4Review from './steps/Step4Review'
import Step5Confirmation from './steps/Step5Confirmation'
import type { RegFormData } from './types'

const TOTAL_STEPS = 5

const STEP_LABELS = [
  'Personal Details',
  'Delegate Type',
  'Accommodation',
  'Review',
  'Confirmed',
]

export default function RegistrationApp() {
  const [step, setStep] = useState(1)
  const [delegateId, setDelegateId] = useState<string | null>(null)
  const [form, setForm] = useState<RegFormData>({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    organisation: '',
    job_title: '',
    country: '',
    delegate_type: '',
    dietary_requirements: '',
    accessibility_needs: '',
    wants_accommodation: false,
    accommodation_package_id: '',
    accommodation_check_in: '2026-11-08',
    accommodation_check_out: '2026-11-13',
    accommodation_notes: '',
    popia_data: false,
    popia_marketing: false,
    popia_profile_public: false,
  })

  const update = (patch: Partial<RegFormData>) =>
    setForm(prev => ({ ...prev, ...patch }))

  const next = () => setStep(s => Math.min(s + 1, TOTAL_STEPS))
  const back = () => setStep(s => Math.max(s - 1, 1))

  return (
    <div style={{ maxWidth: 680, margin: '0 auto', padding: '0 1rem' }}>
      {/* Progress steps */}
      {step < 5 && (
        <div
          style={{
            display: 'flex',
            justifyContent: 'space-between',
            marginBottom: '2.5rem',
            gap: '.25rem',
          }}
          role="list"
          aria-label="Registration steps"
        >
          {STEP_LABELS.slice(0, 4).map((label, i) => {
            const num = i + 1
            const done = step > num
            const active = step === num
            return (
              <div
                key={num}
                role="listitem"
                style={{ flex: 1, textAlign: 'center' }}
              >
                <div
                  style={{
                    width: 32,
                    height: 32,
                    borderRadius: '50%',
                    background: done
                      ? '#0D9488'
                      : active
                      ? '#0D9488'
                      : '#E2E8F0',
                    color: done || active ? '#fff' : '#94A3B8',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontWeight: 700,
                    fontSize: '.875rem',
                    margin: '0 auto .5rem',
                  }}
                >
                  {done ? '✓' : num}
                </div>
                <div
                  style={{
                    fontSize: '.7rem',
                    color: active ? '#0D9488' : '#94A3B8',
                    fontWeight: active ? 700 : 400,
                  }}
                >
                  {label}
                </div>
              </div>
            )
          })}
        </div>
      )}

      {step === 1 && (
        <Step1Personal form={form} update={update} onNext={next} />
      )}
      {step === 2 && (
        <Step2Type form={form} update={update} onNext={next} onBack={back} />
      )}
      {step === 3 && (
        <Step3Accommodation
          form={form}
          update={update}
          onNext={next}
          onBack={back}
        />
      )}
      {step === 4 && (
        <Step4Review
          form={form}
          onBack={back}
          onSuccess={(id) => {
            setDelegateId(id)
            next()
          }}
        />
      )}
      {step === 5 && <Step5Confirmation delegateId={delegateId} form={form} />}
    </div>
  )
}
