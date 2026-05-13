<?php include('../../config/conexion.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Lista de Productos</h2>

        <a href="crear.php" class="btn btn-primary">
            Agregar Producto
        </a>
    </div>
<table class="table table-bordered table-hover">

        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Ventas Totales</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
 <?php

        $sql = "SELECT productos.*, categorias.nombre_categoria
        FROM productos
        INNER JOIN categorias
        ON productos.id_categoria = categorias.id_categoria";
        $resultado = $conexion->query($sql);

        while($fila = $resultado->fetch_assoc()) {

        ?>

            <tr>
                <td><?= $fila['id_producto'] ?></td>

               <td>

                <?php
                $sqlImagen = "SELECT * FROM producto_imagenes
                WHERE id_producto = ".$fila['id_producto']."
                LIMIT 1";
                $resultadoImagen = $conexion->query($sqlImagen);
                $imagen = $resultadoImagen->fetch_assoc();
                ?>
                <img
                    src="../../assets/uploads/productos/<?= $imagen['imagen'] ?>"
                    width="80"
                    height="80"
                    style="object-fit: cover;"
                >
                </td>

                <td><?= $fila['nombre'] ?></td>
                <td>Bs <?= $fila['precio'] ?></td>
                <td><?= $fila['stock'] ?></td>
                <td><?= $fila['nombre_categoria'] ?></td>
                <td><?= $fila['estado'] ?></td>
                <td><?= $fila['ventas_totales'] ?></td>
                <td>
                    <button
                    class="btn btn-info btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modal<?= $fila['id_producto'] ?>"
                >
                    Ver
                    </button>
                    <a
                        href="editar.php?id=<?= $fila['id_producto'] ?>"
                        class="btn btn-warning btn-sm"
                    >
                        Editar
                    </a>

                    <a
                        href="eliminar.php?id=<?= $fila['id_producto'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('¿Eliminar producto?')"
                    >
                        Eliminar
                    </a>

                </td>
            </tr>

        <?php

$sqlImagenes = "SELECT * FROM producto_imagenes
WHERE id_producto = ".$fila['id_producto'];

$imagenes = $conexion->query($sqlImagenes);

?>

<div
    class="modal fade"
    id="modal<?= $fila['id_producto'] ?>"
    tabindex="-1"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">

                    <?= $fila['nombre'] ?>

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="row">

                    <!-- GALERIA -->
                    <div class="col-md-6">

                        <div class="row">

                        <?php while($img = $imagenes->fetch_assoc()) { ?>

                            <div class="col-6 mb-3">
                                <img
                                    src="../../assets/uploads/productos/<?= $img['imagen'] ?>"
                                    class="img-fluid rounded shadow imagen-preview"
                                    style="
                                        height: 180px;
                                        object-fit: cover;
                                        width: 100%;
                                        cursor: pointer;
                                    "
                                    data-bs-toggle="modal"
                                    data-bs-target="#imagenGrande<?= $img['id_imagen'] ?>"
                                >
                            </div>

                        <?php } ?>

                        </div>

                    </div>

                    <!-- INFORMACION -->
                    <div class="col-md-6">

                        <h4>
                            <?= $fila['nombre'] ?>
                        </h4>

                        <hr>

                        <p>

                            <strong>Descripción:</strong>

                            <br>

                            <?= $fila['descripcion'] ?>

                        </p>

                        <p>

                            <strong>Precio:</strong>

                            Bs <?= $fila['precio'] ?>

                        </p>

                        <p>

                            <strong>Stock:</strong>

                            <?= $fila['stock'] ?>

                        </p>

                        <p>

                            <strong>Categoría:</strong>

                            <?= $fila['nombre_categoria'] ?>

                        </p>

                        <p>

                            <strong>Estado:</strong>

                            <?= $fila['estado'] ?>

                        </p>

                        <p>

                            <strong>Ventas Totales:</strong>

                            <?= $fila['ventas_totales'] ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

    <?php } ?>
        
</tbody>

    </table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>