<?php

$registros = $registros ?? [];

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Colaboradores </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background: #eef3f8;
            color: #1f2937;
        }

        header {
            background: linear-gradient(135deg, #0f4c81, #1e88e5);
            color: white;
            padding: 28px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 30px;
        }

        header p {
            margin-top: 8px;
            font-size: 15px;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-primary {
            background: #0f4c81;
            color: white;
        }

        .btn-primary:hover {
            background: #0b3b63;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .card h2 {
            margin-top: 0;
            color: #0f4c81;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 10px;
        }

        .info {
            background: #eff6ff;
            border-left: 5px solid #1e88e5;
            padding: 12px;
            border-radius: 8px;
            color: #1e3a8a;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        thead {
            background: #0f4c81;
            color: white;
        }

        th,
        td {
            padding: 11px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        .verde {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #22c55e;
        }

        .rojo {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .azul {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .gris {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #9ca3af;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #6b7280;
            font-size: 16px;
        }

        footer {
            text-align: center;
            padding: 18px;
            background: #0f172a;
            color: white;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 24px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Reporte de Colaboradores</h1>
    <p>Verificación de integridad de datos laborales sensibles</p>
</header>

<main class="container">

    <div class="actions">
        <a href="index.php?action=form" class="btn btn-primary">
            Registrar Nuevo Colaborador
        </a>

        <a href="index.php?action=export" class="btn btn-success">
            Exportar a Excel
        </a>
    </div>

    <section class="card">
        <h2>Datos Registrados</h2>

        <div class="info">
            El distintivo verde indica que los datos sensibles del perfil laboral mantienen su integridad.
            El distintivo rojo indica que los datos pudieron ser modificados fuera del sistema.
        </div>

        <?php if (empty($registros)): ?>

            <div class="empty">
                No hay colaboradores registrados todavía.
            </div>

        <?php else: ?>

            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Identidad</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Edad</th>
                        <th>Tipo Sangre</th>
                        <th>Sexo</th>
                        <th>Nacionalidad</th>
                        <th>Ruta</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Ocupación</th>
                        <th>Planilla</th>
                        <th>Salario</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Cargo</th>
                        <th>Empleado</th>
                        <th>Motivo Baja</th>
                        <th>Integridad</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($registros as $fila): ?>
                        <tr>
                            <td><?= e($fila['id_empleado']) ?></td>
                            <td><?= e($fila['identidad']) ?></td>
                            <td><?= e($fila['nombre']) ?></td>
                            <td><?= e($fila['apellido']) ?></td>
                            <td><?= e($fila['edad']) ?></td>
                            <td><?= e($fila['tipo_sangre']) ?></td>
                            <td><?= e($fila['sexo']) ?></td>
                            <td><?= e($fila['nacionalidad']) ?></td>
                            <td><?= e($fila['ruta']) ?></td>
                            <td><?= e($fila['correo']) ?></td>
                            <td><?= e($fila['celular']) ?></td>
                            <td><?= e($fila['nombre_ocupacion']) ?></td>
                            <td><?= e($fila['nombre_planilla']) ?></td>
                            <td>$<?= number_format((float)$fila['salario'], 2) ?></td>
                            <td><?= e($fila['fecha_inicio']) ?></td>
                            <td><?= e($fila['fecha_fin'] ?: 'N/A') ?></td>

                            <td>
                                <?php if ((int)$fila['cargo_activo'] === 1): ?>
                                    <span class="badge azul">Activo</span>
                                <?php else: ?>
                                    <span class="badge gris">Histórico</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ((int)$fila['empleado_activo'] === 1): ?>
                                    <span class="badge azul">Activo</span>
                                <?php else: ?>
                                    <span class="badge gris">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= e($fila['motivo_baja'] ?: 'N/A') ?></td>

                            <td>
                                <?php if (!empty($fila['integridad_valida'])): ?>
                                    <span class="badge verde">Íntegro</span>
                                <?php else: ?>
                                    <span class="badge rojo">Alterado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </section>

</main>
<footer>
    Ian Torres - 2024. Todos los derechos reservados.
</footer>


</body>
</html>