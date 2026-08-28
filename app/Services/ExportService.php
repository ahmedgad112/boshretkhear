<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function excel(string $title, Collection|array $rows, string $filename): StreamedResponse
    {
        $rows = collect($rows);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setTitle(mb_substr($title, 0, 31));

        if ($rows->isEmpty()) {
            $sheet->setCellValue('A1', 'لا توجد بيانات');
        } else {
            $headers = array_keys($rows->first());
            $column = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue([$column, 1], $header);
                $column++;
            }

            $rowNumber = 2;
            foreach ($rows as $row) {
                $column = 1;
                foreach ($headers as $header) {
                    $sheet->setCellValue([$column, $rowNumber], $row[$header] ?? '');
                    $column++;
                }
                $rowNumber++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function pdf(string $title, Collection|array $rows, string $filename)
    {
        $rows = collect($rows);
        $headers = $rows->isNotEmpty() ? array_keys($rows->first()) : [];

        $pdf = Pdf::loadView('exports.report', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
