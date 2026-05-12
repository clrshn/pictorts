<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $previewTitle }}</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #dde6ef;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        .preview-page {
            padding: 24px 18px 30px;
        }

        .preview-actions {
            width: min({{ $sheetWidth }}, {{ $orientation === 'landscape' ? '1100px' : '760px' }});
            max-width: 100%;
            margin: 0 auto 14px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .preview-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 36px;
            padding: 0;
            border: 0;
            border-radius: 10px;
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            box-sizing: border-box;
        }

        .preview-action svg {
            width: 18px;
            height: 18px;
            display: block;
        }

        .preview-action.print {
            background: #a40000;
        }

        .preview-action.close {
            background: #475569;
        }

        .preview-action.download {
            background: #2563eb;
        }

        .preview-action.close {
            margin-left: auto;
        }

        .preview-shell {
            width: min({{ $sheetWidth }}, {{ $orientation === 'landscape' ? '1100px' : '760px' }});
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #b8c5d6;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        .preview-frame {
            display: block;
            width: 100%;
            height: {{ $orientation === 'landscape' ? '74vh' : '86vh' }};
            min-height: {{ $orientation === 'landscape' ? '620px' : '900px' }};
            border: 0;
            background: #ffffff;
        }

        @media (max-width: 900px) {
            .preview-page {
                padding: 16px 10px 22px;
            }

            .preview-frame {
                height: 82vh;
                min-height: 720px;
            }
        }
    </style>
</head>
<body>
    <div class="preview-page">
        <div class="preview-actions">
            <a href="{{ $pdfInlineUrl }}" target="_blank" rel="noopener" class="preview-action print" title="Print" aria-label="Print">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 8V4h10v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 17H5a2 2 0 0 1-2-2v-4a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v4a2 2 0 0 1-2 2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 14h10v6H7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <circle cx="17" cy="11" r="1" fill="currentColor"/>
                </svg>
            </a>
            <a href="{{ $pdfDownloadUrl }}" class="preview-action download" title="Download PDF" aria-label="Download PDF">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 4v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="m8 10 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 18h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
            <button type="button" class="preview-action close" onclick="window.close()" title="Close" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="preview-shell">
            <iframe
                class="preview-frame"
                src="{{ $pdfInlineUrl }}#toolbar=0&navpanes=0&view=FitH"
                title="{{ $reportTitle }} PDF Preview"
            ></iframe>
        </div>
    </div>
</body>
</html>
