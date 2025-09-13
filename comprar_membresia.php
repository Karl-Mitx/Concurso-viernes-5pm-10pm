<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulario de Compra - EcoBici</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
  body {
    background: #f8f9fa;
    font-family: 'Poppins', sans-serif;

    /* 🔥 Centrado absoluto con flexbox */
    display: flex;
    justify-content: center;  /* Centrado horizontal */
    align-items: center;      /* Centrado vertical */
    min-height: 100vh;        /* Ocupa toda la pantalla */
    margin: 0;
  }

  h1 {
    color: #28a745;
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
  }

  span {
    display: block;
    text-align: center;
    margin-bottom: 20px;
    color: #555;
  }

  .order-form-label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
  }

  .order-form-input {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 10px;
    font-size: 16px;
    transition: border-color 0.3s ease;
  }

  .order-form-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 8px rgba(40, 167, 69, 0.3);
  }

  .btn-submit {
    background-color: #28a745;
    border: none;
    padding: 12px 30px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 8px;
    transition: all 0.3s ease;
  }

  .btn-submit:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
  }

  .order-form {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    width: 100%;
  }
</style>

</head>
<body>

<section class="order-form m-4">
  <div class="container pt-4">
      <div class="row">
          <div class="col-12 px-4">
              <h1>Formulario de Compra</h1>
              <span>Completa tus datos para adquirir tu membresía EcoBici</span>
              <hr class="mt-1" />
          </div>

          <div class="col-12">
              <div class="row mx-4">
                  <div class="col-12">
                      <label class="order-form-label">Nombre</label>
                  </div>
                  <div class="col-sm-6">
                      <input type="text" id="form1" class="form-control order-form-input" placeholder="Nombres" required />
                  </div>
                  <div class="col-sm-6 mt-2 mt-sm-0">
                      <input type="text" id="form2" class="form-control order-form-input" placeholder="Apellidos" required />
                  </div>
              </div>

              <div class="row mt-3 mx-4">
                  <div class="col-12">
                      <label class="order-form-label">Correo electrónico</label>
                  </div>
                  <div class="col-12">
                      <input type="email" id="form3" class="form-control order-form-input" placeholder="ejemplo@correo.com" required />
                  </div>
              </div>

              <div class="row mt-3 mx-4">
                  <div class="col-12">
                      <label class="order-form-label">Teléfono</label>
                  </div>
                  <div class="col-12">
                      <input type="tel" id="form4" class="form-control order-form-input" placeholder="+502 0000 0000" required />
                  </div>
              </div>

              <div class="row mt-3 mx-4">
                  <div class="col-12">
                      <label class="order-form-label">Dirección</label>
                  </div>
                  <div class="col-12">
                      <input type="text" id="form5" class="form-control order-form-input" placeholder="Calle y número" required />
                  </div>
                  <div class="col-12 mt-2">
                      <input type="text" id="form6" class="form-control order-form-input" placeholder="Colonia o zona" />
                  </div>
              </div>

              <div class="row mt-3 mx-4">
                  <div class="col-sm-6 mt-2 pe-sm-2">
                      <input type="text" id="form7" class="form-control order-form-input" placeholder="Ciudad" required />
                  </div>
                  <div class="col-sm-6 mt-2 ps-sm-0">
                      <input type="text" id="form8" class="form-control order-form-input" placeholder="Región/Departamento" required />
                  </div>
              </div>

              <div class="row mt-3 mx-4">
                  <div class="col-sm-6 mt-2 pe-sm-2">
                      <input type="text" id="form9" class="form-control order-form-input" placeholder="Código Postal" />
                  </div>
                  <div class="col-sm-6 mt-2 ps-sm-0">
                      <input type="text" id="form10" class="form-control order-form-input" placeholder="País" required />
                  </div>
              </div>
              <div class="row mt-4">
  <div class="col-sm-6 text-center">
    <button type="button" class="btn btn-secondary btn-submit" onclick="window.history.back();">Volver</button>
  </div>
  <div class="col-sm-6 text-center">
    <button type="submit" class="btn btn-success btn-submit">Comprar</button>
  </div>
</div>


              
              </div>
          </div>
      </div>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>