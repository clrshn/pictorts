<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $previewTitle }}</title>

    <style>
       @page {
        size: {{ $paperSize }} {{ $orientation }};
       margin: 2.65in 0.75in 1.15in 0.75in;
        }

        html,
        body {
            margin: 0;
            padding-top: 2.3in;
            padding-left: 0.75in;
            padding-right: 0.75in;  
            padding-bottom: 0.60in;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }

        /* ---- Fixed header (repeats every page) ---- */
        .header-block {
            position: fixed;
            top: 0.50in;
            left: 1.0in;
            right: 0.75in;
        }

        .header-table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 90px;
        }

        .logo-cell.left {
            text-align: left;
            padding-right: 10px;
        }

        .logo-cell.right {
            text-align: right;
            padding-left: 10px;
        }

        .logo-left {
            width: 92px;
            height: auto;
        }

        .logo-right {
            width: 100px;
            height: auto;
        }

        .gov-heading {
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #1f2d4a;
            line-height: 1.0;
        }

        .gov-heading p {
            margin: 0;
        }

        .gov-heading .line-1 {
            font-size: 15px;
            font-weight: 700;
        }

        .gov-heading .line-2 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .gov-heading .line-3,
        .gov-heading .line-4 {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #294c96;
            white-space: nowrap;
        }

        .title-block {
            margin: 16px 10 10;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #17233c;
        }

        .system-title,
        .report-title {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .report-title {
            margin-top: 6px;
        }

        /* ---- Fixed footer (repeats every page) ---- */
        .footer-block {
            position: fixed;
            left: 0.35in;
            right: 0.35in;
            bottom: 0.15in;
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

        /* ---- Flowing content ---- */
        .content-block {
            width: 92%;
            margin: 0 auto;
        }

        .section-box {
            border: 1px solid #97a1ae;
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        .data-table th {
            padding: 8px 6px;
            border: 1px solid #97a1ae;
            background: #df7275;
            color: #ffffff;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.15;
        }

        .data-table td {
            padding: 8px 6px;
            border: 1px solid #adb7c4;
            vertical-align: top;
            color: #334155;
            font-size: 10.5px;
            line-height: 1.24;
            word-break: break-word;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table tbody tr:nth-child(odd) td {
            background: #fbe7e5;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fff8f7;
        }

        .no-data {
            padding: 24px 14px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }
    </style>
</head>

<body>
    @php($canRenderImages = extension_loaded('gd'))

    {{-- Fixed header — rendered once, shown on every page by DomPDF --}}
    <div class="header-block">
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
            <p class="system-title">PICTO - Records Monitoring System</p>
            <p class="report-title">{{ $reportTitle }}</p>
        </div>
    </div>

    {{-- Fixed footer — rendered once, shown on every page by DomPDF --}}
    <div class="footer-block">
        <div class="footer-line"></div>
        <div class="footer-tagline">LA UNION: Agkaysa!</div>
        <div class="footer-contact">
            (072) 888-4453, (072) 888-3608, (072) 242-5959 local 1060 to 1065 |
            webmaster@launion.gov.ph | www.launion.gov.ph
        </div>
        <div class="footer-line" style="margin-top: 6px;"></div>
    </div>

    {{-- Flowing content — fills the area between @page margins --}}
    <div class="content-block">
        <div class="section-box">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! nl2br(e((string) $cell)) !!}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="no-data">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
