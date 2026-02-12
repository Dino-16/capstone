<?php

namespace App\Exports\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait CsvExportable
{
    /**
     * Generate a StreamedResponse for CSV download.
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $filename): StreamedResponse
    {
        $headings = $this->headings();
        $rows = $this->rows();

        return new StreamedResponse(function () use ($headings, $rows) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8 compatibility with Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Write headings
            fputcsv($handle, $headings);

            // Write data rows
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Return the headings for the CSV.
     *
     * @return array
     */
    abstract public function headings(): array;

    /**
     * Return the data rows for the CSV.
     *
     * @return array
     */
    abstract public function rows(): array;
}
