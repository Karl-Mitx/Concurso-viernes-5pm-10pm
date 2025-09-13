<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

require 'conexion.php';

$usuario = $_SESSION['usuario'];
$usuarioId = $usuario['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bicicletaId = intval($_POST['bicicleta_id'] ?? 0);

    if ($usuarioId > 0 && $bicicletaId > 0) {
        $duracionHoras = 1; // duración estimada
$fechaFin = $_POST['fecha_fin'] ?? null;
$sql = "INSERT INTO historial_bicicletas (usuario_id, bicicleta_id, fecha_inicio, fecha_fin)
        VALUES (?, ?, NOW(), ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iis", $usuarioId, $bicicletaId, $fechaFin);
$stmt->execute();

if ($stmt->affected_rows > 0) {
            header("Location: historico_uso.php");
            exit;
        } else {
            die("Error al registrar uso: " . $mysqli->error);
        }
    } else {
        die("Datos inválidos.");
    }
}
?>
