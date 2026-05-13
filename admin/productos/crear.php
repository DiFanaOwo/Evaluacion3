<?php
include('../../config/conexion.php');

$categorias = $conexion->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h3>Nuevo Producto</h3>
        </div>

        <div class="card-body">

            <form action="guardar.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>

                <div class="mb-3">
                <label >Estado</label>
                <select name="estado" class="form-select" required>
                <option value="">Seleccione estado</option>
                <option value="disponible">Disponible</option>
                <option value="agotado">Agotado</option>
                </select>
                </div>
                <!-- SELECT DE CATEGORIAS -->
                <div class="mb-3">
                    <label>Categoría</label>

                    <select name="id_categoria" class="form-select" required>

                        <option value="">Seleccione categoría</option>

                        <?php while($categoria = $categorias->fetch_assoc()) { ?>

                            <option value="<?= $categoria['id_categoria'] ?>">
                                <?= $categoria['nombre_categoria'] ?>
                                (<?= $categoria['medida'] ?>)
                            </option>

                        <?php } ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Imagen</label>
                    <input type="file"
                        name="imagenes[]"
                        class="form-control"
                        multiple
                        accept="image/*" required>
                </div>

                <button class="btn btn-success">
                    Guardar Producto
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