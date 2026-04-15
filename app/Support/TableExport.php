<?php

namespace App\Support;

use Illuminate\Http\Response;
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
        return response()->view('exports.table', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'meta' => $meta,
        ]);
    }

    public static function printRecord(string $title, array $sections, array $meta = []): Response
    {
        return response()->view('exports.record', [
            'title' => $title,
            'sections' => $sections,
            'meta' => $meta,
        ]);
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
            return array_map(fn ($key) => $row[$key] ?? '—', $visibleKeys);
        }, $rows);

        return [$headers, $projectedRows];
    }
}
