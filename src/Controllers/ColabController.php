<?php

namespace Itech\Controllers;

use Itech\Config\Database;
use Itech\Core\IntegritySigner;
use Itech\Core\Validator;
use Itech\Core\Sanitizer;
use PDO;

class ColabController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function showForm(): void
    {
        $ocupaciones = $this->obtenerOcupaciones();
        $planillas = $this->obtenerPlanillas();

        require __DIR__ . '/../Views/colaboradores/form.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=form');
            exit;
        }
        $identidad = Sanitizer::text($_POST['identidad'] ?? '');
        $nombre = Sanitizer::title($_POST['nombre'] ?? '');
        $apellido = Sanitizer::title($_POST['apellido'] ?? '');
        $edad = Sanitizer::int($_POST['edad'] ?? 0);
        $tipoSangre = Sanitizer::text($_POST['tipo_sangre'] ?? '');
        $sexo = Sanitizer::text($_POST['sexo'] ?? '');
        $nacionalidad = Sanitizer::title($_POST['nacionalidad'] ?? '');
        $ruta = Sanitizer::text($_POST['ruta'] ?? '');
        $correo = Sanitizer::email($_POST['correo'] ?? '');
        $celular = Sanitizer::text($_POST['celular'] ?? '');

        $idOcupacion = Sanitizer::int($_POST['id_ocupacion'] ?? 0);
        $idPlanilla = Sanitizer::int($_POST['id_planilla'] ?? 0);
        $salario = Sanitizer::number($_POST['salario'] ?? 0);
        $fechaInicio = Sanitizer::date($_POST['fecha_inicio'] ?? '') ?? '';
        $fechaFin = Sanitizer::date($_POST['fecha_fin'] ?? '');
        $motivoBaja = Sanitizer::text($_POST['motivo_baja'] ?? '');
        $fechaFinBD = $fechaFin;
        $empleadoActivo = $fechaFinBD === null ? 1 : 0;
        $cargoActivo = $fechaFinBD === null ? 1 : 0;

            Validator::colaboradorPerfil([
        'identidad' => $identidad,
        'nombre' => $nombre,
        'apellido' => $apellido,
        'edad' => $edad,
        'tipo_sangre' => $tipoSangre,
        'sexo' => $sexo,
        'nacionalidad' => $nacionalidad,
        'ruta' => $ruta,
        'correo' => $correo,
        'celular' => $celular,
        'id_ocupacion' => $idOcupacion,
        'id_planilla' => $idPlanilla,
        'salario' => $salario,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFinBD,
        'motivo_baja' => $motivoBaja,
    ]);

        try {
            $this->pdo->beginTransaction();

            $colaborador = $this->buscarColaboradorPorIdentidad($identidad);

            if ($colaborador) {
                $idEmpleado = (int)$colaborador['id_empleado'];

                $this->actualizarColaborador($idEmpleado, [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'edad' => $edad,
                    'tipo_sangre' => $tipoSangre,
                    'sexo' => $sexo,
                    'nacionalidad' => $nacionalidad,
                    'ruta' => $ruta,
                    'correo' => $correo,
                    'celular' => $celular,
                    'empleado_activo' => $empleadoActivo,
                    'motivo_baja' => $fechaFinBD !== null ? $motivoBaja : null,
                ]);

                if ($cargoActivo === 1) {
                    $this->desactivarCargosAnteriores($idEmpleado);
                }
            } else {
                $idEmpleado = $this->crearColaborador([
                    'identidad' => $identidad,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'edad' => $edad,
                    'tipo_sangre' => $tipoSangre,
                    'sexo' => $sexo,
                    'nacionalidad' => $nacionalidad,
                    'ruta' => $ruta,
                    'correo' => $correo,
                    'celular' => $celular,
                    'empleado_activo' => $empleadoActivo,
                    'motivo_baja' => $fechaFinBD !== null ? $motivoBaja : null,
                ]);
            }

            $datosFirma = [
                'salario' => number_format($salario, 2, '.', ''),
                'id_empleado' => $idEmpleado,
                'id_planilla' => $idPlanilla,
                'id_ocupacion' => $idOcupacion,
                'fecha_inicio' => $fechaInicio,
            ];

            $firma = IntegritySigner::sign($datosFirma);

            $this->crearPerfilLaboral([
                'id_empleado' => $idEmpleado,
                'id_ocupacion' => $idOcupacion,
                'id_planilla' => $idPlanilla,
                'salario' => number_format($salario, 2, '.', ''),
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFinBD,
                'cargo_activo' => $cargoActivo,
                'firma_integridad' => $firma,
            ]);

            $this->pdo->commit();

            header('Location: index.php?action=reporte');
            exit;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new \RuntimeException('No fue posible guardar el colaborador. Detalle: ' . $e->getMessage());
        }
    }

    private function obtenerOcupaciones(): array
    {
        $stmt = $this->pdo->query("
            SELECT id_ocupacion, nombre_ocupacion
            FROM cat_ocupaciones
            ORDER BY nombre_ocupacion ASC
        ");

        return $stmt->fetchAll();
    }

    private function obtenerPlanillas(): array
    {
        $stmt = $this->pdo->query("
            SELECT id_planilla, nombre_planilla
            FROM cat_tipos_planilla
            ORDER BY id_planilla ASC
        ");

        return $stmt->fetchAll();
    }

    private function buscarColaboradorPorIdentidad(string $identidad): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id_empleado, identidad, correo
            FROM colaboradores
            WHERE identidad = :identidad
            LIMIT 1
        ");

        $stmt->execute([
            ':identidad' => $identidad,
        ]);

        $resultado = $stmt->fetch();

        return $resultado ?: null;
    }

    private function crearColaborador(array $data): int
    {
        $sql = "
            INSERT INTO colaboradores (
                identidad,
                nombre,
                apellido,
                edad,
                tipo_sangre,
                sexo,
                nacionalidad,
                ruta,
                correo,
                celular,
                empleado_activo,
                motivo_baja
            ) VALUES (
                :identidad,
                :nombre,
                :apellido,
                :edad,
                :tipo_sangre,
                :sexo,
                :nacionalidad,
                :ruta,
                :correo,
                :celular,
                :empleado_activo,
                :motivo_baja
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':identidad' => $data['identidad'],
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':edad' => $data['edad'],
            ':tipo_sangre' => $data['tipo_sangre'],
            ':sexo' => $data['sexo'],
            ':nacionalidad' => $data['nacionalidad'],
            ':ruta' => $data['ruta'],
            ':correo' => $data['correo'],
            ':celular' => $data['celular'],
            ':empleado_activo' => $data['empleado_activo'],
            ':motivo_baja' => $data['motivo_baja'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    private function actualizarColaborador(int $idEmpleado, array $data): void
    {
        $sql = "
            UPDATE colaboradores
            SET
                nombre = :nombre,
                apellido = :apellido,
                edad = :edad,
                tipo_sangre = :tipo_sangre,
                sexo = :sexo,
                nacionalidad = :nacionalidad,
                ruta = :ruta,
                correo = :correo,
                celular = :celular,
                empleado_activo = :empleado_activo,
                motivo_baja = :motivo_baja
            WHERE id_empleado = :id_empleado
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':edad' => $data['edad'],
            ':tipo_sangre' => $data['tipo_sangre'],
            ':sexo' => $data['sexo'],
            ':nacionalidad' => $data['nacionalidad'],
            ':ruta' => $data['ruta'],
            ':correo' => $data['correo'],
            ':celular' => $data['celular'],
            ':empleado_activo' => $data['empleado_activo'],
            ':motivo_baja' => $data['motivo_baja'],
            ':id_empleado' => $idEmpleado,
        ]);
    }

    private function desactivarCargosAnteriores(int $idEmpleado): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE perfiles_laborales
            SET cargo_activo = 0
            WHERE id_empleado = :id_empleado
              AND cargo_activo = 1
        ");

        $stmt->execute([
            ':id_empleado' => $idEmpleado,
        ]);
    }

    private function crearPerfilLaboral(array $data): void
    {
        $sql = "
            INSERT INTO perfiles_laborales (
                id_empleado,
                id_ocupacion,
                id_planilla,
                salario,
                fecha_inicio,
                fecha_fin,
                cargo_activo,
                firma_integridad
            ) VALUES (
                :id_empleado,
                :id_ocupacion,
                :id_planilla,
                :salario,
                :fecha_inicio,
                :fecha_fin,
                :cargo_activo,
                :firma_integridad
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id_empleado' => $data['id_empleado'],
            ':id_ocupacion' => $data['id_ocupacion'],
            ':id_planilla' => $data['id_planilla'],
            ':salario' => $data['salario'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin' => $data['fecha_fin'],
            ':cargo_activo' => $data['cargo_activo'],
            ':firma_integridad' => $data['firma_integridad'],
        ]);
    }
}