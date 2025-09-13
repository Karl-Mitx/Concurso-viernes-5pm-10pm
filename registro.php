<?php
require 'conexion.php'; // debe definir $mysqli y set_charset('utf8mb4')

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar
    $dpi    = trim($_POST['dpi'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'usuario';
    $foto = ''; // ruta relativa que guardaremos en BD

    // Validaciones básicas
    if ($dpi === '') $errors['dpi'] = 'DPI es requerido.';
    if ($nombre === '') $errors['nombre'] = 'Nombre es requerido.';
    if ($apellido === '') $errors['apellido'] = 'Apellido es requerido.';
    if ($fecha_nacimiento === '') $errors['fecha_nacimiento'] = 'Fecha de nacimiento es requerida.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Correo inválido.';
    if ($telefono === '') $errors['telefono'] = 'Teléfono es requerido.';
    if (strlen($password) < 6) $errors['password'] = 'Mínimo 6 caracteres.';

    // Manejo de foto (opcional)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDirAbs = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
            if (!is_dir($uploadDirAbs)) {
                // Crear carpeta /uploads
                if (!mkdir($uploadDirAbs, 0777, true) && !is_dir($uploadDirAbs)) {
                    $errors['foto'] = 'No se pudo crear la carpeta de subidas.';
                }
            }
            if (empty($errors['foto'])) {
                $tmp  = $_FILES['foto']['tmp_name'];
                $orig = $_FILES['foto']['name'];
                $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
                // Extensiones permitidas (agrega/quita según necesidad)
                $allowed = ['jpg','jpeg','png','gif','webp','svg'];
                if (!in_array($ext, $allowed, true)) {
                    $errors['foto'] = 'Formato no permitido. Usa JPG, PNG, GIF, WEBP o SVG.';
                } else {
                    // Nombre seguro
                    $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
                    $safe = $base . '' . date('Ymd_His') . '' . bin2hex(random_bytes(3)) . '.' . $ext;
                    $destAbs = $uploadDirAbs . DIRECTORY_SEPARATOR . $safe;
                    if (!move_uploaded_file($tmp, $destAbs)) {
                        $errors['foto'] = 'No se pudo guardar la imagen (ruta/permisos).';
                    } else {
                        $foto = 'uploads/' . $safe; // ruta relativa para la BD
                    }
                }
            }
        } else {
            $errors['foto'] = 'Error al subir archivo (código ' . $_FILES['foto']['error'] . ').';
        }
    }

    // Insertar si todo ok
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO usuarios 
            (dpi, nombre, apellido, foto, fecha_nacimiento, email, telefono, password, rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errors['general'] = 'Error de preparación: ' . $mysqli->error;
        } else {
            $stmt->bind_param("sssssssss", $dpi, $nombre, $apellido, $foto, $fecha_nacimiento, $email, $telefono, $hash, $rol);
            if ($stmt->execute()) {
                $success = true;
            } else {
                // 1062 = clave duplicada (email o dpi ya existe si hay índice único)
                if ($stmt->errno === 1062) {
                    $errors['general'] = 'El DPI o el correo ya está registrado.';
                } else {
                    $errors['general'] = 'Error en BD: ' . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      body{margin:0;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0b0f14;font-family:system-ui,Segoe UI,Roboto,Arial;color:#e5e7eb}
      .card{background:#151a21;border:1px solid #263041;border-radius:14px;padding:26px;width:100%;max-width:360px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
      h2{margin:0 0 16px;font-weight:700}
      form{display:grid;gap:8px}
      input,select{width:100%;padding:10px 12px;border:1px solid #2b3647;border-radius:8px;background:#0f141a;color:#e5e7eb}
      button{padding:10px 12px;border:0;border-radius:8px;background:#28a745;color:#fff;font-weight:600;cursor:pointer}
      .err{color:#ff6b6b;font-size:.85rem;margin-top:-4px}
      .ok{background:#0f5132;color:#d1e7dd;border:1px solid #0f5132;padding:10px;border-radius:8px;margin-top:10px}
      .warn{background:#3a1c1c;color:#f8d7da;border:1px solid #6b2020;padding:10px;border-radius:8px;margin-top:10px}
      a{color:#7dd3fc}
      .logo{font-size:28px;margin-bottom:6px}
    </style>
</head>
<body>
  <div class="card">
    <div class="logo">⚡</div>
    <h2>Crear cuenta</h2>

    <?php if (!empty($errors['general'])): ?>
      <div class="warn"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="ok">¡Cuenta creada! <a href="index.php">Iniciar sesión</a></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <input type="text" name="dpi" placeholder="DPI" value="<?= htmlspecialchars($_POST['dpi'] ?? '') ?>" required>
      <?php if(!empty($errors['dpi'])): ?><div class="err"><?= htmlspecialchars($errors['dpi']) ?></div><?php endif; ?>

      <input type="text" name="nombre" placeholder="Nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
      <?php if(!empty($errors['nombre'])): ?><div class="err"><?= htmlspecialchars($errors['nombre']) ?></div><?php endif; ?>

      <input type="text" name="apellido" placeholder="Apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" required>
      <?php if(!empty($errors['apellido'])): ?><div class="err"><?= htmlspecialchars($errors['apellido']) ?></div><?php endif; ?>

      <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($_POST['fecha_nacimiento'] ?? '') ?>" required>
      <?php if(!empty($errors['fecha_nacimiento'])): ?><div class="err"><?= htmlspecialchars($errors['fecha_nacimiento']) ?></div><?php endif; ?>

      <input type="email" name="email" placeholder="Correo" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      <?php if(!empty($errors['email'])): ?><div class="err"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>

      <input type="text" name="telefono" placeholder="Teléfono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" required>
      <?php if(!empty($errors['telefono'])): ?><div class="err"><?= htmlspecialchars($errors['telefono']) ?></div><?php endif; ?>

      <input type="password" name="password" placeholder="Password" required>
      <?php if(!empty($errors['password'])): ?><div class="err"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>

      <input type="file" name="foto" accept="image/*">
      <?php if(!empty($errors['foto'])): ?><div class="err"><?= htmlspecialchars($errors['foto']) ?></div><?php endif; ?>

      <select name="rol" required>
        <option value="usuario" <?= (($_POST['rol'] ?? '')==='usuario')?'selected':''; ?>>Usuario</option>
        <option value="admin"   <?= (($_POST['rol'] ?? '')==='admin')?'selected':''; ?>>Administrador</option>
      </select>

      <button type="submit">Registrar</button>
    </form>
  </div>
</body>
</html>