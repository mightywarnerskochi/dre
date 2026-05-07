<?php

namespace App\Http\Controllers;

use App\Mail\CareerApplicationSubmitted;
use App\Models\CmsKit\CareerCandidate;
use App\Models\CmsKit\SiteInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CareerApplicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'phone_dial_code' => ['required', 'string', 'regex:/^[1-9][0-9]{0,4}$/'],
            'phone_national' => ['required', 'string', 'regex:/^\d{6,13}$/'],
            'country' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'place' => ['required', 'string', 'max:255'],
            'apply_for' => ['required', 'string', 'max:500'],
            'designation' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string', 'max:255'],
            'additional_information' => [
                'nullable',
                'string',
                'max:65535',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $text = trim(strip_tags((string) $value));
                    if ($text === '') {
                        return;
                    }
                    $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) > 1000) {
                        $fail(__('Additional information must be at most 1000 words.'));
                    }
                },
            ],
            'privacy' => ['accepted'],
            'career_slug' => ['nullable', 'string', 'max:255'],
            'recaptcha_token' => ['nullable', 'string'],
            'attachment' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,ps,jpg,jpeg',
            ],
        ], [
            'phone_national.regex' => 'Enter a phone number of 6 to 13 digits (without country code).',
            'privacy.accepted' => 'You must agree to the privacy policy.',
            'attachment.required' => 'Please attach your CV or supporting document.',
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

        $e164Phone = '+'.$validated['phone_dial_code'].preg_replace('/\D+/', '', $validated['phone_national']);

        $path = $request->file('attachment')->store('career-candidates', 'public');

        $candidate = CareerCandidate::query()->create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'phone' => $e164Phone,
            'country' => $validated['country'],
            'state' => $validated['state'],
            'apply_for' => $validated['apply_for'],
            'experience' => $validated['experience'],
            'designation' => $validated['designation'],
            'additional_information' => $validated['additional_information'] ?? null,
            'attachment' => $path,
            'privacy' => true,
            'submitted_at' => now(),
            'extra_fields' => array_filter([
                'place' => $validated['place'],
                'career_slug' => $validated['career_slug'] ?? null,
            ]),
        ]);

        $adminTo = trim((string) config('mail.career_application_to', ''));

        if ($adminTo === '') {
            $site = SiteInformation::query()->first();
            $adminTo = trim((string) ($site?->email_1 ?? ''));
        }

        if ($adminTo === '') {
            $adminTo = trim((string) config('mail.from.address', ''));
        }

        if ($adminTo !== '') {
            try {
                Mail::to($adminTo)->send(new CareerApplicationSubmitted($candidate));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => __('Your application has been submitted successfully.'),
        ]);
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

            // For v3 tokens Google returns score; reject very low confidence.
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
