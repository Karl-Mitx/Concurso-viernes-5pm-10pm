<?php
// comprar_membresia.php
require 'helpers.php';        // debe iniciar sesión y requerir conexion.php
$u = usuarioActual();
if (!$u) { header('Location: index.php'); exit; }

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // excepciones en mysqli
$mysqli->set_charset('utf8mb4');
date_default_timezone_set('America/Guatemala');

$ok = $err = '';

// Cargar membresía existente
function getMembresiaByUser(mysqli $mysqli, int $uid) {
  $stmt = $mysqli->prepare(
    "SELECT usuario_id, tipo, inicio, fin, estado, nombres, apellidos, correo, telefono,
            direccion, ciudad, region, pais, codigo_postal, colonia
     FROM membresias WHERE usuario_id=? LIMIT 1"
  );
  $stmt->bind_param('i', $uid);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc() ?: null;
  $stmt->close();
  return $row;
}

$membresia = getMembresiaByUser($mysqli, (int)$u['id']);

// Procesar compra/renovación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Campos de formulario
    $tipo = $_POST['tipo'] ?? 'mensual';
    if (!in_array($tipo, ['mensual','anual'], true)) $tipo = 'mensual';

    $nombres  = trim($_POST['nombres']  ?? ($u['nombre']   ?? ''));
    $apellidos= trim($_POST['apellidos']?? ($u['apellido'] ?? ''));
    $correo   = trim($_POST['correo']   ?? ($u['email']    ?? ''));
    $telefono = trim($_POST['telefono'] ?? ($u['telefono'] ?? ''));
    $direccion= trim($_POST['direccion']?? '');
    $colonia  = trim($_POST['colonia']  ?? '');
    $ciudad   = trim($_POST['ciudad']   ?? '');
    $region   = trim($_POST['region']   ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $pais     = trim($_POST['pais']     ?? '');

    // Validaciones mínimas
    if ($nombres === '' || $apellidos === '') throw new Exception('Por favor, ingresa tu nombre y apellido.');
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) throw new Exception('Correo inválido.');
    if ($telefono === '') throw new Exception('Teléfono requerido.');
    if ($ciudad === '' || $region === '' || $pais === '') throw new Exception('Ciudad, región y país son obligatorios.');

    // Fechas
    $inicio = new DateTime('today');
    $fin    = (clone $inicio);
    $fin->modify($tipo === 'anual' ? '+1 year' : '+1 month');
    $inicioStr = $inicio->format('Y-m-d');
    $finStr    = $fin->format('Y-m-d');

    // Insert/Update idempotente (requiere UNIQUE (usuario_id) en membresias)
    $sql = "
      INSERT INTO membresias
        (usuario_id, tipo, inicio, fin, estado, nombres, apellidos, correo, telefono,
         direccion, ciudad, region, pais, codigo_postal, colonia)
      VALUES
        (?, ?, ?, ?, 'activa', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE
        tipo=VALUES(tipo),
        inicio=VALUES(inicio),
        fin=VALUES(fin),
        estado='activa',
        nombres=VALUES(nombres),
        apellidos=VALUES(apellidos),
        correo=VALUES(correo),
        telefono=VALUES(telefono),
        direccion=VALUES(direccion),
        ciudad=VALUES(ciudad),
        region=VALUES(region),
        pais=VALUES(pais),
        codigo_postal=VALUES(codigo_postal),
        colonia=VALUES(colonia)
    ";
    $stmt = $mysqli->prepare($sql);
    $uid = (int)$u['id'];

    // i + 13 s = 14 parámetros (coinciden con los ? del INSERT)
    $stmt->bind_param(
      'isssssssssssss',
      $uid, $tipo, $inicioStr, $finStr,
      $nombres, $apellidos, $correo, $telefono,
      $direccion, $ciudad, $region, $pais, $codigo_postal, $colonia
    );

    $stmt->execute();
    $stmt->close();

    $ok = '¡Membresía activada/renovada correctamente!';
    $membresia = getMembresiaByUser($mysqli, $uid); // refrescar
  } catch (mysqli_sql_exception $e) {
    // Error real de MySQL (útil mientras desarrollas)
    $err = 'Error de base de datos: ' . $e->getMessage();
  } catch (Exception $e) {
    $err = $e->getMessage();
  }
}

// Estado vigente para UI
$vigente = null;
if ($membresia && $membresia['estado'] === 'activa' && !empty($membresia['fin'])) {
  try {
    $hoy = new DateTime('today');
    $fin = new DateTime($membresia['fin']);
    if ($fin >= $hoy) $vigente = $membresia;
  } catch (Exception $e) { /* noop */ }
}

// Prefill
$pref = [
  'nombres' => $membresia['nombres']   ?? ($u['nombre']   ?? ''),
  'apellidos' => $membresia['apellidos'] ?? ($u['apellido'] ?? ''),
  'correo' => $membresia['correo']     ?? ($u['email']    ?? ''),
  'telefono' => $membresia['telefono'] ?? ($u['telefono'] ?? ''),
  'direccion' => $membresia['direccion'] ?? '',
  'colonia' => $membresia['colonia']   ?? '',
  'ciudad' => $membresia['ciudad']     ?? '',
  'region' => $membresia['region']     ?? '',
  'codigo_postal' => $membresia['codigo_postal'] ?? '',
  'pais' => $membresia['pais']         ?? ''
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulario de Compra - EcoBici</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    :root{ --eco:#28a745; --eco-dark:#218838; --ink:#1d2632; --panel:#ffffff; --line:#e5efe7; }
    body{ min-height:100vh; margin:0; font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      display:flex; align-items:center; justify-content:center; padding:24px; color:var(--ink);
      background: radial-gradient(1200px 600px at 10% -10%, rgba(40,167,69,.12) 0, transparent 60%),
                 radial-gradient(1200px 600px at 120% 10%, rgba(33,136,56,.10) 0, transparent 55%), #f4f8f6; }
    .eco-card{ background:var(--panel); width:100%; max-width:920px; border-radius:18px; border:1px solid var(--line);
      box-shadow:0 18px 40px rgba(0,0,0,.08); overflow:hidden; }
    .eco-header{ padding:22px 26px; background:linear-gradient(135deg, #e9f7ef, #f6fffb); border-bottom:1px solid var(--line); }
    .brand{ display:flex; align-items:center; gap:12px; margin-bottom:6px; }
    .brand-badge{ width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, var(--eco), #6ee7b7);
      display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; box-shadow:0 10px 24px rgba(40,167,69,.35); }
    .brand-title{ font-weight:800; font-size:22px; letter-spacing:.2px; color:#103018; }
    .brand-title .bici{ color:var(--eco); }
    .tagline{ color:#4a6a58; margin:0 }
    .leaf-divider{ height:12px; margin-top:10px;
      background:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='12' viewBox='0 0 240 12'><path d='M0 6h100' stroke='%23b8e3c9' stroke-width='2' stroke-linecap='round'/><path d='M140 6h100' stroke='%23b8e3c9' stroke-width='2' stroke-linecap='round'/><path d='M120 6c7 0 10-6 10-6s-3 12-10 12s-10-12-10-12s3 6 10 6z' fill='%2328a745'/></svg>") center/contain no-repeat; }
    .eco-body{ padding:26px; }
    h1{ font-size:26px; color:var(--eco); font-weight:800; text-align:center; margin:0 0 6px; }
    .sub{ text-align:center; color:#5c6f66; margin-bottom:18px; }
    .order-form-label{ font-weight:700; color:#234436; margin-bottom:6px; }
    .order-form-input.form-control{ border:1px solid #cfead8; border-radius:10px; padding:12px 14px; font-size:16px; background:#fbfffd;
      transition:.18s ease; }
    .order-form-input.form-control:focus{ border-color:var(--eco); box-shadow:0 0 0 4px rgba(40,167,69,.15); }
    .btn-submit{ border:none; border-radius:12px; padding:12px 28px; font-weight:800; letter-spacing:.2px;
      box-shadow:0 10px 20px rgba(40,167,69,.20); transition:.15s ease; outline:0; }
    .btn-success.btn-submit{ background:linear-gradient(135deg, var(--eco), var(--eco-dark)); }
    .btn-success.btn-submit:hover{ transform:translateY(-1px); }
    .btn-secondary.btn-submit{ background:#e7f5ee; color:#17412b; box-shadow:none; border:1px solid #cfead8; }
    .btn-secondary.btn-submit:hover{ background:#dff1e7; }
    .hr-soft{ border-top:1px dashed #cfead8; margin:14px 0 2px; }
    .alert-ok{background:#e9f7ef;border:1px solid #b7e4c7;color:#0f5132;border-radius:12px;padding:10px 12px;margin-bottom:12px;font-weight:600}
    .alert-err{background:#fde2e1;border:1px solid #f5b5b2;color:#842029;border-radius:12px;padding:10px 12px;margin-bottom:12px;font-weight:600}
    .status-pills{display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;border:1px solid #cfead8;background:#fbfffd;color:#17412b;font-weight:700;font-size:13px}
    .pill.ok{border-color:#bbf7d0;background:#ecfdf5;color:#14532d}
    .pill.bad{border-color:#fecaca;background:#fef2f2;color:#7f1d1d}
  </style>
</head>
<body>

  <section class="eco-card">
    <div class="eco-header">
      <div class="brand">
        <div class="brand-badge">🌿</div>
        <div class="brand-title"><span class="eco">Eco</span><span class="bici">Bici</span> • Membresía</div>
      </div>
      <p class="tagline">Muévete limpio, respira mejor. Con tu membresía apoyas <b>+ciclovías</b> y <b>–CO₂</b>.</p>
      <div class="leaf-divider"></div>
    </div>

    <div class="eco-body">
      <div class="text-center">
        <h1>Formulario de Compra</h1>
        <div class="sub">Completa tus datos para adquirir o renovar tu membresía EcoBici</div>
      </div>

      <div class="status-pills">
        <span class="pill <?= $vigente ? 'ok':'bad' ?>"><b>Estado:</b> <?= $vigente ? 'activa ✅' : 'inactiva ❌' ?></span>
        <?php if($membresia && !empty($membresia['fin'])): ?>
          <span class="pill"><b>Vence:</b> <?= htmlspecialchars($membresia['fin']) ?></span>
        <?php endif; ?>
        <span class="pill"><b>Usuario:</b> <?= htmlspecialchars($u['nombre'].' '.$u['apellido']) ?></span>
      </div>

      <?php if($ok): ?><div class="alert-ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
      <?php if($err): ?><div class="alert-err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

      <hr class="hr-soft"/>

      <form action="" method="POST" novalidate>
        <div class="form-row mx-1">
          <div class="col-12 mb-2">
            <label class="order-form-label">Tipo de membresía</label>
            <select name="tipo" class="custom-select" required>
              <option value="anual"   <?= (($membresia['tipo'] ?? '')==='anual')?'selected':''; ?>>Anual</option>
              <option value="mensual" <?= (($membresia['tipo'] ?? '')==='mensual')?'selected':''; ?>>Mensual</option>
            </select>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2"><label class="order-form-label">Nombre</label></div>
          <div class="col-sm-6 mb-3">
            <input type="text" name="nombres" class="form-control order-form-input" placeholder="Nombres" value="<?= htmlspecialchars($pref['nombres']) ?>" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" name="apellidos" class="form-control order-form-input" placeholder="Apellidos" value="<?= htmlspecialchars($pref['apellidos']) ?>" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2"><label class="order-form-label">Correo electrónico</label></div>
          <div class="col-12 mb-3">
            <input type="email" name="correo" class="form-control order-form-input" placeholder="ejemplo@correo.com" value="<?= htmlspecialchars($pref['correo']) ?>" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2"><label class="order-form-label">Teléfono</label></div>
          <div class="col-12 mb-3">
            <input type="tel" name="telefono" class="form-control order-form-input" placeholder="+502 0000 0000" value="<?= htmlspecialchars($pref['telefono']) ?>" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2"><label class="order-form-label">Dirección</label></div>
          <div class="col-12 mb-3">
            <input type="text" name="direccion" class="form-control order-form-input" placeholder="Calle y número" value="<?= htmlspecialchars($pref['direccion']) ?>" required>
          </div>
          <div class="col-12 mb-3">
            <input type="text" name="colonia" class="form-control order-form-input" placeholder="Colonia o zona" value="<?= htmlspecialchars($pref['colonia']) ?>">
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-sm-6 mb-3">
            <input type="text" name="ciudad" class="form-control order-form-input" placeholder="Ciudad" value="<?= htmlspecialchars($pref['ciudad']) ?>" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" name="region" class="form-control order-form-input" placeholder="Región/Departamento" value="<?= htmlspecialchars($pref['region']) ?>" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" name="codigo_postal" class="form-control order-form-input" placeholder="Código Postal" value="<?= htmlspecialchars($pref['codigo_postal']) ?>">
          </div>
          <div class="col-sm-6 mb-4">
            <input type="text" name="pais" class="form-control order-form-input" placeholder="País" value="<?= htmlspecialchars($pref['pais']) ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="col-sm-6 text-center mb-2 mb-sm-0">
            <button type="button" class="btn btn-secondary btn-submit" onclick="window.location.href='home.php'">Volver</button>
          </div>
          <div class="col-sm-6 text-center">
            <button type="submit" class="btn btn-success btn-submit">Comprar 🌱</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>