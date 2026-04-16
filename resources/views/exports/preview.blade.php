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
            width: {{ $sheetWidth }};
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
            min-width: 96px;
            height: 36px;
            padding: 0 16px;
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

        .preview-action.print {
            background: #a40000;
        }

        .preview-action.close {
            background: #475569;
        }

        .preview-shell {
            width: {{ $sheetWidth }};
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cfd8e3;
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
            <a href="{{ $pdfInlineUrl }}" target="_blank" rel="noopener" class="preview-action print">Print</a>
            <button type="button" class="preview-action close" onclick="window.close()">Close</button>
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
