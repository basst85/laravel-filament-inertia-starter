import { useCallback, useState } from 'react'

export class ApiRequestError extends Error {
  status: number
  data: unknown

  constructor(message: string, status: number, data: unknown) {
    super(message)
    this.name = 'ApiRequestError'
    this.status = status
    this.data = data
  }
}

export function isApiRequestError(error: unknown): error is ApiRequestError {
  return error instanceof ApiRequestError
}

function getErrorMessage(data: unknown, fallback: string): string {
  if (typeof data === 'object' && data !== null && 'message' in data) {
    const message = (data as { message?: unknown }).message

    if (typeof message === 'string' && message.trim() !== '') {
      return message
    }
  }

  return fallback
}

export function useApiRequest<TResponse = unknown>() {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const request = useCallback(async (url: string, init?: RequestInit): Promise<TResponse> => {
    setLoading(true)
    setError(null)

    try {
      const response = await fetch(url, init)

      let data: unknown = null

      try {
        data = await response.json()
      } catch {
        data = null
      }

      if (!response.ok) {
        const message = getErrorMessage(data, 'Request failed.')

        throw new ApiRequestError(message, response.status, data)
      }

      return data as TResponse
    } catch (caughtError) {
      const message =
        caughtError instanceof ApiRequestError
          ? caughtError.message
          : caughtError instanceof Error
            ? caughtError.message
            : 'Unexpected request error.'

      setError(message)
      throw caughtError
    } finally {
      setLoading(false)
    }
  }, [])

  return {
    request,
    loading,
    error,
  }
}
