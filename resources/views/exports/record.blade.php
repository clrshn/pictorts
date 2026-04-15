<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> </title>
    <style>
        @page {
            size: {{ request('paper_size', 'A4') }} {{ request('orientation', 'portrait') }};
            margin: 14mm 12mm 16mm;
        }

        :root {
            --ink: #1e293b;
            --muted: #64748b;
            --line: #dbe4f0;
            --soft: #f8fafc;
            --accent: #1d4ed8;
            --accent-dark: #1e40af;
            --footer-red: #d9485f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Georgia, serif;
            margin: 0;
            color: var(--ink);
            background: #ffffff;
        }

        .page {
            max-width: 1060px;
            margin: 0 auto;
            padding: 0 4px;
        }

        .export-actions {
            margin-bottom: 18px;
            display: flex;
            gap: 10px;
        }

        .export-actions button {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            background: #8b0000;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .export-actions button.secondary {
            background: #475569;
        }

        .report-shell {
            border-top: 4px solid rgba(29, 78, 216, 0.12);
            padding-top: 6px;
        }

        .report-header {
            display: grid;
            grid-template-columns: 92px 1fr 92px;
            align-items: center;
            gap: 14px;
            padding: 6px 0 12px;
            border-bottom: 1px solid var(--line);
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-wrap img {
            width: 62px;
            height: 62px;
            object-fit: contain;
        }

        .report-heading {
            text-align: center;
        }

        .report-heading .line {
            margin: 0;
            line-height: 1.15;
        }

        .report-heading .line.top {
            font-size: 18px;
            font-weight: 700;
        }

        .report-heading .line.middle {
            margin-top: 2px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--accent-dark);
        }

        .system-title {
            margin: 14px 0 2px;
            text-align: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
        }

        .report-title {
            margin: 0 0 16px;
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--accent-dark);
        }

        .meta-card {
            margin: 0 0 18px;
            padding: 12px 16px;
            border-left: 4px solid var(--accent);
            background: linear-gradient(180deg, #f8fbff 0%, #f1f5f9 100%);
            border-radius: 8px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
            font-size: 13px;
        }

        .meta-card div {
            margin-bottom: 4px;
        }

        .meta-card div:last-child {
            margin-bottom: 0;
        }

        .section {
            margin-bottom: 18px;
            border: 1px solid #dde6f1;
            border-radius: 12px;
            overflow: hidden;
            break-inside: avoid;
        }

        .section-title {
            background: linear-gradient(180deg, #f8fafc 0%, #eef4fb 100%);
            color: #1f2937;
            padding: 11px 14px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #dde6f1;
        }

        .section-body {
            padding: 14px;
            background: #fff;
        }

        .field {
            display: grid;
            grid-template-columns: 230px 1fr;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
        }

        .field:last-child {
            border-bottom: none;
        }

        .field-label {
            font-weight: 800;
            color: #334155;
        }

        .field-value {
            color: #475569;
            white-space: pre-line;
        }

        .report-footer {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            text-align: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #64748b;
        }

        .report-footer .system {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .report-footer .details {
            margin-top: 4px;
            font-size: 12px;
        }

        .report-footer .province {
            margin-top: 6px;
            font-size: 12px;
            color: #94a3b8;
        }

        .footer-bar {
            margin-top: 18px;
            border-top: 4px solid var(--footer-red);
            border-bottom: 4px solid var(--footer-red);
            padding: 5px 0 4px;
            text-align: center;
            font-family: "Times New Roman", Georgia, serif;
        }

        .footer-bar .tagline {
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-dark);
            letter-spacing: 0.02em;
        }

        .footer-bar .contact {
            margin-top: 3px;
            font-size: 10px;
            color: #475569;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        @media print {
            body {
                margin: 0;
            }

            .export-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
    <div class="export-actions">
        <button onclick="window.print()">Print / Save PDF</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

    <div class="report-shell">
        <div class="report-header">
            <div class="logo-wrap">
                <img src="{{ asset('images/pglu-logo.png') }}" alt="PGLU Logo">
            </div>

            <div class="report-heading">
                <p class="line top">Republic of the Philippines</p>
                <p class="line top">Province of La Union</p>
                <p class="line middle">Provincial Information and Communications Technology Office</p>
            </div>

            <div class="logo-wrap">
                <img src="{{ asset('images/bagong pilipinas logo.jfif') }}" alt="Bagong Pilipinas Logo">
            </div>
        </div>

        <div class="system-title">PICTO - Records and Tracking System</div>
        <div class="report-title">{{ request('report_title', $title) }}</div>

        <div class="meta-card">
            <div><strong>Printed On:</strong> {{ now()->format('F d, Y h:i A') }}</div>
            @if(!empty($meta))
                @foreach($meta as $label => $value)
                    <div><strong>{{ $label }}:</strong> {{ $value }}</div>
                @endforeach
            @endif
        </div>

        @foreach($sections as $section)
            <div class="section">
                <div class="section-title">{{ $section['title'] }}</div>
                <div class="section-body">
                    @foreach($section['fields'] as $label => $value)
                        <div class="field">
                            <div class="field-label">{{ $label }}</div>
                            <div class="field-value">{!! nl2br(e((string) $value)) !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="report-footer">
            <div class="system">PICTO - Records and Tracking System</div>
            <div class="details">{{ request('report_title', $title) }} - Generated on {{ now()->format('F d, Y h:i A') }}</div>
            <div class="province">Province of La Union - Provincial Information and Communications Technology Office</div>
        </div>

        <div class="footer-bar">
            <div class="tagline">LA UNION: Agkaysa!</div>
            <div class="contact">(072) 888-4453, (072) 888-3608, (072) 242-5959 local 1060 to 1065 | webmaster@launion.gov.ph | www.launion.gov.ph</div>
        </div>
    </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 250);
        });
    </script>
</body>
</html>
