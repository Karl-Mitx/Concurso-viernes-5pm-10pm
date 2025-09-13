<?php
// helpers.php
session_start();
require 'conexion.php'; // Debe definir $mysqli y ->set_charset('utf8mb4')

function usuarioActual() {
  return $_SESSION['usuario'] ?? null;
}

function cargarUsuarioPorEmail(mysqli $mysqli, string $email) {
  $stmt = $mysqli->prepare("SELECT id, email, dpi, nombre, apellido, foto, fecha_nacimiento, telefono, rol FROM usuarios WHERE email=? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc() ?: null;
  $stmt->close();
  return $row;
}

/** Devuelve la membresía vigente (o null) consultando la tabla `membresias` por usuario_id */
function membresiaVigente(mysqli $mysqli, int $usuarioId) {
  $stmt = $mysqli->prepare("
    SELECT usuario_id, tipo, inicio, fin, estado
    FROM membresias
    WHERE usuario_id = ?
    LIMIT 1
  ");
  $stmt->bind_param('i', $usuarioId);
  $stmt->execute();
  $res = $stmt->get_result();
  $m = $res->fetch_assoc() ?: null;
  $stmt->close();

  if (!$m) return null;
  if ($m['estado'] !== 'activa' || empty($m['fin'])) return null;
  try {
    $hoy = new DateTime('today');
    $fin = new DateTime($m['fin']);
    return ($fin >= $hoy) ? $m : null;
  } catch (Exception $e) {
    return null;
  }
}

/** true si el usuario tiene membresía activa */
function tieneMembresiaActiva($usuario, mysqli $mysqli) {
  if (!$usuario || empty($usuario['id'])) return false;
  return membresiaVigente($mysqli, (int)$usuario['id']) !== null;
}

/** Refresca $_SESSION['usuario'] desde la BD por id */
function refreshUsuarioEnSesion(mysqli $mysqli) {
  if (empty($_SESSION['usuario']['id'])) return;
  $id = (int)$_SESSION['usuario']['id'];
  $stmt = $mysqli->prepare("SELECT id, email, dpi, nombre, apellido, foto, fecha_nacimiento, telefono, rol FROM usuarios WHERE id=? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    $_SESSION['usuario'] = $row;
  }
  $stmt->close();
}