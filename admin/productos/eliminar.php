<?php
include('../../config/conexion.php');

$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id_producto = $id";

$conexion->query($sql);

header('Location: index.php');

?>
