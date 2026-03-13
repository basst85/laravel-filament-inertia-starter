import { FormEvent, useState } from 'react'
import { Head } from '@inertiajs/react'
import { isApiRequestError, useApiRequest } from '@/hooks/use-api-request'
import SiteLayout from '@/components/site-layout'

type MenuItem = {
  id: number
  label: string
  url: string
  open_in_new_tab: boolean
}

type ContactFormConfig = {
  title: string
  intro_text?: string | null
  fields: {
    key: string
    label: string
    type: 'text' | 'email' | 'textarea' | 'tel'
    required: boolean
    placeholder?: string
  }[]
  button_label: string
  success_toast: string
  error_toast: string
}

type ToastState = {
  type: 'success' | 'error'
  message: string
}

export default function Contact({
  menuItems,
  contactForm,
}: {
  menuItems: MenuItem[]
  contactForm: ContactFormConfig
}) {
  const [values, setValues] = useState<Record<string, string>>(
    Object.fromEntries(contactForm.fields.map((field) => [field.key, ''])),
  )
  const [toast, setToast] = useState<ToastState | null>(null)
  const [errors, setErrors] = useState<Record<string, string | undefined>>({})
  const { request, loading: submitting } = useApiRequest<{ message?: string }>()

  function getValidatorHint(type: ContactFormConfig['fields'][number]['type']): string {
    if (type === 'email') {
      return 'Vul een geldig e-mailadres in.'
    }

    if (type === 'textarea') {
      return 'Je bericht wordt veilig verzonden.'
    }

    if (type === 'tel') {
      return 'Vul een telefoonnummer in.'
    }

    return 'Controleer of dit veld correct is ingevuld.'
  }

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setErrors({})

    try {
      const data = await request('/api/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(values),
      })

      setToast({
        type: 'success',
        message: data.message || contactForm.success_toast,
      })

      setValues(Object.fromEntries(contactForm.fields.map((field) => [field.key, ''])))
    } catch (caughtError) {
      if (isApiRequestError(caughtError) && caughtError.status === 422) {
        const responseData =
          typeof caughtError.data === 'object' && caughtError.data !== null
            ? (caughtError.data as { errors?: Record<string, string[]> })
            : undefined

        if (responseData?.errors) {
          const nextErrors: Record<string, string | undefined> = {}

          for (const field of contactForm.fields) {
            nextErrors[field.key] = responseData.errors[field.key]?.[0]
          }

          setErrors(nextErrors)
        }
      }

      setToast({
        type: 'error',
        message: contactForm.error_toast,
      })
    } finally {
      window.setTimeout(() => setToast(null), 3000)
    }
  }

  return (
    <SiteLayout menuItems={menuItems}>
      <Head title={contactForm.title} />

      <main className="container py-10">
        <section className="rounded-box border-base-300 bg-base-100 mx-auto max-w-2xl border p-6 shadow-sm md:p-8">
          <h1 className="text-3xl font-bold md:text-4xl">{contactForm.title}</h1>

          {contactForm.intro_text && <p className="mt-3 opacity-80">{contactForm.intro_text}</p>}

          <form className="mt-8 space-y-5" onSubmit={onSubmit} noValidate>
            {contactForm.fields.map((field) => (
              <label key={field.key} className="form-control w-full">
                <div className="label">
                  <span className="label-text">{field.label}</span>
                </div>

                {field.type === 'textarea' ? (
                  <textarea
                    className="textarea textarea-bordered validator min-h-36 w-full"
                    required={field.required}
                    value={values[field.key] ?? ''}
                    placeholder={field.placeholder || ''}
                    onChange={(event) =>
                      setValues((current) => ({
                        ...current,
                        [field.key]: event.target.value,
                      }))
                    }
                  />
                ) : (
                  <input
                    type={field.type}
                    className="input input-bordered validator w-full"
                    required={field.required}
                    value={values[field.key] ?? ''}
                    placeholder={field.placeholder || ''}
                    onChange={(event) =>
                      setValues((current) => ({
                        ...current,
                        [field.key]: event.target.value,
                      }))
                    }
                  />
                )}

                <p className="validator-hint">{getValidatorHint(field.type)}</p>
                {errors[field.key] && (
                  <p className="label-text-alt text-error">{errors[field.key]}</p>
                )}
              </label>
            ))}

            <button type="submit" className="btn btn-primary" disabled={submitting}>
              {submitting ? 'Sending...' : contactForm.button_label}
            </button>
          </form>
        </section>

        {toast && (
          <div className="toast toast-top toast-end z-50">
            <div className={`alert ${toast.type === 'success' ? 'alert-success' : 'alert-error'}`}>
              <span>{toast.message}</span>
            </div>
          </div>
        )}
      </main>
    </SiteLayout>
  )
}
