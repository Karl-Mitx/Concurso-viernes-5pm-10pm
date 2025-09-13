<?php
require 'conexion.php';
session_start();

$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Ingresa un correo válido.';
    }
    if (strlen($password) < 6) {
        $passwordError = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (!$emailError && !$passwordError) {
        $stmt = $mysqli->prepare("SELECT id, dpi, nombre, apellido, foto, fecha_nacimiento, email, telefono, password, rol FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $dpi, $nombre, $apellido, $foto, $fecha_nacimiento, $emailDB, $telefono, $hash, $rol);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['usuario'] = [
                    'id' => $id,
                    'dpi' => $dpi,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'foto' => $foto,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'email' => $emailDB,
                    'telefono' => $telefono,
                    'rol' => $rol
                ];
                header('Location: principal.php');
                exit;
            } else {
                $passwordError = 'Correo o contraseña incorrectos.';
            }
        } else {
            $passwordError = 'Correo o contraseña incorrectos.';
        }
        $stmt->close();
    }
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>EcoBici • Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --bg:#0b0f14; --panel:#141a22; --ink:#e8eef6; --muted:#9aa4b2; --line:#223042;
      --brand:#28a745; --brand-2:#218838; --danger:#ef4444;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background:
        radial-gradient(1200px 600px at 20% -10%, #15202b 0, transparent 60%),
        radial-gradient(1200px 600px at 120% 10%, rgba(40,167,69,.12) 0, transparent 55%),
        var(--bg);
      font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--ink);
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
      display:flex; align-items:center; justify-content:center; padding:24px;
    }
    .card{
      width:100%; max-width:380px; background:var(--panel);
      border:1px solid var(--line); border-radius:16px; padding:24px 22px;
      box-shadow:0 20px 40px rgba(0,0,0,.35);
    }
    .logo{font-size:28px; line-height:1; margin-bottom:6px}
    h2{margin:0 0 4px; font-size:22px}
    p.sub{margin:0 0 16px; color:var(--muted)}
    form{display:grid; gap:14px}
    .group{position:relative}
    .group input{
      width:100%; padding:16px 14px 14px; border-radius:10px;
      background:#0f141a; border:1px solid var(--line); color:var(--ink);
      outline:0; font-size:15px;
    }
    .group label{
      position:absolute; left:12px; top:11px; padding:0 6px;
      background:transparent; color:#a7b6c8; font-size:14px; pointer-events:none;
      transition:.15s ease;
    }
    .group input:focus{border-color:#344a63}
    .group input:focus + label,
    .group input:not(:placeholder-shown) + label{
      top:-8px; left:10px; background:var(--panel); color:#cbd5e1; font-size:12px;
    }
    .group.error input{border-color:#7f1d1d; background:#120e10}
    .error{color:#fca5a5; font-size:12px; margin-top:6px}
    .row{display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:-4px}
    .row a{color:#90cdf4; text-decoration:none; font-size:13px}
    .btn{
      width:100%; padding:12px 14px; border:0; border-radius:10px;
      background:var(--brand); color:#fff; font-weight:700; cursor:pointer; transition:.15s;
    }
    .btn:hover{background:var(--brand-2)}
    .toggle{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      background:transparent; border:0; color:#a7b6c8; cursor:pointer; padding:6px;
    }
    .footer{margin-top:14px; text-align:center; font-size:14px; color:var(--muted)}
    .footer a{color:#90cdf4; text-decoration:none}
    .brand{font-weight:800; letter-spacing:.2px; margin-bottom:6px}
    .brand .eco{color:#fff} .brand .bici{color:var(--brand)}
  </style>
</head>
<body>
  <div class="card">
    <div class="brand"><span class="eco">Eco</span><span class="bici">Bici</span></div>
    <div class="logo">⚡</div>
    <h2>Iniciar sesión</h2>
    <p class="sub">Accede a tu cuenta</p>

    <form method="POST" novalidate>
      <!-- Email -->
      <div class="group <?php echo $emailError ? 'error' : ''; ?>">
        <input type="email" id="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" placeholder=" " required autocomplete="email">
        <label for="email">Correo</label>
        <?php if ($emailError): ?><div class="error"><?php echo e($emailError); ?></div><?php endif; ?>
      </div>

      <!-- Password -->
      <div class="group <?php echo $passwordError ? 'error' : ''; ?>" style="margin-top:-2px">
        <input type="password" id="password" name="password" placeholder=" " required autocomplete="current-password">
        <label for="password">Contraseña</label>
        <button type="button" class="toggle" aria-label="Mostrar/Ocultar" onclick="togglePwd()">👁</button>
        <?php if ($passwordError): ?><div class="error"><?php echo e($passwordError); ?></div><?php endif; ?>
      </div>

      <div class="row">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">
          <input type="checkbox" name="remember" style="accent-color:var(--brand)"> Mantener sesión
        </label>
        <a href="#" onclick="alert('Pídeselo al admin 😉');return false;">¿Olvidaste tu contraseña?</a>
      </div>

      <button class="btn" type="submit">Iniciar sesión</button>
    </form>

    <div class="footer">
      ¿Nuevo por aquí? <a href="registro.php">Crear cuenta</a>
    </div>
  </div>

  <script>
    function togglePwd(){
      const i = document.getElementById('password');
      i.type = i.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>