<?php

namespace Itech\Controllers;

use Itech\Config\Database;
use Itech\Core\IntegritySigner;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PDO;

class ReporteController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function index(): void
    {
        $registros = $this->obtenerRegistros();

        foreach ($registros as &$fila) {
            $fila['integridad_valida'] = IntegritySigner::verify([
                'salario' => number_format((float)$fila['salario'], 2, '.', ''),
                'id_empleado' => $fila['id_empleado'],
                'id_planilla' => $fila['id_planilla'],
                'id_ocupacion' => $fila['id_ocupacion'],
                'fecha_inicio' => $fila['fecha_inicio'],
            ], $fila['firma_integridad']);
        }

        unset($fila);

        require __DIR__ . '/../Views/reportes/index.php';
    }

    public function exportExcel(): void
    {
        $registros = $this->obtenerRegistros();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Reporte Colaboradores');

        $headers = [
            'Código Empleado',
            'Identidad',
            'Nombre',
            'Apellido',
            'Edad',
            'Tipo de Sangre',
            'Sexo',
            'Nacionalidad',
            'Ruta',
            'Correo',
            'Celular',
            'Empleado Activo',
            'Motivo Baja',
            'Ocupación',
            'Planilla',
            'Salario',
            'Fecha Inicio',
            'Fecha Fin',
            'Cargo Activo',
            'Integridad',
        ];

        $col = 1;

        foreach ($headers as $header) {
            $sheet->setCellValue([$col, 1], $header);
            $col++;
        }

        $row = 2;

        foreach ($registros as $fila) {
            $integridadValida = IntegritySigner::verify([
                'salario' => number_format((float)$fila['salario'], 2, '.', ''),
                'id_empleado' => $fila['id_empleado'],
                'id_planilla' => $fila['id_planilla'],
                'id_ocupacion' => $fila['id_ocupacion'],
                'fecha_inicio' => $fila['fecha_inicio'],
            ], $fila['firma_integridad']);

            $sheet->setCellValue([1, $row], $fila['id_empleado']);
            $sheet->setCellValue([2, $row], $fila['identidad']);
            $sheet->setCellValue([3, $row], $fila['nombre']);
            $sheet->setCellValue([4, $row], $fila['apellido']);
            $sheet->setCellValue([5, $row], $fila['edad']);
            $sheet->setCellValue([6, $row], $fila['tipo_sangre']);
            $sheet->setCellValue([7, $row], $fila['sexo']);
            $sheet->setCellValue([8, $row], $fila['nacionalidad']);
            $sheet->setCellValue([9, $row], $fila['ruta']);
            $sheet->setCellValue([10, $row], $fila['correo']);
            $sheet->setCellValue([11, $row], $fila['celular']);
            $sheet->setCellValue([12, $row], (int)$fila['empleado_activo'] === 1 ? 'Activo' : 'Inactivo');
            $sheet->setCellValue([13, $row], $fila['motivo_baja'] ?: 'N/A');
            $sheet->setCellValue([14, $row], $fila['nombre_ocupacion']);
            $sheet->setCellValue([15, $row], $fila['nombre_planilla']);
            $sheet->setCellValue([16, $row], number_format((float)$fila['salario'], 2, '.', ''));
            $sheet->setCellValue([17, $row], $fila['fecha_inicio']);
            $sheet->setCellValue([18, $row], $fila['fecha_fin'] ?: 'N/A');
            $sheet->setCellValue([19, $row], (int)$fila['cargo_activo'] === 1 ? 'Activo' : 'Histórico');
            $sheet->setCellValue([20, $row], $integridadValida ? 'Válido' : 'Alterado');

            $row++;
        }

        foreach (range('A', 'T') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'reporte_colaboradores.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function obtenerRegistros(): array
    {
        $sql = "
            SELECT
                c.id_empleado,
                c.identidad,
                c.nombre,
                c.apellido,
                c.edad,
                c.tipo_sangre,
                c.sexo,
                c.nacionalidad,
                c.ruta,
                c.correo,
                c.celular,
                c.empleado_activo,
                c.motivo_baja,

                p.id_perfil,
                p.id_ocupacion,
                p.id_planilla,
                p.salario,
                p.fecha_inicio,
                p.fecha_fin,
                p.cargo_activo,
                p.firma_integridad,

                o.nombre_ocupacion,
                tp.nombre_planilla

            FROM perfiles_laborales p
            INNER JOIN colaboradores c
                ON p.id_empleado = c.id_empleado
            INNER JOIN cat_ocupaciones o
                ON p.id_ocupacion = o.id_ocupacion
            INNER JOIN cat_tipos_planilla tp
                ON p.id_planilla = tp.id_planilla
            ORDER BY c.id_empleado DESC, p.fecha_inicio DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }
}