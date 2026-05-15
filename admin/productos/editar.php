<?php

include('../../config/conexion.php');

$id = $_GET['id'];

$sql = "SELECT * FROM productos WHERE id_producto = $id";
$resultado = $conexion->query($sql);

$producto = $resultado->fetch_assoc();

$sqlImagenes = "SELECT * FROM producto_imagenes
WHERE id_producto = $id";
$imagenes = $conexion->query($sqlImagenes);

$sqlCategorias = "SELECT * FROM categorias";
$categorias = $conexion->query($sqlCategorias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow">

        <div class="card-header bg-warning">
            <h3>Editar Producto</h3>
        </div>

        <div class="card-body">

            <form action="actualizar.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?= $producto['id_producto'] ?>">

                <div class="mb-3">
                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= $producto['nombre'] ?>"
                    >
                </div>

                <div class="mb-3">
                    <label>Descripción</label>
                     <textarea
                        name="descripcion"
                        class="form-control"
                    ><?= $producto['descripcion'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Precio</label>
                    <input
                        type="number"
                        step="0.01"
                        name="precio"
                        class="form-control"
                        value="<?= $producto['precio'] ?>"
                    >
                </div>

                <div class="mb-3">
                    <label>Stock</label>
                    <input
                        type="number"
                        name="stock"
                        class="form-control"
                        value="<?= $producto['stock'] ?>"
                    >
                </div>

                <div class="mb-3">
                <label >Estado</label>
                <select name="estado" class="form-select" value="<?= $producto['estado'] ?>">" required>
                <option value="">Seleccione estado</option>
                <option value="disponible">Disponible</option>
                <option value="agotado">Agotado</option>
                </select>
                </div>


                <div class="mb-3">
                    <label>Categoría</label>
                    <select name="id_categoria" class="form-select">
                    <?php while($categoria = $categorias->fetch_assoc()) { ?>

                        <option
                            value="<?= $categoria['id_categoria'] ?>"
                            <?= ($producto['id_categoria'] == $categoria['id_categoria'])
                                ? 'selected'
                                : ''
                            ?>
                        >
                            <?= $categoria['nombre_categoria'] ?>
                        </option>

                    <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nueva Imagen</label>
                    <div class="mb-3">

                    <label>Agregar Nuevas Imágenes</label>

                    <input
                        type="file"
                        name="imagenes[]"
                        class="form-control"
                        multiple
                        accept="image/*"
                    >
                </div>
                <div class="mb-3">

    <label class="mb-3">

        Imágenes Actuales

    </label>

    <div class="row">

    <?php while($img = $imagenes->fetch_assoc()) { ?>

        <div class="col-md-3 mb-4">

            <div class="card shadow-sm">

                <!-- IMAGEN -->
                <img
                    src="../../assets/uploads/productos/<?= $img['imagen'] ?>"
                    class="card-img-top"
                    style="
                        height: 180px;
                        object-fit: cover;
                    "
                >

                <!-- BOTON ELIMINAR -->
                <div class="card-body text-center">

                    <label class="text-danger">

                        <input
                            type="checkbox"
                            name="eliminar_imagenes[]"
                            value="<?= $img['id_imagen'] ?>"
                        >

                        Eliminar

                    </label>

                </div>

            </div>

        </div>

    <?php } ?>

    </div>

</div>
                <br>

                <button class="btn btn-success">
                    Actualizar
                </button>
                <a href="index.php" class="btn btn-secondary">
                    Volver
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>