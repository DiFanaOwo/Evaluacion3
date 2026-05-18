<?php
session_start();

require_once __DIR__ . '/../config/conexion.php';

include("../middlewares/auth.php");

/* =========================
   VALIDAR CARRITO
========================= */

if (
    !isset($_SESSION['cart']) ||
    !is_array($_SESSION['cart']) ||
    empty($_SESSION['cart'])
) {

    header('Location: carrito.php');
    exit;

}

/* =========================
   VARIABLES
========================= */

$orderCreated = false;
$order = null;
$orderId = '';

$cartItems = [];

$totalAmount = 0;

$stockWarning = false;

/* =========================
   OBTENER PRODUCTOS
========================= */

$ids = implode(
    ',',
    array_map('intval', array_keys($_SESSION['cart']))
);

if ($ids !== '') {

    $sqlProductos = "SELECT * FROM productos
    WHERE id_producto IN ($ids)";

    $result = $conexion->query($sqlProductos);

    if ($result) {

        while ($row = $result->fetch_assoc()) {

            $productId = intval($row['id_producto']);

            $quantity = intval(
                $_SESSION['cart'][$productId] ?? 0
            );

            $availableStock = intval($row['stock']);

            /* ELIMINAR SI NO HAY STOCK */

            if (
                $quantity <= 0 ||
                $availableStock <= 0
            ) {

                unset($_SESSION['cart'][$productId]);

                continue;

            }

            /* AJUSTAR STOCK */

            if ($quantity > $availableStock) {

                $quantity = $availableStock;

                $_SESSION['cart'][$productId] = $quantity;

                $stockWarning = true;

            }

            /* CALCULOS */

            $row['quantity'] = $quantity;

            $row['subtotal'] =
                $quantity * floatval($row['precio']);

            $totalAmount += $row['subtotal'];

            $cartItems[] = $row;

        }

        $result->free();

    }

}

/* SI CARRITO VACIO */

if (empty($cartItems)) {

    header('Location: carrito.php');

    exit;

}

/* =========================
   CONFIRMAR COMPRA
========================= */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['confirm'])
) {

    /* DATOS PEDIDO */

    $id_usuario = $_SESSION['id_usuario'];

    $id_metodo_pago = 1;

    $direccion = "Sin dirección";

    $estado_pedido = "Pagado";

    /* INSERTAR PEDIDO */

    $sqlPedido = "INSERT INTO pedidos
    (
        id_usuario,
        total,
        estado_pedido,
        id_metodo_pago,
        direccion
    )
    VALUES
    (
        '$id_usuario',
        '$totalAmount',
        '$estado_pedido',
        '$id_metodo_pago',
        '$direccion'
    )";

    $conexion->query($sqlPedido);

    /* OBTENER ID PEDIDO */

    $id_pedido = $conexion->insert_id;

    /* DATOS VISUALES */

    $orderId = "PED-" . str_pad(
        $id_pedido,
        5,
        "0",
        STR_PAD_LEFT
    );

    $orderTime = date('Y-m-d H:i:s');

    $orderItems = [];

    /* VALIDAR ventas_totales */

    $hasSalesColumn = false;

    $columnCheck = $conexion->query(
        "SHOW COLUMNS FROM productos
        LIKE 'ventas_totales'"
    );

    if (
        $columnCheck &&
        $columnCheck->num_rows > 0
    ) {

        $hasSalesColumn = true;

        $columnCheck->free();

    }

    /* RECORRER PRODUCTOS */

    foreach ($cartItems as $item) {

        $productId = intval($item['id_producto']);

        $quantity = intval($item['quantity']);

        $precio = floatval($item['precio']);

        $subtotal = $quantity * $precio;

        /* INSERTAR DETALLE */

        $sqlDetalle = "INSERT INTO detalle_pedido
        (
            id_pedido,
            id_producto,
            cantidad,
            precio_unitario,
            subtotal
        )
        VALUES
        (
            '$id_pedido',
            '$productId',
            '$quantity',
            '$precio',
            '$subtotal'
        )";

        $conexion->query($sqlDetalle);

        /* GUARDAR RESUMEN */

        $orderItems[] = [

            'id' => $productId,

            'nombre' => $item['nombre'],

            'precio' => $precio,

            'cantidad' => $quantity,

            'subtotal' => $subtotal,

        ];

        /* ACTUALIZAR STOCK */

        $updateSql = $hasSalesColumn

            ? "UPDATE productos
               SET stock = stock - $quantity,
               ventas_totales =
               ventas_totales + $quantity
               WHERE id_producto = $productId
               LIMIT 1"

            : "UPDATE productos
               SET stock = stock - $quantity
               WHERE id_producto = $productId
               LIMIT 1";

        $conexion->query($updateSql);

    }

    /* CREAR ORDEN */

    $newOrder = [

        'id' => $orderId,

        'fecha' => $orderTime,

        'total' => $totalAmount,

        'items' => $orderItems,

    ];

    /* GUARDAR SESSION */

    $_SESSION['last_order'] = $newOrder;

    if (
        !isset($_SESSION['orders']) ||
        !is_array($_SESSION['orders'])
    ) {

        $_SESSION['orders'] = [];

    }

    array_unshift(
        $_SESSION['orders'],
        $newOrder
    );

    /* LIMPIAR CARRITO */

    $_SESSION['cart'] = [];

    /* REDIRIGIR */

    header("Location: confirmacion.php");

    exit();

}

/* =========================
   CONTADORES
========================= */

$cartCount = (
    isset($_SESSION['cart']) &&
    is_array($_SESSION['cart'])
)

? array_sum($_SESSION['cart'])

: 0;

$favoritesCount = (
    isset($_SESSION['favorites']) &&
    is_array($_SESSION['favorites'])
)

? count($_SESSION['favorites'])

: 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        Checkout - Ingeniosos

    </title>

    <link
        rel="stylesheet"
        href="../assets/css/styles.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/advanced.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/utilities.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

</head>

<body>

<header class="header">

    <div class="header-content">

        <a href="productos.php" class="logo">

            <img
                src="../assets/uploads/logo-ingeniosos.jpeg"
                alt="Logo Ingeniosos"
                class="logo-img-real"
            >

            <span class="logo-text">

                Ingeniosos

            </span>

        </a>

        <nav class="navbar">

            <button
                class="nav-btn"
                onclick="location.href='productos.php';"
            >

                <i class="fas fa-home"></i>

                Inicio

            </button>

            <a href="productos.php" class="nav-btn">

                Libros Didácticos

            </a>

            <a href="historial.php" class="nav-btn">

                Historial

            </a>

        </nav>

        <div class="search-container">

            <input
                type="text"
                class="search-box"
                placeholder="Buscar libros..."
                disabled
            >

        </div>

        <div class="header-icons">

            <button
                class="icon-btn"
                title="Mi Perfil"
            >

                <i class="fas fa-user"></i>

            </button>

            <a
                href="favoritos.php"
                class="icon-btn"
                title="Favoritos"
            >

                <i class="fas fa-heart"></i>

                <span class="icon-badge">

                    <?= intval($favoritesCount) ?>

                </span>

            </a>

            <a
                href="carrito.php"
                class="icon-btn"
                title="Carrito"
            >

                <i class="fas fa-shopping-cart"></i>

                <span class="icon-badge">

                    <?= intval($cartCount) ?>

                </span>

            </a>

        </div>

    </div>

</header>

<main class="cart-page">

    <div class="cart-heading">

        <h1>

            Confirmar compra

        </h1>

        <div class="cart-actions">

            <a href="productos.php">

                Seguir comprando

            </a>

        </div>

    </div>

    <?php if ($stockWarning) { ?>

        <div class="notice warning">

            Algunos productos fueron ajustados
            al stock disponible.

        </div>

    <?php } ?>

    <table class="cart-table">

        <thead>

            <tr>

                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach ($cartItems as $item) { ?>

            <tr>

                <td>

                    <div class="cart-item">

                        <div class="cart-product">

                            <img
                                src="<?=
                                !empty($item['imagen'])

                                ? '../assets/uploads/productos/' .
                                htmlspecialchars($item['imagen'])

                                : '../assets/uploads/productos/default-product.png'
                                ?>"
                                alt="<?= htmlspecialchars($item['nombre']) ?>"
                            >

                            <div class="cart-product-info">

                                <h3>

                                    <?= htmlspecialchars($item['nombre']) ?>

                                </h3>

                            </div>

                        </div>

                    </div>

                </td>

                <td>

                    Bs <?= number_format($item['precio'], 2, ',', '.') ?>

                </td>

                <td>

                    <?= intval($item['quantity']) ?>

                </td>

                <td>

                    Bs <?= number_format($item['subtotal'], 2, ',', '.') ?>

                </td>

            </tr>

        <?php } ?>

        </tbody>

    </table>

    <div class="cart-summary">

        <h2>

            Resumen de compra

        </h2>

        <div class="cart-summary-row">

            <span>Total</span>

            <strong>

                Bs <?= number_format($totalAmount, 2, ',', '.') ?>

            </strong>

        </div>

        <form
            method="POST"
            action="cheackout.php"
        >

            <input
                type="hidden"
                name="confirm"
                value="1"
            >

            <button type="submit">

                Confirmar compra

            </button>

        </form>

    </div>

</main>

</body>
</html>