<?php
$ocupaciones = $ocupaciones ?? [];
$planillas = $planillas ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrataciones</title>
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
            padding: 30px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
        }

        header p {
            margin-top: 8px;
            font-size: 16px;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .card h2 {
            margin-top: 0;
            color: #0f4c81;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 6px;
            color: #374151;
        }

        input,
        select,
        textarea {
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: #f8fafc;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30,136,229,0.15);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .full {
            grid-column: 1 / 3;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
        }

        .btn-primary {
            background: #0f4c81;
            color: white;
        }

        .btn-primary:hover {
            background: #0b3b63;
        }

        .btn-secondary {
            background: #16a34a;
            color: white;
        }

        .btn-secondary:hover {
            background: #15803d;
        }

        .note {
            background: #eff6ff;
            border-left: 5px solid #1e88e5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #1e3a8a;
        }

        footer {
            text-align: center;
            padding: 18px;
            background: #0f172a;
            color: white;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: 1;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Contrataciones</h1>
    <p>Formulario de registro de colaboradores y perfiles laborales</p>
</header>

<main class="container">

    <div class="note">
        Complete los datos personales del colaborador y su perfil laboral actual o histórico.
    </div>

    <form method="POST" action="index.php?action=store">

        <section class="card">
            <h2>Datos del Colaborador</h2>

            <div class="grid">
                <div class="form-group">
                    <label for="identidad">Identidad / Documento</label>
                    <input type="text" id="identidad" name="identidad" required>
                </div>

                <div class="form-group">
                    <label for="edad">Edad</label>
                    <input type="number" id="edad" name="edad" min="18" max="100" required>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>

                <div class="form-group">
                    <label for="tipo_sangre">Tipo de Sangre</label>
                    <select id="tipo_sangre" name="tipo_sangre" required>
                        <option value="">Seleccione...</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo" required>
                        <option value="">Seleccione...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nacionalidad">Nacionalidad</label>
                    <input type="text" id="nacionalidad" name="nacionalidad" required>
                </div>

                <div class="form-group">
                    <label for="ruta">Ruta del Colaborador</label>
                    <select id="ruta" name="ruta" required>
                        <option value="">Seleccione...</option>
                        <option value="Panamá Este">Panamá Este</option>
                        <option value="Panamá Oeste">Panamá Oeste</option>
                        <option value="Panamá Norte">Panamá Norte</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="card">
            <h2>Información de Contacto</h2>

            <div class="grid">
                <div class="form-group">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" required>
                </div>
            </div>
        </section>

        <section class="card">
            <h2>Perfil Laboral</h2>

            <div class="grid">
                <div class="form-group">
                    <label for="id_ocupacion">Puesto / Ocupación</label>
                    <select id="id_ocupacion" name="id_ocupacion" required>
                        <option value="">Seleccione...</option>

                        <?php foreach ($ocupaciones as $ocupacion): ?>
                            <option value="<?= htmlspecialchars((string)$ocupacion['id_ocupacion']) ?>">
                                <?= htmlspecialchars($ocupacion['nombre_ocupacion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_planilla">Tipo de Planilla</label>
                    <select id="id_planilla" name="id_planilla" required>
                        <option value="">Seleccione...</option>

                        <?php foreach ($planillas as $planilla): ?>
                            <option value="<?= htmlspecialchars((string)$planilla['id_planilla']) ?>">
                                <?= htmlspecialchars($planilla['nombre_planilla']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="salario">Salario</label>
                    <input type="number" id="salario" name="salario" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>

                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin">
                </div>

                <div class="form-group full">
                    <label for="motivo_baja">Motivo de Baja</label>
                    <textarea id="motivo_baja" name="motivo_baja" placeholder="Llenar solo si el colaborador tiene fecha de fin o baja laboral."></textarea>
                </div>
            </div>
        </section>

        <div class="actions">
            <button type="submit" class="btn btn-primary">
                Guardar Colaborador
            </button>

            <a href="index.php?action=reporte" class="btn btn-secondary">
                Ver Reporte
            </a>
        </div>

    </form>

</main>

<footer>
    Ian Torres - 2024. Todos los derechos reservados. 
    <Tel:6641>6641-4141</Tel:6641>
    Correo: ianeduardotm@gmail.com
</footer>

</body>
</html>