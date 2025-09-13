<?php
$mysqli = new mysqli("localhost", "root", "", "ecobici");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}
?>