<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TableExport
{
    public static function csv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public static function printTable(string $title, array $headers, array $rows, array $meta = []): Response
    {
        return response()->view('exports.preview', self::buildPreviewData(
            $title,
            request()->fullUrlWithQuery(['export' => 'pdf', 'disposition' => 'inline']),
            request()->fullUrlWithQuery(['export' => 'pdf', 'disposition' => 'attachment'])
        ));
    }

    public static function pdfTable(string $title, array $headers, array $rows, array $meta = []): Response
    {
        return self::downloadPdf('exports.table', self::buildViewData($title, [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'meta' => $meta,
            'showActions' => false,
            'pdfPrintUrl' => null,
            'pdfDownloadUrl' => null,
        ]), self::pdfFilename($title));
    }

    public static function printRecord(string $title, array $sections, array $meta = []): Response
    {
        return response()->view('exports.preview', self::buildPreviewData(
            $title,
            request()->fullUrlWithQuery(['export' => 'pdf', 'disposition' => 'inline']),
            request()->fullUrlWithQuery(['export' => 'pdf', 'disposition' => 'attachment'])
        ));
    }

    public static function pdfRecord(string $title, array $sections, array $meta = []): Response
    {
        return self::downloadPdf('exports.record', self::buildViewData($title, [
            'title' => $title,
            'sections' => $sections,
            'meta' => $meta,
            'showActions' => false,
            'pdfPrintUrl' => null,
            'pdfDownloadUrl' => null,
        ]), self::pdfFilename($title));
    }

    public static function normalizeVisibleColumns(?string $visibleColumns, array $availableColumns, array $excludedColumns = []): array
    {
        $availableKeys = array_keys($availableColumns);
        $visibleKeys = $visibleColumns
            ? array_filter(array_map('trim', explode(',', $visibleColumns)))
            : $availableKeys;

        return array_values(array_filter($visibleKeys, function ($key) use ($availableColumns, $excludedColumns) {
            return array_key_exists($key, $availableColumns) && !in_array($key, $excludedColumns, true);
        }));
    }

    public static function projectRows(array $availableColumns, array $rows, array $visibleKeys): array
    {
        $headers = array_map(fn ($key) => $availableColumns[$key], $visibleKeys);
        $projectedRows = array_map(function ($row) use ($visibleKeys) {
            return array_map(fn ($key) => $row[$key] ?? '-', $visibleKeys);
        }, $rows);

        return [$headers, $projectedRows];
    }

    private static function buildViewData(string $title, array $data): array
    {
        $paperSize = strtoupper((string) request('paper_size', 'A4'));
        $orientation = strtolower((string) request('orientation', 'portrait')) === 'landscape' ? 'landscape' : 'portrait';

        return array_merge($data, [
            'reportTitle' => trim((string) request('report_title', $title)) ?: $title,
            'previewTitle' => 'PICTO Report Preview',
            'paperSize' => $paperSize,
            'orientation' => $orientation,
            'sheetWidth' => self::sheetWidth($paperSize, $orientation),
            'sheetHeight' => self::sheetHeight($paperSize, $orientation),
            'leftLogo' => self::imageDataUri(public_path('images/pglu-logo.png')),
            'rightLogo' => self::imageDataUri(public_path('images/Bagong_Pilipinas_logo.png')),
        ]);
    }

    private static function buildPreviewData(string $title, string $pdfInlineUrl, string $pdfDownloadUrl): array
    {
        $paperSize = strtoupper((string) request('paper_size', 'A4'));
        $orientation = strtolower((string) request('orientation', 'portrait')) === 'landscape' ? 'landscape' : 'portrait';

        return [
            'previewTitle' => 'PICTO Report Preview',
            'reportTitle' => trim((string) request('report_title', $title)) ?: $title,
            'paperSize' => $paperSize,
            'orientation' => $orientation,
            'sheetWidth' => self::sheetWidth($paperSize, $orientation),
            'pdfInlineUrl' => $pdfInlineUrl,
            'pdfDownloadUrl' => $pdfDownloadUrl,
        ];
    }

    private static function downloadPdf(string $view, array $data, string $filename): Response
    {
        $paperSize = strtolower((string) ($data['paperSize'] ?? 'a4'));
        $orientation = strtolower((string) ($data['orientation'] ?? 'portrait'));
        $disposition = request('disposition', 'attachment');

        $pdf = Pdf::loadView($view, $data)
            ->setPaper($paperSize, $orientation);

        return $disposition === 'inline'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    private static function pdfFilename(string $title): string
    {
        $slug = Str::slug($title ?: 'report');

        return ($slug ?: 'report') . '-' . now()->format('Y-m-d') . '.pdf';
    }

    private static function imageDataUri(string $path): ?string
    {
        if (!is_file($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg', 'jfif' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    private static function sheetWidth(string $paperSize, string $orientation): string
    {
        return match ($paperSize) {
            'LEGAL' => $orientation === 'landscape' ? '356mm' : '216mm',
            'LETTER' => $orientation === 'landscape' ? '279mm' : '216mm',
            default => $orientation === 'landscape' ? '297mm' : '210mm',
        };
    }

    private static function sheetHeight(string $paperSize, string $orientation): string
    {
        return match ($paperSize) {
            'LEGAL' => $orientation === 'landscape' ? '182mm' : '322mm',
            'LETTER' => $orientation === 'landscape' ? '182mm' : '245mm',
            default => $orientation === 'landscape' ? '176mm' : '263mm',
        };
    }
}
