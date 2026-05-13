<?php

include('../../config/conexion.php');

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$id_categoria = $_POST['id_categoria'];

$sql = "INSERT INTO productos
(nombre, descripcion, precio, stock, id_categoria)
VALUES
('$nombre', '$descripcion', '$precio', '$stock', '$id_categoria')";

$conexion->query($sql);

$id_producto = $conexion->insert_id;

# GUARDAR IMAGENES

foreach($_FILES['imagenes']['tmp_name'] as $key => $tmp) {

    $nombreImagen = $_FILES['imagenes']['name'][$key];

    move_uploaded_file(
        $tmp,
        '../../assets/uploads/productos/' . $nombreImagen
    );

    $sqlImagen = "INSERT INTO producto_imagenes
    (id_producto, imagen)
    VALUES
    ('$id_producto', '$nombreImagen')";

    $conexion->query($sqlImagen);
}

header('Location: index.php');

?>