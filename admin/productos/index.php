<?php include('../../config/conexion.php'); ?>
<?php include('../../includes/header.php'); ?>

<?php include("../../middlewares/admin.php");?>


<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/tablasAdmin.css">
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Lista de Productos</h2>

        <a href="crear.php" class="btn btn-primary">
            Agregar Producto
        </a>

    </div>
    <!-- FILTROS -->

<form method="GET" class="row mb-4">

    <!-- BUSCAR NOMBRE -->
    <div class="col-md-3">

        <input
            type="text"
            name="nombre"
            class="form-control"
            placeholder="Buscar por nombre"

            value="<?= $_GET['nombre'] ?? '' ?>"
        >

    </div>

    <!-- CATEGORIA -->
    <div class="col-md-3">

        <select
            name="categoria"
            class="form-select"
        >

            <option value="">
                Todas las categorías
            </option>

            <?php

            $sqlCategorias = "SELECT * FROM categorias";

            $categorias = $conexion->query($sqlCategorias);

            while($cat = $categorias->fetch_assoc()) {

            ?>

                <option
                    value="<?= $cat['id_categoria'] ?>"

                    <?= (isset($_GET['categoria']) &&
                        $_GET['categoria'] == $cat['id_categoria'])
                        ? 'selected'
                        : ''
                    ?>
                >

                    <?= $cat['nombre_categoria'] ?>

                </option>

            <?php } ?>

        </select>

    </div>

    <!-- ESTADO -->
    <div class="col-md-2">

        <select
            name="estado"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            <option
                value="disponible"

                <?= (($_GET['estado'] ?? '') == 'disponible')
                    ? 'selected'
                    : ''
                ?>
            >
                Disponible
            </option>

            <option
                value="agotado"

                <?= (($_GET['estado'] ?? '') == 'agotado')
                    ? 'selected'
                    : ''
                ?>
            >
                Agotado
            </option>

        </select>

    </div>

    <!-- STOCK -->
    <div class="col-md-2">

        <select
            name="stock"
            class="form-select"
        >

            <option value="">
                Stock
            </option>

            <option
                value="con"

                <?= (($_GET['stock'] ?? '') == 'con')
                    ? 'selected'
                    : ''
                ?>
            >
                Con stock
            </option>

            <option
                value="sin"

                <?= (($_GET['stock'] ?? '') == 'sin')
                    ? 'selected'
                    : ''
                ?>
            >
                Sin stock
            </option>

        </select>

    </div>

    <!-- BOTON -->
    <div class="col-md-2">

        <button class="btn btn-primary w-100">

            Filtrar

        </button>

    </div>

</form>

    <table class="table custom-table align-middle">

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
                ON productos.id_categoria = categorias.id_categoria
                WHERE 1=1";
        # FILTRO NOMBRE

if(!empty($_GET['nombre'])) {

    $nombre = $_GET['nombre'];

    $sql .= " AND productos.nombre
    LIKE '%$nombre%'";

}

# FILTRO CATEGORIA

if(!empty($_GET['categoria'])) {

    $categoria = $_GET['categoria'];

    $sql .= " AND productos.id_categoria =
    '$categoria'";

}

# FILTRO ESTADO

if(!empty($_GET['estado'])) {

    $estado = $_GET['estado'];

    $sql .= " AND productos.estado =
    '$estado'";

}

# FILTRO STOCK

if(!empty($_GET['stock'])) {

    if($_GET['stock'] == "con") {

        $sql .= " AND productos.stock > 0";

    }

    if($_GET['stock'] == "sin") {

        $sql .= " AND productos.stock <= 0";

    }

}

        $resultado = $conexion->query($sql);

        while($fila = $resultado->fetch_assoc()) {

            # OBTENER PRIMERA IMAGEN

            $sqlImagen = "SELECT * FROM producto_imagenes
            WHERE id_producto = ".$fila['id_producto']."
            LIMIT 1";

            $resultadoImagen = $conexion->query($sqlImagen);

            $imagen = $resultadoImagen->fetch_assoc();

        ?>

            <tr>

                <td><?= $fila['id_producto'] ?></td>

                <!-- IMAGEN -->
                <td>

                    <?php if($imagen) { ?>

                        <img
    src="../../assets/uploads/productos/<?= $imagen['imagen'] ?>"
    class="table-img"
>

                    <?php } else { ?>

                        <span class="text-muted">
                            Sin imagen
                        </span>

                    <?php } ?>

                </td>

                <td><?= $fila['nombre'] ?></td>

                <td>
                    Bs <?= $fila['precio'] ?>
                </td>

                <td><?= $fila['stock'] ?></td>

                <td><?= $fila['nombre_categoria'] ?></td>

                <td><?= $fila['estado'] ?></td>

                <td><?= $fila['ventas_totales'] ?></td>

                <!-- BOTONES -->
                <td>

                    <!-- VER -->
                    <button
                        class="btn btn-ver btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modal<?= $fila['id_producto'] ?>"
                    >
                        Ver
                    </button>

                    <!-- EDITAR -->
                    <a
                        href="editar.php?id=<?= $fila['id_producto'] ?>"
                        class="btn btn-editar btn-sm"
                    >
                        Editar
                    </a>

                    <!-- ELIMINAR -->
                    <a
                        href="eliminar.php?id=<?= $fila['id_producto'] ?>"
                        class="btn btn-eliminar btn-sm"
                        onclick="return confirm('¿Eliminar producto?')"
                    >
                        Eliminar
                    </a>

                </td>

            </tr>

            <?php

            # TODAS LAS IMAGENES DEL PRODUCTO

            $sqlImagenes = "SELECT * FROM producto_imagenes
            WHERE id_producto = ".$fila['id_producto'];

            $imagenes = $conexion->query($sqlImagenes);

            ?>

            <!-- ===================================== -->
            <!-- MODAL PRODUCTO -->
            <!-- ===================================== -->

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

                                                class="img-fluid rounded shadow"

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

            <?php

            # MODALES IMAGEN GRANDE

            $sqlImagenesModal = "SELECT * FROM producto_imagenes
            WHERE id_producto = ".$fila['id_producto'];

            $imagenesModal = $conexion->query($sqlImagenesModal);

            while($imgModal = $imagenesModal->fetch_assoc()) {

            ?>

                <!-- MODAL IMAGEN GRANDE -->

<div
    class="modal fade"
    id="imagenGrande<?= $imgModal['id_imagen'] ?>"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

        <div
            class="modal-content bg-dark border-0 shadow-none"
        >

            <!-- BOTON X -->
            <div class="modal-header border-0 p-2">

                <button
                    type="button"
                    class="btn-close btn-close-white ms-auto"

                    data-bs-dismiss="modal"

                    onclick="
                        setTimeout(() => {

                            const modalProducto =
                            new bootstrap.Modal(
                                document.getElementById(
                                    'modal<?= $fila['id_producto'] ?>'
                                )
                            );

                            modalProducto.show();

                        }, 200);
                    "
                ></button>

            </div>

            <!-- IMAGEN -->
            <div class="modal-body text-center p-0">

                <img
                    src="../../assets/uploads/productos/<?= $imgModal['imagen'] ?>"

                    class="img-fluid rounded shadow"

                    style="
                        max-height: 90vh;
                        width: auto;
                        object-fit: contain;
                    "
                >

            </div>

        </div>

    </div>

</div>

            <?php } ?>

        <?php } ?>

        </tbody>

    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
<footer><?php include('../../includes/footer.php'); ?>
</footer>
</html>