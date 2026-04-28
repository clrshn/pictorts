<?php

namespace App\Http\Controllers;

use App\Support\TableExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TableReportController extends Controller
{
    private function cacheKey(string $id): string
    {
        return 'table-report:' . $id;
    }

    public function store(Request $request)
    {
        // Report payloads are cached temporarily so the preview, print, and PDF URLs
        // can reuse the same prepared dataset without rewriting it to the database.
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'headers' => 'required|array|min:1',
            'headers.*' => 'required|string|max:255',
            'rows' => 'required|array',
            'rows.*' => 'required|array',
            'paper_size' => 'nullable|in:A4,LETTER,LEGAL',
            'orientation' => 'nullable|in:portrait,landscape',
        ]);

        $title = trim((string) ($validated['title'] ?? 'Report')) ?: 'Report';
        $headers = array_values($validated['headers']);
        $columnCount = count($headers);

        $rows = array_values(array_map(function ($row) use ($columnCount) {
            $cells = array_map(function ($value) {
                if (is_scalar($value) || $value === null) {
                    return (string) $value;
                }

                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            }, array_values($row));

            return array_slice(array_pad($cells, $columnCount, ''), 0, $columnCount);
        }, $validated['rows']));

        $paperSize = strtoupper((string) ($validated['paper_size'] ?? 'A4'));
        $orientation = strtolower((string) ($validated['orientation'] ?? 'portrait')) === 'landscape'
            ? 'landscape'
            : 'portrait';

        $reportId = (string) Str::uuid();
        Cache::put($this->cacheKey($reportId), [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
        ], now()->addMinutes(30));

        $query = [
            'report_title' => $title,
            'paper_size' => $paperSize,
            'orientation' => $orientation,
        ];

        return response()->json([
            'preview_url' => route('table-reports.show', array_merge(['report' => $reportId], $query)),
            'pdf_inline_url' => route('table-reports.show', array_merge(['report' => $reportId], $query, [
                'export' => 'pdf',
                'disposition' => 'inline',
            ])),
            'pdf_download_url' => route('table-reports.show', array_merge(['report' => $reportId], $query, [
                'export' => 'pdf',
                'disposition' => 'attachment',
            ])),
        ]);
    }

    public function show(Request $request, string $report): Response
    {
        // The same cached report can be rendered either as print preview or PDF,
        // depending on the requested output mode.
        $payload = Cache::get($this->cacheKey($report));
        abort_unless(is_array($payload), 404);

        $title = (string) ($payload['title'] ?? 'Report');
        $headers = is_array($payload['headers'] ?? null) ? $payload['headers'] : [];
        $rows = is_array($payload['rows'] ?? null) ? $payload['rows'] : [];

        if ($request->get('export') === 'pdf') {
            return TableExport::pdfTable($title, $headers, $rows);
        }

        return TableExport::printTable($title, $headers, $rows);
    }
}
