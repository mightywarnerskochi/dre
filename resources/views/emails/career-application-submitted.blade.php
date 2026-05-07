@php
    $submittedAt = optional($candidate->submitted_at)->timezone(config('app.timezone'))->format('d M Y, h:i A');
    $logoSrc = filled($logoPath ?? null) && is_file($logoPath)
        ? $message->embed($logoPath)
        : ($logoUrl ?? asset('images/logo.png'));
    $rows = [
        'Vacancy' => $candidate->apply_for,
        'Name' => $candidate->name,
        'Email' => $candidate->email,
        'Phone' => $candidate->phone,
        'Country' => $candidate->country,
        'State' => $candidate->state,
        'Place' => data_get($candidate->extra_fields, 'place'),
        'Designation' => $candidate->designation,
        'Experience' => $candidate->experience,
        'Submitted at' => $submittedAt,
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Career Application</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; color:#0a1119; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background:#ffffff; border:1px solid #e3e9f2; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background:#2f5aa4; padding:26px 32px;">
                            <img src="{{ $logoSrc }}" alt="Distinguished Real Estate" width="220" style="display:block; max-width:220px; height:auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px; color:#2f5aa4; font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Career Application</p>
                            <h1 style="margin:0 0 12px; font-size:28px; line-height:1.25; font-weight:500; color:#0a1119;">New application received</h1>
                            <p style="margin:0 0 28px; font-size:15px; line-height:1.7; color:#3c4a5f;">A candidate has submitted the career application form on the website.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; border:1px solid #e6ebf2; border-radius:8px; overflow:hidden;">
                                @foreach($rows as $label => $value)
                                    @if(filled($value))
                                        <tr>
                                            <td style="width:34%; padding:13px 16px; border-bottom:1px solid #e6ebf2; background:#f8fafc; color:#5e6b80; font-size:14px; font-weight:700;">{{ $label }}</td>
                                            <td style="padding:13px 16px; border-bottom:1px solid #e6ebf2; color:#0a1119; font-size:14px; line-height:1.5;">{{ $value }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>

                            @if(filled($candidate->additional_information))
                                <div style="margin-top:24px;">
                                    <h2 style="margin:0 0 10px; font-size:16px; color:#0a1119;">Additional information</h2>
                                    <div style="padding:16px; background:#f8fafc; border:1px solid #e6ebf2; border-radius:8px; color:#233044; font-size:14px; line-height:1.7;">
                                        {!! nl2br(e(\Illuminate\Support\Str::limit(strip_tags($candidate->additional_information), 8000))) !!}
                                    </div>
                                </div>
                            @endif

                            @if($candidate->attachment)
                                <p style="margin:24px 0 0; font-size:14px; line-height:1.7; color:#3c4a5f;">Candidate attachment is included with this email.</p>
                            @endif

                            <p style="margin:28px 0 0; font-size:14px; line-height:1.7; color:#3c4a5f;">
                                Thanks &amp; Regards,<br>
                                <strong style="color:#0a1119;">Distinguished Real Estate</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; background:#0a1119; color:#cbd5e1; font-size:12px; line-height:1.7;">
                            <strong style="display:block; color:#ffffff; margin-bottom:6px;">Disclaimer</strong>
                            This email was generated from the Distinguished Real Estate website career application form. The information may be confidential and intended only for the recipient. If you received this email by mistake, please delete it and notify the sender.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
