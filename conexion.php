<?php
$mysqli = new mysqli("localhost", "root", "", "ECObici");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
?>