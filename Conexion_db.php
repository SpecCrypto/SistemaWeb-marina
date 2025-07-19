<?php
$conexion = mysqli_connect("localhost", "root", "", "marina_astillero");

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
