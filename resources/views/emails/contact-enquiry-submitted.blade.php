@php
    $logoSrc = filled($logoPath ?? null) && is_file($logoPath)
        ? $message->embed($logoPath)
        : ($logoUrl ?? asset('images/logo.png'));
    $rows = [
        'Name' => $enquiry->name,
        'Email' => $enquiry->email,
        'Phone' => $enquiry->phone,
        'Country' => $enquiry->country,
        'Subject' => $enquiry->subject,
        'Submitted at' => optional($enquiry->created_at)->timezone(config('app.timezone'))->format('d M Y, h:i A'),
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Enquiry</title>
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
                            <p style="margin:0 0 8px; color:#2f5aa4; font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Contact Enquiry</p>
                            <h1 style="margin:0 0 12px; font-size:28px; line-height:1.25; font-weight:500; color:#0a1119;">New enquiry received</h1>
                            <p style="margin:0 0 28px; font-size:15px; line-height:1.7; color:#3c4a5f;">A visitor submitted the contact form on the website.</p>

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

                            <div style="margin-top:24px;">
                                <h2 style="margin:0 0 10px; font-size:16px; color:#0a1119;">Message</h2>
                                <div style="padding:16px; background:#f8fafc; border:1px solid #e6ebf2; border-radius:8px; color:#233044; font-size:14px; line-height:1.7;">
                                    {!! nl2br(e($enquiry->message)) !!}
                                </div>
                            </div>

                            <p style="margin:28px 0 0; font-size:14px; line-height:1.7; color:#3c4a5f;">
                                Thanks &amp; Regards,<br>
                                <strong style="color:#0a1119;">Distinguished Real Estate</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; background:#0a1119; color:#cbd5e1; font-size:12px; line-height:1.7;">
                            <strong style="display:block; color:#ffffff; margin-bottom:6px;">Disclaimer</strong>
                            This email was generated from the Distinguished Real Estate website contact form. The information may be confidential and intended only for the recipient. If you received this email by mistake, please delete it and notify the sender.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
