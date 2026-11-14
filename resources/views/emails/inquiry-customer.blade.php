@php $brand = \App\Models\Setting::get('site_name', config('app.name')); @endphp
<!doctype html>
<html>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b">
  <div style="max-width:560px;margin:0 auto;padding:24px">
    <div style="background:#0b0e1f;border-radius:14px 14px 0 0;padding:28px 32px;color:#fff">
      <h1 style="margin:0;font-size:20px">{{ $brand }}</h1>
    </div>
    <div style="background:#fff;border:1px solid #e2e8f0;border-top:0;border-radius:0 0 14px 14px;padding:32px">
      <h2 style="margin:0 0 8px;font-size:20px">Thank you, {{ $inquiry->name }}!</h2>
      <p style="color:#475569;line-height:1.6">We've received your website inquiry and our team will review your requirements shortly. Here is your reference number:</p>
      <p style="font-size:22px;font-weight:bold;letter-spacing:1px;background:#f1f5f9;border-radius:10px;padding:14px;text-align:center;color:#0b0e1f">{{ $inquiry->reference }}</p>

      <table style="width:100%;border-collapse:collapse;margin:20px 0;font-size:14px">
        @if ($inquiry->package)<tr><td style="padding:6px 0;color:#64748b">Package</td><td style="padding:6px 0;font-weight:600">{{ $inquiry->package->name }}</td></tr>@endif
        @if ($inquiry->website_type)<tr><td style="padding:6px 0;color:#64748b">Website type</td><td style="padding:6px 0">{{ $inquiry->website_type }}</td></tr>@endif
        @if ($inquiry->pages_required)<tr><td style="padding:6px 0;color:#64748b">Pages</td><td style="padding:6px 0">{{ $inquiry->pages_required }}</td></tr>@endif
      </table>

      <h3 style="font-size:15px;margin:20px 0 8px">What happens next?</h3>
      <ol style="color:#475569;line-height:1.7;padding-left:18px;margin:0">
        <li>Our team reviews your requirements.</li>
        <li>We schedule a <strong>15-minute review meeting</strong> to finalise scope.</li>
        <li>You receive a tailored proposal and next steps.</li>
      </ol>

      <p style="color:#94a3b8;font-size:12px;margin-top:28px">This is an automated confirmation from {{ $brand }}. Please keep your reference number for follow-up.</p>
    </div>
  </div>
</body>
</html>
