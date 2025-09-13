<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulario de Compra - EcoBici</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    :root{
      --eco:#28a745;
      --eco-dark:#218838;
      --ink:#1d2632;
      --panel:#ffffff;
      --line:#e5efe7;
    }

    /* Fondo con gradiente + patrón de hojas (SVG inline) */
    body{
      min-height:100vh; margin:0;
      font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      display:flex; align-items:center; justify-content:center; padding:24px;
      color:var(--ink);
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(40,167,69,.12) 0, transparent 60%),
        radial-gradient(1200px 600px at 120% 10%, rgba(33,136,56,.10) 0, transparent 55%),
        #f4f8f6;
      /* patrón */
      background-image:
        url("data:image/svg+xml;utf8,\
        <svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120' fill='none'>\
          <path d='M18 68c18-10 30-30 34-44c-14 4-34 16-44 34c-2 3 2 8 6 10c3 2 8 2 4 0z' fill='%23cfead8' opacity='.35'/>\
          <path d='M92 38c-18 10-30 30-34 44c14-4 34-16 44-34c2-3-2-8-6-10c-3-2-8-2-4 0z' fill='%23cfead8' opacity='.28'/>\
        </svg>");
      background-size: cover, cover, auto, 120px 120px;
      background-blend-mode: normal, normal, normal, multiply;
    }

    .eco-card{
      background:var(--panel);
      width:100%; max-width:920px;
      border-radius:18px;
      border:1px solid var(--line);
      box-shadow:0 18px 40px rgba(0,0,0,.08);
      overflow:hidden;
    }

    .eco-header{
      padding:22px 26px;
      background:linear-gradient(135deg, #e9f7ef, #f6fffb);
      border-bottom:1px solid var(--line);
    }
    .brand{
      display:flex; align-items:center; gap:12px; margin-bottom:6px;
    }
    .brand-badge{
      width:40px; height:40px; border-radius:50%;
      background:linear-gradient(135deg, var(--eco), #6ee7b7);
      display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800;
      box-shadow:0 10px 24px rgba(40,167,69,.35);
    }
    .brand-title{
      font-weight:800; font-size:22px; letter-spacing:.2px; color:#103018;
    }
    .brand-title .eco{color:#0e1d11} .brand-title .bici{color:var(--eco)}

    .tagline{color:#4a6a58; margin:0}
    .leaf-divider{
      height:12px; margin-top:10px;
      background:
        url("data:image/svg+xml;utf8,\
        <svg xmlns='http://www.w3.org/2000/svg' width='240' height='12' viewBox='0 0 240 12'>\
          <path d='M0 6h100' stroke='%23b8e3c9' stroke-width='2' stroke-linecap='round'/>\
          <path d='M140 6h100' stroke='%23b8e3c9' stroke-width='2' stroke-linecap='round'/>\
          <path d='M120 6c7 0 10-6 10-6s-3 12-10 12s-10-12-10-12s3 6 10 6z' fill='%2328a745'/>\
        </svg>") center/contain no-repeat;
    }

    .eco-body{ padding:26px; }

    h1{ font-size:26px; color:var(--eco); font-weight:800; text-align:center; margin:0 0 6px; }
    .sub{ text-align:center; color:#5c6f66; margin-bottom:18px; }

    .order-form-label{ font-weight:700; color:#234436; margin-bottom:6px; }

    .order-form-input.form-control{
      border:1px solid #cfead8; border-radius:10px; padding:12px 14px; font-size:16px;
      transition:.18s ease; background:#fbfffd;
    }
    .order-form-input.form-control:focus{
      border-color:var(--eco);
      box-shadow:0 0 0 4px rgba(40,167,69,.15);
    }

    .btn-submit{
      border:none; border-radius:12px; padding:12px 28px; font-weight:800; letter-spacing:.2px;
      box-shadow:0 10px 20px rgba(40,167,69,.20); transition:.15s ease; outline:0;
    }
    .btn-success.btn-submit{
      background:linear-gradient(135deg, var(--eco), var(--eco-dark));
    }
    .btn-success.btn-submit:hover{ transform:translateY(-1px); }
    .btn-secondary.btn-submit{
      background:#e7f5ee; color:#17412b; box-shadow:none; border:1px solid #cfead8;
    }
    .btn-secondary.btn-submit:hover{ background:#dff1e7; }

    .hr-soft{ border-top:1px dashed #cfead8; margin:14px 0 2px; }

    /* Responsivo */
    @media (max-width: 576px){
      .eco-body{ padding:18px; }
      .order-form-input.form-control{ font-size:15px; padding:11px 12px; }
    }
  </style>
</head>
<body>

  <section class="eco-card">
    <!-- Encabezado -->
    <div class="eco-header">
      <div class="brand">
        <div class="brand-badge">🌿</div>
        <div class="brand-title"><span class="eco">Eco</span><span class="bici">Bici</span> • Membresía</div>
      </div>
      <p class="tagline">Muévete limpio, respira mejor. Con tu membresía apoyas <b>+ciclovías</b> y <b>–CO₂</b>.</p>
      <div class="leaf-divider"></div>
    </div>

    <!-- Contenido -->
    <div class="eco-body">
      <div class="text-center">
        <h1>Formulario de Compra</h1>
        <div class="sub">Completa tus datos para adquirir tu membresía EcoBici</div>
      </div>
      <hr class="hr-soft"/>

      <form>
        <div class="form-row mx-1">
          <div class="col-12 mb-2">
            <label class="order-form-label">Nombre</label>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Nombres" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Apellidos" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2">
            <label class="order-form-label">Correo electrónico</label>
          </div>
          <div class="col-12 mb-3">
            <input type="email" class="form-control order-form-input" placeholder="ejemplo@correo.com" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2">
            <label class="order-form-label">Teléfono</label>
          </div>
          <div class="col-12 mb-3">
            <input type="tel" class="form-control order-form-input" placeholder="+502 0000 0000" required>
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-12 mb-2">
            <label class="order-form-label">Dirección</label>
          </div>
          <div class="col-12 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Calle y número" required>
          </div>
          <div class="col-12 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Colonia o zona">
          </div>
        </div>

        <div class="form-row mx-1">
          <div class="col-sm-6 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Ciudad" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Región/Departamento" required>
          </div>
          <div class="col-sm-6 mb-3">
            <input type="text" class="form-control order-form-input" placeholder="Código Postal">
          </div>
          <div class="col-sm-6 mb-4">
            <input type="text" class="form-control order-form-input" placeholder="País" required>
          </div>
        </div>

        <div class="form-row">
          <div class="col-sm-6 text-center mb-2 mb-sm-0">
            <button type="button" class="btn btn-secondary btn-submit" onclick="window.history.back()">Volver</button>
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