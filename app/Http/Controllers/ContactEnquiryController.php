<?php

namespace App\Http\Controllers;

use App\Mail\ContactEnquirySubmitted;
use App\Models\CmsKit\Enquiry;
use App\Models\CmsKit\SiteInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactEnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'phone_dial_code' => ['required', 'string', 'regex:/^[1-9][0-9]{0,4}$/'],
            'phone_national' => ['required', 'string', 'regex:/^\d{6,13}$/'],
            'phone_country_iso2' => ['nullable', 'string', 'size:2'],
            'phone_country_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'recaptcha_token' => ['nullable', 'string'],
        ], [
            'phone_national.regex' => 'Enter a phone number of 6 to 13 digits.',
            'phone_national.required' => 'Phone number is required.',
            'phone_dial_code.required' => 'Country code is required.',
        ]);

        if (! $this->verifyRecaptchaToken($validated['recaptcha_token'] ?? null, (string) $request->ip())) {
            return response()->json([
                'ok' => false,
                'message' => __('reCAPTCHA verification failed. Please refresh and try again.'),
                'errors' => [
                    'recaptcha_token' => [__('reCAPTCHA verification failed. Please refresh and try again.')],
                ],
            ], 422);
        }

        $phone = $this->formatPhone($validated);

        $countryLabel = trim((string) ($validated['phone_country_name'] ?? ''));
        if ($countryLabel === '') {
            $countryCode = strtoupper(trim((string) ($validated['phone_country_iso2'] ?? '')));
            $countryLabel = $countryCode !== '' ? $countryCode : null;
        }

        $enquiry = Enquiry::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'company' => null,
            'country' => $countryLabel,
            'page_source' => 'Contact',
            'page_url' => $request->headers->get('referer') ?: url('/contact'),
            'message' => $validated['message'],
            'extra_fields' => array_filter([
                'enquiry_type' => 'form',
                'subject' => $validated['subject'] ?? null,
                'phone_dial_code' => $validated['phone_dial_code'] ?? null,
                'phone_national' => $validated['phone_national'] ?? null,
                'phone_country_iso2' => strtoupper(trim((string) ($validated['phone_country_iso2'] ?? ''))) ?: null,
                'phone_country_name' => $validated['phone_country_name'] ?? null,
            ], static fn ($value) => filled($value)),
        ]);

        $to = $this->recipientEmail();
        if ($to !== '') {
            try {
                Mail::to($to)->send(new ContactEnquirySubmitted($enquiry));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => __('Your enquiry has been submitted successfully.'),
        ]);
    }

    private function formatPhone(array $validated): ?string
    {
        $dial = preg_replace('/\D+/', '', (string) ($validated['phone_dial_code'] ?? ''));
        $national = preg_replace('/\D+/', '', (string) ($validated['phone_national'] ?? ''));

        if ($dial !== '' && $national !== '') {
            return '+'.$dial.$national;
        }

        $raw = trim((string) ($validated['phone'] ?? ''));

        return $raw !== '' ? $raw : null;
    }

    private function recipientEmail(): string
    {
        $configured = trim((string) config('mail.contact_enquiry_to', ''));
        if ($configured !== '') {
            return $configured;
        }

        $site = SiteInformation::query()->first();

        return trim((string) ($site?->receipt_email ?: $site?->email_1 ?: config('mail.from.address', '')));
    }

    private function verifyRecaptchaToken(?string $token, string $ip): bool
    {
        $enabled = (bool) config('services.recaptcha.enabled', false);
        $secret = trim((string) config('services.recaptcha.secret_key', ''));
        if (! $enabled || $secret === '') {
            return true;
        }

        $token = trim((string) $token);
        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            if (! $response->ok()) {
                return false;
            }

            $payload = $response->json();
            if (! is_array($payload) || ! ($payload['success'] ?? false)) {
                return false;
            }

            if (isset($payload['score']) && is_numeric($payload['score']) && (float) $payload['score'] < 0.3) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
