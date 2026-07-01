<?php

namespace Itech\Core;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Exportador de reportes a Excel usando PhpSpreadsheet.
 *
 * Nota: esta clase no se referencia actualmente en el código. El
 * `ReporteController` genera el Excel directamente con PhpSpreadsheet.
 * Puede eliminarse o reutilizarse para centralizar la lógica de exportación.
 */
class ExcelExporter
{
    public static function exportInscritos(array $inscritos): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inscritos iTECH');

        $headers = [
            'A1' => 'Identidad', 'B1' => 'Nombre', 'C1' => 'Apellido', 'D1' => 'Edad',
            'E1' => 'Sexo', 'F1' => 'País', 'G1' => 'Nacionalidad', 'H1' => 'Correo',
            'I1' => 'Celular', 'J1' => 'Temas de Interés', 'K1' => 'Observaciones',
            'L1' => 'Fecha de Registro', 'M1' => 'Integridad',
        ];
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = $sheet->getStyle('A1:M1');
        $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2E5AAC');
        $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row = 2;
        foreach ($inscritos as $r) {
            $sheet->setCellValue("A{$row}", $r['identidad']);
            $sheet->setCellValue("B{$row}", $r['nombre']);
            $sheet->setCellValue("C{$row}", $r['apellido']);
            $sheet->setCellValue("D{$row}", $r['edad']);
            $sheet->setCellValue("E{$row}", $r['sexo']);
            $sheet->setCellValue("F{$row}", $r['nombre_pais']);
            $sheet->setCellValue("G{$row}", $r['nacionalidad']);
            $sheet->setCellValue("H{$row}", $r['correo']);
            $sheet->setCellValue("I{$row}", $r['celular']);
            $sheet->setCellValue("J{$row}", $r['temas']);
            $sheet->setCellValue("K{$row}", $r['observaciones']);
            $sheet->setCellValue("L{$row}", $r['fecha_registro']);
            $sheet->setCellValue("M{$row}", $r['integridad_valida'] ? 'Válido' : 'Corrompido');
            $row++;
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_inscritos_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
