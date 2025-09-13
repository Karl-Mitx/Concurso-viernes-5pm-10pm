<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
$usuario = $_SESSION['usuario'];

// Utilidades
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$nombreCompleto = trim(($usuario['nombre'] ?? '').' '.($usuario['apellido'] ?? ''));
$rol = $usuario['rol'] ?? 'usuario';
$foto = $usuario['foto'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Panel • EcoBici</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root{
      --bg:#0b0f14; --panel:#141a22; --muted:#9aa4b2; --ink:#e8eef6;
      --line:#223042; --brand:#28a745; --brand-2:#218838; --danger:#ef4444;
      --chip:#1f2937; --chip-line:#2b3647;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:radial-gradient(1200px 600px at 20% -10%, #15202b 0, transparent 60%),
                 radial-gradient(1200px 600px at 120% 10%, rgba(40,167,69,.15) 0, transparent 55%),
                 var(--bg);
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--ink);
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    .wrap{max-width:1100px;margin:0 auto;padding:24px}
    .topbar{
      display:flex;align-items:center;gap:12px;margin-bottom:18px
    }
    .brand{font-weight:800;letter-spacing:.2px}
    .brand .eco{color:var(--ink)} .brand .bici{color:var(--brand)}
    .spacer{flex:1}
    .btn{
      display:inline-flex;align-items:center;justify-content:center;gap:8px;
      border:1px solid var(--line); background:#0f141a; color:var(--ink);
      padding:10px 14px; border-radius:10px; text-decoration:none; cursor:pointer;
      transition:.18s ease; font-weight:600
    }
    .btn:hover{border-color:#344a63}
    .btn.brand{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn.brand:hover{background:var(--brand-2);border-color:var(--brand-2)}
    .btn.ghost{background:transparent}
    .btn.danger{border-color:#7f1d1d;color:#fca5a5;background:#1b0f10}
    .grid{
      display:grid; gap:18px;
      grid-template-columns: 360px 1fr;
    }
    @media (max-width: 900px){ .grid{grid-template-columns:1fr} }

    .card{
      background:var(--panel); border:1px solid var(--line); border-radius:16px;
      padding:20px 20px; box-shadow:0 10px 30px rgba(0,0,0,.35);
    }
    .card h3{margin:0 0 12px; font-size:18px}
    .profile{
      display:flex; flex-direction:column; align-items:center; text-align:center; gap:14px;
    }
    .avatar{
      width:120px; height:120px; border-radius:50%; overflow:hidden;
      border:3px solid rgba(40,167,69,.35); box-shadow:0 0 0 6px rgba(40,167,69,.08);
      background:#0f141a; display:flex; align-items:center; justify-content:center;
    }
    .avatar img{width:100%; height:100%; object-fit:cover; display:block}
    .avatar svg{width:56px;height:56px;opacity:.65}
    .who{font-weight:800; font-size:20px}
    .role{
      display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px;
      background:var(--chip); border:1px solid var(--chip-line); color:#cde9d6; font-size:12px;
    }

    .info{
      display:grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap:12px;
    }
    @media (max-width: 640px){ .info{grid-template-columns:1fr} }
    .kv{background:#0f141a;border:1px solid var(--line);border-radius:12px;padding:12px}
    .kv b{display:block; color:#9fb5c8; font-size:12px; letter-spacing:.3px; text-transform:uppercase; margin-bottom:6px}
    .kv span{font-size:15px;color:var(--ink)}

    .actions{display:flex;gap:10px;flex-wrap:wrap}
  </style>
</head>
<body>
  <div class="wrap">
    <!-- Topbar -->
    <div class="topbar">
      <div class="brand"><span class="eco">Eco</span><span class="bici">Bici</span> · Panel</div>
      <div class="spacer"></div>
      <a class="btn danger" href="logout.php">Cerrar sesión</a>
    </div>

    <!-- Content -->
    <div class="grid">
      <!-- Perfil -->
      <div class="card profile">
        <div class="avatar">
          <?php if ($foto): ?>
            <img src="<?= e($foto) ?>" alt="Foto de perfil" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <svg viewBox="0 0 24 24" fill="none" stroke="#9aa4b2" stroke-width="1.5" style="display:none">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>
            </svg>
          <?php else: ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="#9aa4b2" stroke-width="1.5">
              <circle cx="12" cy="8" r="4"></circle>
              <path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>
            </svg>
          <?php endif; ?>
        </div>
        <div class="who"><?= e($nombreCompleto ?: 'Usuario') ?></div>
        <div class="role">Rol: <strong><?= e(strtoupper($rol)) ?></strong></div>

        <div class="actions">
          <a class="btn brand" href="home.html">Ir a la página principal</a>
          <?php if ($rol === 'admin'): ?>
            <a class="btn" href="catalogo.php">Catálogo de usuarios</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Detalle -->
      <div class="card">
        <h3>Información de la cuenta</h3>
        <div class="info">
          <div class="kv"><b>DPI</b><span><?= e($usuario['dpi'] ?? '-') ?></span></div>
          <div class="kv"><b>Fecha de nacimiento</b><span><?= e($usuario['fecha_nacimiento'] ?? '-') ?></span></div>
          <div class="kv"><b>Correo</b><span><?= e($usuario['email'] ?? '-') ?></span></div>
          <div class="kv"><b>Teléfono</b><span><?= e($usuario['telefono'] ?? '-') ?></span></div>
        </div>

        <div style="margin-top:16px">
          <h3>Accesos rápidos</h3>
          <div class="actions">
            <a class="btn" href="rutas_personalizadas.html">Rutas personalizadas</a>
            <a class="btn" href="membresia.html">Comprar membresía</a>
            <a class="btn" href="estaciones.html">Estaciones de bicicletas</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>