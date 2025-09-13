<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;

/* Avatar: si no hay foto, generamos SVG con inicial del nombre */
$inicial = $usuario && !empty($usuario['nombre']) ? mb_strtoupper(mb_substr($usuario['nombre'],0,1)) : '👤';
$defaultAvatar = 'data:image/svg+xml;charset=utf-8,' . rawurlencode(
  '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120">
     <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
       <stop offset="0" stop-color="#28a745"/><stop offset="1" stop-color="#6ee7b7"/></linearGradient></defs>
     <rect width="100%" height="100%" fill="url(#g)"/>
     <circle cx="60" cy="60" r="38" fill="rgba(255,255,255,.15)"/>
     <text x="50%" y="56%" text-anchor="middle" font-family="Poppins,Arial" font-size="46" fill="#fff">'.$inicial.'</text>
   </svg>'
);
$avatar = !empty($usuario['foto']) ? $usuario['foto'] : $defaultAvatar;
$nombreCorto = $usuario && !empty($usuario['nombre']) ? explode(' ', trim($usuario['nombre']))[0] : 'Cuenta';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <title>EcoBici • Puerto Barrios</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon" />

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />

  <!-- Libs -->
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />
  <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

  
  <link href="css/style.css" rel="stylesheet" />

  <!-- Overrides Eco -->
  <style>
    :root{ --eco:#28a745; --eco-dark:#218838; --ink:#0f1720; --panel:#ffffff; --line:#e5efe7; }
    *{box-sizing:border-box}
    body{
      font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(40,167,69,.10) 0, transparent 60%),
        radial-gradient(1200px 600px at 110% 10%, rgba(33,136,56,.10) 0, transparent 55%),
        #f6faf7;
      color:var(--ink);
      background-image:
        url("data:image/svg+xml;utf8,\
        <svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'>\
          <path d='M18 68c18-10 30-30 34-44c-14 4-34 16-44 34c-2 3 2 8 6 10c3 2 8 2 4 0z' fill='%23cfead8' opacity='.35'/>\
          <path d='M92 38c-18 10-30 30-34 44c14-4 34-16 44-34c2-3-2-8-6-10c-3-2-8-2-4 0z' fill='%23cfead8' opacity='.28'/>\
        </svg>");
      background-size: cover, cover, auto, 120px 120px;
      background-blend-mode: normal, normal, multiply;
    }

    /* Topbar */
    .topbar-eco{ background:linear-gradient(135deg,#e9f7ef,#f6fffb); border-bottom:1px solid var(--line); color:#1f3b25; font-weight:600; }
    .topbar-eco a{ color:#1a6e3b !important; }

    /* Navbar */
    .nav-bar .navbar{ background:#fff !important; border-bottom:1px solid var(--line); border-radius:12px; margin-top:10px; }
    .navbar-brand h1{ font-weight:800; letter-spacing:.3px; margin:0; }
    .navbar-brand h1 .text-dark{color:#0f1e13 !important}
    .navbar .nav-link{
      font-weight:600; color:#1f2d2a !important; padding:.75rem 1rem !important; border-radius:8px; transition:all .15s ease;
    }
    .navbar .nav-link:hover{ background:#f2fbf6; color:var(--eco) !important; }
    .navbar .nav-link.active{ color:#fff !important; background:var(--eco); }

    /* Botón Eco */
    .btn-eco{
      background:linear-gradient(135deg,var(--eco),var(--eco-dark));
      border:0; color:#fff; font-weight:700; border-radius:12px; padding:.9rem 1.6rem; box-shadow:0 12px 26px rgba(40,167,69,.22);
    }
    .btn-eco:hover{ filter:brightness(.98); transform:translateY(-1px); }

    /* Carousel */
    #header-carousel .carousel-item img{ height:72vh; object-fit:cover; object-position:center; }
    .carousel-caption{ bottom:0%; }
    .caption-glass{
      backdrop-filter: blur(8px);
      background: linear-gradient(to bottom, rgba(0,0,0,.20), rgba(0,0,0,.30));
      border:1px solid rgba(255,255,255,.18);
      border-radius:16px; padding:18px 20px;
    }
    .caption-glass h1, .caption-glass h4{ text-shadow:0 2px 10px rgba(0,0,0,.45); }
    .caption-glass h1{ font-weight:800 }

    /* Footer */
    .footer-eco{ background:#0c0f0e; color:#e7f5ee; border-top:1px solid #12301f; }
    .footer-eco h4{ letter-spacing:2px; color:#c5f3d9; }
    .footer-eco a{ color:#a7f3c3; }
    .footer-eco a:hover{ color:#7ee0a7; text-decoration:none; }

    /* Back-to-top */
    .back-to-top{ background:var(--eco); border-color:var(--eco); box-shadow:0 10px 24px rgba(40,167,69,.35); }
    .back-to-top:hover{ background:var(--eco-dark); border-color:var(--eco-dark); }

    /* Cuenta / Avatar */
    .account-dropdown .avatar{
      width:38px; height:38px; border-radius:50%; overflow:hidden; border:1px solid var(--line);
      display:inline-flex; align-items:center; justify-content:center; background:#eaf7ef;
    }
    .account-dropdown .avatar img{ width:100%; height:100%; object-fit:cover; display:block; }
    .account-name{ display:none; font-weight:700; }
    @media (min-width: 992px){ .account-name{ display:inline; } }

    /* ======= FIX del overlay negro del template en el carrusel ======= */
    .nav-bar::before,
    #header-carousel::before,
    #header-carousel .carousel-inner::before,
    #header-carousel .carousel-item::before,
    #header-carousel .carousel-item::after{
      content:none !important; display:none !important;
    }
  </style>
</head>

<body>
  <!-- Topbar -->
  <div class="container-fluid topbar-eco pt-3 d-none d-lg-block">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-left mb-2 mb-lg-0">
          <div class="d-inline-flex align-items-center">
            <p class="mb-0"><i class="fa fa-envelope mr-2"></i>info@umg.edu.gt</p>
            <p class="text-body px-3 mb-0">|</p>
            <p class="mb-0"><i class="fa fa-phone-alt mr-2"></i>+502 7948 5070</p>
          </div>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
          <div class="d-inline-flex align-items-center">
            <a class="px-2" href="https://www.facebook.com/u.marianogalvez/?locale=es_LA"><i class="fab fa-facebook-f"></i></a>
            <a class="px-2" href="https://www.instagram.com/marianogalvez/?hl=es"><i class="fab fa-instagram"></i></a>
            <a class="pl-2" href="https://www.youtube.com/@umarianogalvez"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Navbar -->
<div class="container-fluid position-relative nav-bar p-0">
  <div class="container-lg position-relative p-0 px-lg-3" style="z-index:9;">
    <nav class="navbar navbar-expand-lg navbar-light shadow-lg py-2 py-lg-2 px-3">
      <a href="home.php" class="navbar-brand">
        <h1 class="m-0 text-primary"><span class="text-dark">Eco</span>Bici</h1>
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse"
              data-target="#navbarCollapse" aria-controls="navbarCollapse"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
        <div class="navbar-nav ml-auto py-0 align-items-lg-center">

          <!-- Ítems visibles -->
          <a href="home.php" class="nav-item nav-link active">Inicio</a>
          <a href="estaciones.html" class="nav-item nav-link">Estaciones de Bicicletas</a>
          <a href="rutas_personalizadas.html" class="nav-item nav-link">Rutas Personalizadas</a>
          <a href="CO2.html" class="nav-item nav-link">Cálculo de CO₂</a>
          <a href="selecbici.php" class="nav-item nav-link">Selección de Bicicleta</a>

          <!-- Dropdown "Más" con las 3 últimas -->
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="moreMenu" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Más
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moreMenu">
              <a class="dropdown-item" href="historico_uso.php">Bicicletas Registradas</a>
              <a class="dropdown-item" href="reportes.php">Reportes</a>
            </div>
          </div>

          <!-- Cuenta / Avatar (opcional) -->
          <div class="nav-item dropdown account-dropdown ml-lg-2">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar mr-2"><img src="<?= htmlspecialchars($avatar) ?>" alt="avatar"></span>
              <span class="account-name"><?= htmlspecialchars($nombreCorto) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="accountDropdown">
              <?php if ($usuario): ?>
                <span class="dropdown-item-text small text-muted">
                  Conectado como <b><?= htmlspecialchars($usuario['email']) ?></b>
                </span>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="principal.php"><i class="far fa-user mr-2"></i> Panel</a>
                <a class="dropdown-item" href="comprar_membresia.php"><i class="far fa-id-badge mr-2"></i> Membresía</a>
                <a class="dropdown-item" href="selecbici.php"><i class="fas fa-bicycle mr-2"></i> Alquilar bicicletas</a>
                <?php if (!empty($usuario['rol']) && strtolower($usuario['rol'])==='admin'): ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="catalogo.php"><i class="fas fa-users-cog mr-2"></i> Admin: Usuarios</a>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión</a>
              <?php else: ?>
                <a class="dropdown-item" href="index.php"><i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión</a>
                <a class="dropdown-item" href="registro.php"><i class="far fa-user-plus mr-2"></i> Crear cuenta</a>
              <?php endif; ?>
            </div>
          </div>
          <!-- /Cuenta -->

        </div>
      </div>
    </nav>


            
              <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="accountDropdown">
                <?php if ($usuario): ?>
                  <span class="dropdown-item-text small text-muted">
                    Conectado como <b><?= htmlspecialchars($usuario['email']) ?></b>
                  </span>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="principal.php"><i class="far fa-user mr-2"></i> Panel</a>
                  <a class="dropdown-item" href="comprar_membresia.php"><i class="far fa-id-badge mr-2"></i> Membresía</a>
                  <a class="dropdown-item" href="selecbici.php"><i class="fas fa-bicycle mr-2"></i> Alquilar bicicletas</a>
                  <?php if (!empty($usuario['rol']) && strtolower($usuario['rol'])==='admin'): ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="catalogo.php"><i class="fas fa-users-cog mr-2"></i> Admin: Usuarios</a>
                  <?php endif; ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión</a>
                <?php else: ?>
                  <a class="dropdown-item" href="index.php"><i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión</a>
                  <a class="dropdown-item" href="registro.php"><i class="far fa-user-plus mr-2"></i> Crear cuenta</a>
                <?php endif; ?>
              </div>
            </div>
            <!-- /Cuenta -->
          </div>
        </div>
      </nav>
    </div>
  </div>

  <!-- Carousel -->
  <div class="container-fluid p-0">
    <div id="header-carousel" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img class="w-100" src="img/Bicicleta4.jpg" alt="EcoBici Puerto Barrios" />
          <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
            <div class="caption-glass" style="max-width:900px;">
              <h4 class="text-white text-uppercase mb-md-2">EcoBici Puerto Barrios</h4>
              <h1 class="display-4 text-white mb-md-3">Cuida el planeta, contribuye.</h1>
              <a href="comprar_membresia.php" class="btn btn-eco mt-1">Comprar Membresía</a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img class="w-100" src="img/Bicicleta2.jpg" alt="EcoBici Puerto Barrios" />
          <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
            <div class="caption-glass" style="max-width:900px;">
              <h4 class="text-white text-uppercase mb-md-2">EcoBici Puerto Barrios</h4>
              <h1 class="display-4 text-white mb-md-3">¡Sé más ecológico hoy!</h1>
              <a href="comprar_membresia.php" class="btn btn-eco mt-1">Comprar Membresía</a>
            </div>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
        <div class="btn btn-dark" style="width:45px;height:45px;"><span class="carousel-control-prev-icon mb-n2"></span></div>
      </a>
      <a class="carousel-control-next" href="#header-carousel" data-slide="next">
        <div class="btn btn-dark" style="width:45px;height:45px;"><span class="carousel-control-next-icon mb-n2"></span></div>
      </a>
    </div>
  </div>

  <!-- Footer -->
  <div class="container-fluid footer-eco pt-5 px-0">
    <div class="container pt-3 pb-4">
      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <h4 class="text-uppercase mb-3">Nuestro Contacto</h4>
          <p class="mb-2"><i class="fa fa-map-marker-alt mr-2"></i>16 Calle A, Puerto Barrios</p>
          <p class="mb-2"><i class="fa fa-phone-alt mr-2"></i>+502 7948 5070</p>
          <p class="mb-0"><i class="fa fa-envelope mr-2"></i>info@umg.edu.gt</p>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <h4 class="text-uppercase mb-3">Síguenos</h4>
          <p>Nuestras redes</p>
          <div class="d-flex">
            <a class="btn btn-outline-light btn-lg-square mr-2" href="https://www.facebook.com/u.marianogalvez/?locale=es_LA"><i class="fab fa-facebook-f"></i></a>
            <a class="btn btn-outline-light btn-lg-square mr-2" href="https://www.instagram.com/marianogalvez/?hl=es"><i class="fab fa-instagram"></i></a>
            <a class="btn btn-outline-light btn-lg-square" href="https://www.youtube.com/@umarianogalvez"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <h4 class="text-uppercase mb-3">Horarios</h4>
          <div>
            <h6 class="text-uppercase mb-1">Lunes - Viernes</h6>
            <p class="mb-2">8:00 AM - 10:00 PM</p>
            <h6 class="text-uppercase mb-1">Sábado</h6>
            <p class="mb-2">2:00 PM - 8:00 PM</p>
            <h6 class="text-uppercase mb-1">Domingo</h6>
            <p class="mb-0">CERRADO</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <h4 class="text-uppercase mb-3">Enlaces</h4>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><a href="rutas_personalizadas.html">Rutas Personalizadas</a></li>
            <li class="mb-2"><a href="comprar_membresia.php">Comprar Membresía</a></li>
            <li class="mb-2"><a href="about.html">Estaciones</a></li>
            <li class="mb-0"><a href="home.php">Inicio</a></li>
          </ul>
        </div>
      </div>
      <hr style="border-top:1px solid #12301f" />
      <div class="d-flex justify-content-between align-items-center">
        <small>© <script>document.write(new Date().getFullYear())</script> EcoBici Puerto Barrios</small>
        <small>+ciclovías • –CO₂ • +salud</small>
      </div>
    </div>
  </div>

  <!-- Back to Top -->
  <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
  <script src="lib/easing/easing.min.js"></script>
  <script src="lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="lib/tempusdominus/js/moment.min.js"></script>
  <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
  <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
  <script src="mail/jqBootstrapValidation.min.js"></script>
  <script src="mail/contact.js"></script>
  <script src="js/main.js"></script>
</body>
</html>