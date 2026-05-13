<?php

include('../../config/conexion.php');

$id = $_POST['id'];

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$id_categoria = $_POST['id_categoria'];
$estado = $_POST['estado'];

# =========================================
# ACTUALIZAR PRODUCTO
# =========================================

$sql = "UPDATE productos SET

        nombre='$nombre',
        descripcion='$descripcion',
        precio='$precio',
        stock='$stock',
        id_categoria='$id_categoria',
        estado='$estado'

        WHERE id_producto='$id'";

$conexion->query($sql);

# =========================================
# ELIMINAR IMAGENES
# =========================================

if(isset($_POST['eliminar_imagenes'])) {

    foreach($_POST['eliminar_imagenes'] as $id_imagen) {

        # OBTENER IMAGEN

        $sqlImg = "SELECT * FROM producto_imagenes
        WHERE id_imagen = $id_imagen";

        $resultadoImg = $conexion->query($sqlImg);

        $img = $resultadoImg->fetch_assoc();

        # ELIMINAR ARCHIVO

        $ruta = "../../assets/uploads/productos/" .
        $img['imagen'];

        if(file_exists($ruta)) {

            unlink($ruta);

        }

        # ELIMINAR REGISTRO

        $sqlDelete = "DELETE FROM producto_imagenes
        WHERE id_imagen = $id_imagen";

        $conexion->query($sqlDelete);

    }

}

# =========================================
# AGREGAR NUEVAS IMAGENES
# =========================================

if(!empty($_FILES['imagenes']['name'][0])) {

    foreach($_FILES['imagenes']['tmp_name'] as $key => $tmp) {

        $nombreImagen = time() . "_" .
        $_FILES['imagenes']['name'][$key];

        move_uploaded_file(

            $tmp,

            "../../assets/uploads/productos/" .
            $nombreImagen

        );

        # GUARDAR EN BD

        $sqlNueva = "INSERT INTO producto_imagenes
        (id_producto, imagen)
        VALUES
        ('$id', '$nombreImagen')";

        $conexion->query($sqlNueva);

    }

}

header('Location: index.php');

?>