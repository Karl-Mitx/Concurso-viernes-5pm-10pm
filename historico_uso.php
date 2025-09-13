<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'] ?? 'usuario';

require 'conexion.php'; // $mysqli

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Ejecutar consulta según rol (usamos consultas preparadas)
if ($rol === 'admin') {
    $sql = "SELECT u.nombre, u.apellido, b.id AS bicicleta_id, h.fecha_inicio, h.fecha_fin
            FROM historial_bicicletas h
            JOIN usuarios u ON h.usuario_id = u.id
            JOIN bicicletas b ON h.bicicleta_id = b.id
            ORDER BY h.fecha_inicio DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $idUsuario = (int)($usuario['id'] ?? 0);
    $sql = "SELECT b.id AS bicicleta_id, h.fecha_inicio, h.fecha_fin
            FROM historial_bicicletas h
            JOIN bicicletas b ON h.bicicleta_id = b.id
            WHERE h.usuario_id = ?
            ORDER BY h.fecha_inicio DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Histórico de uso de bicicletas</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f6fffb;padding:20px;color:#0b2f1a}
    .card{background:#fff;border-radius:8px;padding:16px;box-shadow:0 6px 18px rgba(0,0,0,.06);max-width:1000px;margin:0 auto}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:10px;border-bottom:1px solid #e6f4ea;text-align:left}
    thead th{background:#2e7d32;color:#fff}
    .small{font-size:0.9rem;color:#4a5b4a}
    .actions{margin-top:12px}
    a.btn{display:inline-block;padding:8px 12px;border-radius:8px;text-decoration:none;background:#28a745;color:#fff;margin-right:8px}
  </style>
</head>
<body>
    
  <div class="card">
    <h2>📊 Histórico de uso</h2>
    <p class="small">Bienvenido, <strong><?php echo e(trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')) ?: ($usuario['email'] ?? 'Usuario')); ?></strong></p>
<h3>➕ Registrar nuevo uso</h3>
<form method="post" action="registrar_historial.php">
    <label for="bicicleta">Selecciona bicicleta:</label>
    <label for="fecha_fin">Fecha y hora de fin:</label>
<input type="datetime-local" name="fecha_fin" id="fecha_fin">
    <select name="bicicleta_id" id="bicicleta" required>
        <?php
        // Obtener todas las bicicletas
        $resBicis = $mysqli->query("SELECT id, modelo FROM bicicletas");
        while ($bici = $resBicis->fetch_assoc()) {
            echo '<option value="'.$bici['id'].'">'.htmlspecialchars($bici['modelo']).'</option>';
        }
        ?>
    </select>
    <br><br>
    <button type="submit">🚴 Iniciar uso</button>
</form>

    <table>
      <thead>
        <tr>
          <?php if ($rol === 'admin'): ?><th>Usuario</th><?php endif; ?>
          <th>Bicicleta</th>
          <th>Fecha inicio</th>
          <th>Fecha fin</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <?php if ($rol === 'admin'): ?>
                <td><?php echo e(($row['nombre'] ?? '-') . ' ' . ($row['apellido'] ?? '')); ?></td>
              <?php endif; ?>
              <td><?php echo e($row['bicicleta_id'] ?? '-'); ?></td>
              <td><?php echo e($row['fecha_inicio'] ?? '-'); ?></td>
              <td><?php echo e(!empty($row['fecha_fin']) ? $row['fecha_fin'] : 'En uso'); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="<?php echo ($rol === 'admin') ? 4 : 3; ?>" class="small">No hay registros.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="actions">
      <a class="btn" href="principal.php">⬅️ Volver al panel</a>
    <a href="home.php" class="btn">🏠 Página Principal</a>
</div>

<style>
    .btn {
        display: inline-block;
        margin: 6px 10px;
        padding: 10px 18px;
        background: #16a085;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn:hover { background: #138d75; }
</style>
      <a class="btn" href="logout.php" style="background:#ef4444">Cerrar sesión</a>
      
    </div>
  </div>
</body>
</html>
<?php
if (isset($stmt) && $stmt) $stmt->close();
$mysqli->close();
?>