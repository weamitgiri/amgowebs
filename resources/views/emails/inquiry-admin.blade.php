<!doctype html>
<html>
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b">
  <div style="max-width:620px;margin:0 auto;padding:24px">
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:28px">
      <h2 style="margin:0 0 4px;font-size:18px">New website inquiry</h2>
      <p style="margin:0 0 16px;color:#64748b;font-size:13px">Reference <strong>{{ $inquiry->reference }}</strong> · {{ $inquiry->created_at->format('M j, Y g:i A') }}</p>

      <table style="width:100%;border-collapse:collapse;font-size:14px">
        @foreach ([
          'Name' => $inquiry->name,
          'Business' => $inquiry->business_name,
          'Email' => $inquiry->email,
          'Phone' => $inquiry->phone,
          'Country' => $inquiry->country,
          'Package' => $inquiry->package?->name,
          'Website type' => $inquiry->website_type,
          'Pages' => $inquiry->pages_required,
          'Existing site' => $inquiry->existing_url,
          'Budget' => $inquiry->budget,
          'Preferred meeting' => $inquiry->preferred_meeting_at?->format('M j, Y g:i A'),
        ] as $label => $value)
          @if ($value)
            <tr><td style="padding:7px 10px 7px 0;color:#64748b;white-space:nowrap;vertical-align:top">{{ $label }}</td><td style="padding:7px 0;border-bottom:1px solid #f1f5f9">{{ $value }}</td></tr>
          @endif
        @endforeach
      </table>

      <h3 style="font-size:14px;margin:18px 0 6px">Project description</h3>
      <p style="color:#334155;line-height:1.6;white-space:pre-line">{{ $inquiry->description }}</p>
      @if ($inquiry->required_features)
        <h3 style="font-size:14px;margin:18px 0 6px">Required features</h3>
        <p style="color:#334155;line-height:1.6;white-space:pre-line">{{ $inquiry->required_features }}</p>
      @endif
    </div>
  </div>
</body>
</html>
