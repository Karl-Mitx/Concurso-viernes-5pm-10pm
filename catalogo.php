<?php
require 'conexion.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function initials($nombre, $apellido){
    $i = '';
    if ($nombre)  $i .= mb_substr($nombre, 0, 1, 'UTF-8');
    if ($apellido)$i .= mb_substr($apellido, 0, 1, 'UTF-8');
    return mb_strtoupper($i, 'UTF-8');
}

$result = $mysqli->query("SELECT dpi, nombre, apellido, foto, fecha_nacimiento, email, telefono FROM usuarios ORDER BY nombre, apellido");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>EcoBici • Catálogo de Usuarios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --bg:#0b0f14; --panel:#141a22; --muted:#9aa4b2; --ink:#e8eef6;
      --line:#223042; --brand:#28a745; --brand-2:#218838;
      --chip:#1f2937; --chip-line:#2b3647; --warn:#fca5a5;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:
        radial-gradient(1200px 600px at 20% -10%, #15202b 0, transparent 60%),
        radial-gradient(1200px 600px at 120% 10%, rgba(40,167,69,.12) 0, transparent 55%),
        var(--bg);
      color:var(--ink);
      font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    .wrap{max-width:1100px;margin:0 auto;padding:24px}
    .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .brand{font-weight:800} .brand .eco{color:#fff} .brand .bici{color:var(--brand)}
    .spacer{flex:1}
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:10px 14px;border-radius:10px;border:1px solid var(--line);
      background:#0f141a;color:var(--ink);text-decoration:none;cursor:pointer;transition:.18s;
      font-weight:600
    }
    .btn:hover{border-color:#344a63}
    .btn.brand{background:var(--brand);border-color:var(--brand);color:#fff}
    .btn.brand:hover{background:var(--brand-2);border-color:var(--brand-2)}

    .card{
      background:var(--panel);border:1px solid var(--line);border-radius:16px;
      padding:16px;box-shadow:0 10px 30px rgba(0,0,0,.35);
    }

    .toolbar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:12px}
    .search{
      display:flex;align-items:center;gap:8px;background:#0f141a;border:1px solid var(--line);
      border-radius:10px;padding:8px 12px;min-width:260px
    }
    .search input{
      background:transparent;border:0;outline:0;color:var(--ink);width:100%;
      font-size:14px
    }
    .muted{color:var(--muted);font-size:13px}

    .table-wrap{overflow:auto;border-radius:12px;border:1px solid var(--line)}
    table{width:100%;border-collapse:collapse;min-width:720px;background:#0f141a}
    thead th{
      position:sticky;top:0;background:#101722;border-bottom:1px solid var(--line);
      text-align:left;padding:12px;font-size:13px;color:#cbd5e1;letter-spacing:.2px
    }
    tbody td{padding:12px;border-bottom:1px solid rgba(255,255,255,.04);vertical-align:middle}
    tbody tr:hover{background:#0f1520}
    .td-user{display:flex;align-items:center;gap:10px}
    .avatar{
      width:44px;height:44px;border-radius:50%;overflow:hidden;border:1px solid var(--line);
      background:#0b1117;display:flex;align-items:center;justify-content:center;color:#bcd2e5;font-weight:700
    }
    .avatar img{width:100%;height:100%;object-fit:cover;display:block}
    .name{font-weight:700}
    .sub{font-size:12px;color:var(--muted)}
    .empty{padding:18px;text-align:center;color:var(--muted)}
    .foot{display:flex;justify-content:space-between;align-items:center;margin-top:10px}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="brand"><span class="eco">Eco</span><span class="bici">Bici</span> · Catálogo</div>
      <div class="spacer"></div>
      <a class="btn" href="principal.php">Volver al panel</a>
    </div>

    <div class="card">
      <div class="toolbar">
        <div class="search">
          🔎
          <input id="q" type="text" placeholder="Buscar por DPI, nombre, correo, teléfono…">
        </div>
        <div class="muted" id="hint">Escribe para filtrar la tabla.</div>
      </div>

      <div class="table-wrap">
        <table id="tabla">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>DPI</th>
              <th>Fecha Nac.</th>
              <th>Correo</th>
              <th>Teléfono</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                  $init = initials($row['nombre'], $row['apellido']);
                  $foto = $row['foto'] ?? '';
                ?>
                <tr>
                  <td>
                    <div class="td-user">
                      <div class="avatar">
                        <?php if ($foto): ?>
                          <img src="<?= e($foto) ?>" alt="Foto" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                          <div style="display:none"><?= e($init) ?></div>
                        <?php else: ?>
                          <?= e($init) ?>
                        <?php endif; ?>
                      </div>
                      <div>
                        <div class="name"><?= e($row['nombre'].' '.$row['apellido']) ?></div>
                        <div class="sub"><?= e($row['email']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= e($row['dpi']) ?></td>
                  <td><?= e($row['fecha_nacimiento']) ?></td>
                  <td><?= e($row['email']) ?></td>
                  <td><?= e($row['telefono']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="empty">No hay usuarios registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="foot">
        <div class="muted">Total: <span id="total"><?= $result ? (int)$result->num_rows : 0 ?></span></div>
        <a class="btn brand" href="principal.php">Volver</a>
      </div>
    </div>
  </div>

  <script>
    // Filtro simple en vivo
    const q = document.getElementById('q');
    const table = document.getElementById('tabla').getElementsByTagName('tbody')[0];
    const total = document.getElementById('total');

    q.addEventListener('input', () => {
      const term = q.value.toLowerCase().trim();
      let visible = 0;
      for (const row of table.rows) {
        const text = row.innerText.toLowerCase();
        const show = text.indexOf(term) !== -1;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      }
      total.textContent = visible;
    });
  </script>
</body>
</html>