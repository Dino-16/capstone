<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Export data to CSV format
     */
    public static function exportToCsv(Collection $data, array $headers, string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 support in Excel
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($handle, $headers);
            
            // Add data rows
            foreach ($data as $item) {
                $row = [];
                foreach ($headers as $header) {
                    $value = $item[$header] ?? '';
                    // Handle special characters and formatting
                    $row[] = is_string($value) ? $value : (string) $value;
                }
                fputcsv($handle, $row);
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $response;
    }

    /**
     * Export data to XLS format (CSV with .xls extension for compatibility)
     */
    public static function exportToXls(Collection $data, array $headers, string $filename): StreamedResponse
    {
        // Use CSV format but with .xls extension for Excel compatibility
        return self::exportToCsv($data, $headers, str_replace('.csv', '.xls', $filename));
    }

    /**
     * Transform query results to collection with headers
     */
    public static function transformQuery($query, array $mappings): Collection
    {
        $data = $query->get();
        
        return $data->map(function ($item) use ($mappings) {
            $row = [];
            foreach ($mappings as $key => $value) {
                if (is_callable($value)) {
                    $row[$key] = $value($item);
                } else {
                    $row[$key] = $item->{$value} ?? '';
                }
            }
            return $row;
        });
    }
}
