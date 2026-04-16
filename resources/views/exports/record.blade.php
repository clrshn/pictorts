<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $previewTitle }}</title>
    <style>
        @page {
            size: {{ $paperSize }} {{ $orientation }};
            margin: 0.5in 1.5in;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        .report-page {
            padding: 0.08in 0 0.95in;
        }

        .header-table {
            width: 86%;
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 98px;
        }

        .logo-cell.left {
            text-align: left;
        }

        .logo-cell.right {
            text-align: right;
        }

        .logo-left {
            width: 74px;
            height: auto;
            display: block;
        }

        .logo-right {
            width: 76px;
            height: auto;
            display: inline-block;
        }

        .gov-heading {
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #1f2d4a;
            line-height: 1.02;
        }

        .gov-heading p {
            margin: 0;
        }

        .gov-heading .line-1 {
            font-size: 11px;
            font-weight: 700;
        }

        .gov-heading .line-2 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
        }

        .gov-heading .line-3 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            color: #294c96;
        }

        .gov-heading .line-4 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            color: #294c96;
        }

        .title-block {
            margin: 24px 0 34px;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #17233c;
        }

        .content-block {
            width: 86%;
            margin: 0 auto;
        }

        .system-title {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .report-title {
            margin: 6px 0 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .section-box {
            margin-bottom: 16px;
            border: 1px solid #97a1ae;
        }

        .section-title {
            padding: 8px 12px;
            background: #df7275;
            color: #ffffff;
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .field-table td {
            padding: 8px 10px;
            border: 1px solid #adb7c4;
            vertical-align: top;
            font-size: 10.8px;
            line-height: 1.28;
        }

        .field-table tbody tr:nth-child(odd) td {
            background: #fbe7e5;
        }

        .field-table tbody tr:nth-child(even) td {
            background: #fff8f7;
        }

        .field-label {
            width: 34%;
            font-weight: 700;
            text-transform: uppercase;
            color: #1f2937;
            letter-spacing: 0.02em;
        }

        .field-value {
            color: #334155;
            word-break: break-word;
            white-space: pre-line;
        }

        .footer-block {
            position: fixed;
            left: 0.35in;
            right: 0.35in;
            bottom: 0.32in;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
        }

        .footer-line {
            height: 4px;
            background: #df5c70;
        }

        .footer-tagline {
            margin: 6px 0 4px;
            font-size: 15px;
            font-weight: 700;
            color: #294c96;
        }

        .footer-contact {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #475569;
            line-height: 1.25;
        }
    </style>
</head>
<body>
    <div class="report-page">
        <table class="header-table">
            <tr>
                <td class="logo-cell left">
                    @if(!empty($leftLogo))
                        <img src="{{ $leftLogo }}" alt="PGLU Logo" class="logo-left">
                    @endif
                </td>
                <td>
                    <div class="gov-heading">
                        <p class="line-1">Republic of the Philippines</p>
                        <p class="line-2">Province of La Union</p>
                        <p class="line-3">Provincial Information and</p>
                        <p class="line-4">Communications Technology Office</p>
                    </div>
                </td>
                <td class="logo-cell right">
                    @if(!empty($rightLogo))
                        <img src="{{ $rightLogo }}" alt="Bagong Pilipinas Logo" class="logo-right">
                    @endif
                </td>
            </tr>
        </table>

        <div class="title-block">
            <p class="system-title">PICTO - Records and Tracking System</p>
            <p class="report-title">{{ $reportTitle }}</p>
        </div>

        <div class="content-block">
            @foreach($sections as $section)
                <div class="section-box">
                    <div class="section-title">{{ $section['title'] }}</div>
                    <table class="field-table">
                        <tbody>
                            @foreach($section['fields'] as $label => $value)
                                <tr>
                                    <td class="field-label">{{ $label }}</td>
                                    <td class="field-value">{!! nl2br(e((string) $value)) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>

    <div class="footer-block">
        <div class="footer-line"></div>
        <div class="footer-tagline">LA UNION: Agkaysa!</div>
        <div class="footer-contact">(072) 888-4453, (072) 888-3608, (072) 242-5959 local 1060 to 1065 | webmaster@launion.gov.ph | www.launion.gov.ph</div>
        <div class="footer-line" style="margin-top: 6px;"></div>
    </div>
</body>
</html>
