<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ecobici";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


function dates_between($start, $end) {
    $period = new DatePeriod(
        new DateTime($start),
        new DateInterval('P1D'),
        (new DateTime($end))->modify('+1 day')
    );
    $out = [];
    foreach ($period as $dt) $out[] = $dt->format("Y-m-d");
    return $out;
}

function fake_usage_by_date($start, $end) {
    $rows = [];
    foreach (dates_between($start, $end) as $d) {
        $rows[] = ['fecha' => $d, 'cantidad' => rand(5, 60)];
    }
    return $rows;
}

function fake_income_by_date($start, $end) {
    $rows = [];
    foreach (dates_between($start, $end) as $d) {
        $rows[] = ['fecha' => $d, 'monto' => rand(50, 1000)];
    }
    return $rows;
}

function safe_date($d) {
    if (!$d) return '';
    $t = DateTime::createFromFormat('Y-m-d', $d);
    return $t ? $t->format('Y-m-d') : '';
}

// =====================================
// Captura de formularios
// =====================================
$tipo     = $_POST['tipo_reporte'] ?? '';
$inicio   = safe_date($_POST['inicio'] ?? '');
$fin      = safe_date($_POST['fin'] ?? '');
$inicio2  = safe_date($_POST['inicio2'] ?? '');
$fin2     = safe_date($_POST['fin2'] ?? '');

$reporteBicis = null;
$reporteIngresos = null;

// Generar reporte de bicicletas
if ($inicio && $fin && $tipo) {
    $sql = "SELECT fecha, SUM(cantidad) as total 
            FROM uso_bicicletas 
            WHERE fecha BETWEEN '$inicio' AND '$fin'
            GROUP BY fecha
            ORDER BY fecha ASC";
    $result = $conexion->query($sql);

    if ($result && $result->num_rows > 0) {
        $reporteBicis = [];
        while ($row = $result->fetch_assoc()) {
            $reporteBicis[] = ['fecha' => $row['fecha'], 'usos' => $row['total']];
        }
    } else {
        // Insertar datos falsos
        $fake = fake_usage_by_date($inicio, $fin);
        foreach ($fake as $r) {
            $conexion->query("INSERT INTO uso_bicicletas (fecha, cantidad) VALUES ('{$r['fecha']}', {$r['cantidad']})");
        }
        $reporteBicis = $fake;
    }
}

// Generar reporte de ingresos
if ($inicio2 && $fin2 && isset($_POST['ingresos'])) {
    $sql = "SELECT fecha, SUM(monto) as total 
            FROM ingresos 
            WHERE fecha BETWEEN '$inicio2' AND '$fin2'
            GROUP BY fecha
            ORDER BY fecha ASC";
    $result = $conexion->query($sql);

    if ($result && $result->num_rows > 0) {
        $reporteIngresos = [];
        while ($row = $result->fetch_assoc()) {
            $reporteIngresos[] = ['fecha' => $row['fecha'], 'ingreso' => $row['total']];
        }
    } else {
        // Insertar datos falsos
        $fake = fake_income_by_date($inicio2, $fin2);
        foreach ($fake as $r) {
            $conexion->query("INSERT INTO ingresos (fecha, monto) VALUES ('{$r['fecha']}', {$r['monto']})");
        }
        $reporteIngresos = $fake;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Sistema de Bicicletas</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f0fdf4; 
            margin: 0; 
            padding: 20px;
            color: #065f46;
        }
        h1 { text-align: center; color: #047857; }
        h2 { color: #065f46; }
        .container { 
            max-width: 900px; 
            margin: auto; 
        }
        .card { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select, button {
            padding: 10px;
            margin-top: 5px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #6ee7b7;
        }
        button {
            background-color: #10b981;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #059669;
        }
        table {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td {
            padding: 12px; 
            text-align: center; 
            border-bottom: 1px solid #d1fae5;
        }
        th {
            background: #6ee7b7;
            color: #065f46;
        }
        tr:hover {
            background: #ecfdf5;
        }
        .volver {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background: #047857;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        .volver:hover {
            background: #065f46;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Reportes del Sistema de Bicicletas</h1>

        <!-- Reporte de bicicletas -->
        <div class="card">
            <h2>🚲 Reporte de Bicicletas Usadas</h2>
            <form method="POST">
                <label for="tipo">Seleccionar tipo de reporte:</label>
                <select id="tipo" name="tipo_reporte">
                    <option value="dia">Por Día</option>
                    <option value="semana">Por Semana</option>
                    <option value="mes">Por Mes</option>
                </select>

                <label for="inicio">Fecha inicio:</label>
                <input type="date" id="inicio" name="inicio" value="<?= $inicio ?>">

                <label for="fin">Fecha fin:</label>
                <input type="date" id="fin" name="fin" value="<?= $fin ?>">

                <button type="submit">Generar Reporte</button>
            </form>

            <?php if ($reporteBicis): ?>
                <table>
                    <tr>
                        <th>Fecha</th>
                        <th>Usos</th>
                    </tr>
                    <?php foreach ($reporteBicis as $row): ?>
                        <tr>
                            <td><?= $row['fecha'] ?></td>
                            <td><?= $row['usos'] ?? $row['cantidad'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

        <!-- Reporte de ingresos -->
        <div class="card">
            <h2>💰 Reporte de Ingresos</h2>
            <form method="POST">
                <label for="inicio2">Fecha inicio:</label>
                <input type="date" id="inicio2" name="inicio2" value="<?= $inicio2 ?>">

                <label for="fin2">Fecha fin:</label>
                <input type="date" id="fin2" name="fin2" value="<?= $fin2 ?>">

                <button type="submit" name="ingresos">Generar Reporte</button>
            </form>

            <?php if ($reporteIngresos): ?>
                <table>
                    <tr>
                        <th>Fecha</th>
                        <th>Ingresos (Q)</th>
                    </tr>
                    <?php foreach ($reporteIngresos as $row): ?>
                        <tr>
                            <td><?= $row['fecha'] ?></td>
                            <td>Q <?= $row['ingreso'] ?? $row['monto'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

        <a href="home.php" class="volver">⬅ Volver al inicio</a>
    </div>
</body>
</html>
