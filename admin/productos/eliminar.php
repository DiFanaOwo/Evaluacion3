<?php
include('../../config/conexion.php');
include("../../middlewares/admin.php");

$id = $_GET['id'];

$sql = "DELETE FROM productos WHERE id_producto = $id";

$conexion->query($sql);

header('Location: index.php');

?>
