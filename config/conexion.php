<?php

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "tienda_libros_didacticos";

$conexion = new mysqli($host, $usuario, $password, $bd,3306);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

?>