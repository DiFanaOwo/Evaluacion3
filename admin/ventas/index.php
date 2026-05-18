<?php

include('../../config/conexion.php');

include('../../includes/header.php');

?>
<html>
    <body>
        <div class="container mt-5">

    <!-- TITULO -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">

            Historial de Compras

        </h2>

    </div>

    <!-- TABLA -->

    <div class="table-responsive">

        <table class="table custom-table align-middle">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $sql = "SELECT pedidos.*,
                    usuario.nombre,
                    metodos_pago.nombre_metodo

                    FROM pedidos

                    INNER JOIN usuario
                    ON pedidos.id_usuario = usuario.id_usuario

                    INNER JOIN metodos_pago
                    ON pedidos.id_metodo_pago =
                    metodos_pago.id_metodo_pago

                    ORDER BY pedidos.id_pedido DESC";

            $resultado = $conexion->query($sql);

            // GUARDAR MODALES
            $modales = "";

            while($pedido = $resultado->fetch_assoc()) {

            ?>

                <tr>

                    <td>

                        #<?= $pedido['id_pedido'] ?>

                    </td>

                    <td>

                        <?= $pedido['nombre'] ?>

                    </td>

                    <td>

                        <?= $pedido['fecha_pedido'] ?>

                    </td>

                    <td>

                        Bs <?= $pedido['total'] ?>

                    </td>

                    <td>

                        <?= $pedido['estado_pedido'] ?>

                    </td>

                    <td>

                        <button
                            class="btn btn-ver btn-sm"

                            data-bs-toggle="modal"

                            data-bs-target="#modal<?= $pedido['id_pedido'] ?>"
                        >

                            Ver

                        </button>

                    </td>

                </tr>

            <?php

            // CONSULTA DETALLE

            $sqlDetalle = "SELECT detalle_pedido.*,
                            productos.nombre

                            FROM detalle_pedido

                            INNER JOIN productos
                            ON detalle_pedido.id_producto =
                            productos.id_producto

                            WHERE detalle_pedido.id_pedido =
                            ".$pedido['id_pedido'];

            $detalles = $conexion->query($sqlDetalle);

            // EMPEZAR BUFFER
            ob_start();

            ?>

            <!-- MODAL -->

            <div
                class="modal fade"
                id="modal<?= $pedido['id_pedido'] ?>"
                tabindex="-1"
            >

                <div class="modal-dialog modal-lg modal-dialog-centered">

                    <div class="modal-content">

                        <!-- HEADER -->

                        <div class="modal-header bg-primary text-white">

                            <h5 class="modal-title">

                                Pedido #<?= $pedido['id_pedido'] ?>

                            </h5>

                            <button
                                type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"
                            ></button>

                        </div>

                        <!-- BODY -->

                        <div class="modal-body">

                            <!-- INFO PEDIDO -->

                            <div class="mb-4">

                                <h5>

                                    Información del Pedido

                                </h5>

                                <hr>

                                <p>

                                    <strong>Cliente:</strong>

                                    <?= $pedido['nombre'] ?>

                                </p>

                                <p>

                                    <strong>Fecha:</strong>

                                    <?= $pedido['fecha_pedido'] ?>

                                </p>

                                <p>

                                    <strong>Método Pago:</strong>

                                    <?= $pedido['nombre_metodo'] ?>

                                </p>

                                <p>

                                    <strong>Total:</strong>

                                    Bs <?= $pedido['total'] ?>

                                </p>

                                <p>

                                    <strong>Estado:</strong>

                                    <?= $pedido['estado_pedido'] ?>

                                </p>

                            </div>

                            <!-- PRODUCTOS -->

                            <h5>

                                Productos Comprados

                            </h5>

                            <hr>

                            <table class="table table-bordered">

                                <thead>

                                    <tr>

                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php while($detalle = $detalles->fetch_assoc()) { ?>

                                    <tr>

                                        <td>

                                            <?= $detalle['nombre'] ?>

                                        </td>

                                        <td>

                                            <?= $detalle['cantidad'] ?>

                                        </td>

                                        <td>

                                            Bs <?= $detalle['precio_unitario'] ?>

                                        </td>

                                        <td>

                                            Bs <?= $detalle['subtotal'] ?>

                                        </td>

                                    </tr>

                                <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <?php

            // GUARDAR MODAL
            $modales .= ob_get_clean();

            } // FIN WHILE

            ?>

            </tbody>

        </table>

    </div>

</div>

<!-- MOSTRAR MODALES FUERA DE LA TABLA -->

<?= $modales ?>
<?php include('../../includes/footer.php'); ?>
    </body>
    
</html>


