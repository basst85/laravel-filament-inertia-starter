<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactFormSetting;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ContactFormController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $settings = ContactFormSetting::query()->first();

        $rawFields = is_array($settings?->fields) ? $settings->fields : [];

        /** @var array<int, array<string, mixed>> $normalizedFields */
        $normalizedFields = collect($rawFields)->values()->all();

        $fields = collect($normalizedFields)
            ->filter(
                fn (array $field): bool => filled($field['key'] ?? null) && filled($field['label'] ?? null),
            )
            ->values();

        if ($fields->isEmpty()) {
            $fields = collect([
                ['key' => 'name', 'type' => 'text', 'required' => true],
                ['key' => 'email', 'type' => 'email', 'required' => true],
                ['key' => 'message', 'type' => 'textarea', 'required' => true],
            ]);
        }

        $rules = [];

        foreach ($fields as $field) {
            $key = (string) ($field['key'] ?? '');

            if ($key === '') {
                continue;
            }

            $type = (string) ($field['type'] ?? 'text');
            $isRequired = (bool) ($field['required'] ?? false);

            $fieldRules = $isRequired ? ['required'] : ['nullable'];

            $fieldRules[] = $type === 'email' ? 'email' : 'string';

            $fieldRules[] = match ($type) {
                'textarea' => 'max:5000',
                'tel' => 'max:50',
                default => 'max:255',
            };

            $rules[$key] = $fieldRules;
        }

        $validated = $request->validate($rules);

        $fallbackMessage = json_encode($validated, JSON_UNESCAPED_UNICODE) ?: 'Contact form submission';

        try {
            ContactMessage::query()->create([
                'name' => (string) ($validated['name'] ?? (reset($validated) ?: 'N/A')),
                'email' => (string) ($validated['email'] ?? 'no-email@example.invalid'),
                'message' => (string) ($validated['message'] ?? $fallbackMessage),
                'payload' => $validated,
            ]);

            return response()->json([
                'ok' => true,
                'message' => $settings ? $settings->success_toast : 'Your message has been sent successfully.',
            ]);
        } catch (Throwable) {
            return response()->json([
                'ok' => false,
                'message' => $settings ? $settings->error_toast : 'Something went wrong while sending your message.',
            ], 500);
        }
    }
}
