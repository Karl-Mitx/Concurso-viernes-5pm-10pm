<?php
// Conexión a la base de datos
$servername = "localhost";
$username   = "root";   // tu usuario de MySQL
$password   = "";       // tu contraseña de MySQL
$dbname     = "ecobici";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos del formulario
$nombres      = $_POST['nombres'];
$apellidos    = $_POST['apellidos'];
$correo       = $_POST['correo'];
$telefono     = $_POST['telefono'];
$direccion    = $_POST['direccion'];
$colonia      = $_POST['colonia'];
$ciudad       = $_POST['ciudad'];
$region       = $_POST['region'];
$codigo_postal= $_POST['codigo_postal'];
$pais         = $_POST['pais'];

// Insertar en la base de datos
$sql = "INSERT INTO membresias 
(nombres, apellidos, correo, telefono, direccion, colonia, ciudad, region, codigo_postal, pais) 
VALUES (?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $nombres, $apellidos, $correo, $telefono, $direccion, $colonia, $ciudad, $region, $codigo_postal, $pais);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Procesando Membresía</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to bottom, #e0f7e9, #c8f2d9);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-align: center;
        max-width: 400px;
    }
    h2 {
        color: #2e7d32;
        margin-bottom: 15px;
    }
    p {
        color: #555;
        margin-bottom: 25px;
    }
    a {
        display: inline-block;
        text-decoration: none;
        background: #43a047;
        color: #fff;
        padding: 12px 25px;
        border-radius: 8px;
        transition: background 0.3s ease;
        font-weight: bold;
    }
    a:hover {
        background: #2e7d32;
    }
</style>
</head>
<body>
<div class="container">
<?php
if ($stmt->execute()) {
    echo "<h2>✅ ¡Registro exitoso!</h2><p>Gracias por adquirir tu membresía EcoBici 🌱</p>";
    echo "<a href='comprar_membresia.php'>Volver al formulario</a>";
} else {
    echo "<h2>❌ Error</h2><p>" . $stmt->error . "</p>";
    echo "<a href='comprar_membresia.php'>Intentar nuevamente</a>";
}
$stmt->close();
$conn->close();
?>
</div>
</body>
</html>
