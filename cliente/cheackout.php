<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: carrito.php');
    exit;
}

$orderCreated = false;
$order = null;
$orderId = '';
$cartItems = [];
$totalAmount = 0;
$stockWarning = false;

$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
if ($ids !== '') {
    $result = $conexion->query("SELECT * FROM productos WHERE id_producto IN ($ids)");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $productId = intval($row['id_producto']);
            $quantity = intval($_SESSION['cart'][$productId] ?? 0);
            $availableStock = intval($row['stock']);

            if ($quantity <= 0 || $availableStock <= 0) {
                unset($_SESSION['cart'][$productId]);
                continue;
            }

            if ($quantity > $availableStock) {
                $quantity = $availableStock;
                $_SESSION['cart'][$productId] = $quantity;
                $stockWarning = true;
            }

            $row['quantity'] = $quantity;
            $row['subtotal'] = $quantity * floatval($row['precio']);
            $totalAmount += $row['subtotal'];
            $cartItems[] = $row;
        }
        $result->free();
    }
}

if (empty($cartItems)) {
    header('Location: carrito.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $orderId = 'ING-' . strtoupper(substr(uniqid('', true), -8));
    $orderTime = date('Y-m-d H:i:s');
    $orderItems = [];
    $hasSalesColumn = false;

    $columnCheck = $conexion->query("SHOW COLUMNS FROM productos LIKE 'ventas_totales'");
    if ($columnCheck && $columnCheck->num_rows > 0) {
        $hasSalesColumn = true;
        $columnCheck->free();
    }

    foreach ($cartItems as $item) {
        $productId = intval($item['id_producto']);
        $quantity = intval($item['quantity']);

        $orderItems[] = [
            'id' => $productId,
            'nombre' => $item['nombre'],
            'precio' => floatval($item['precio']),
            'cantidad' => $quantity,
            'subtotal' => $item['subtotal'],
        ];

        $updateSql = $hasSalesColumn
            ? "UPDATE productos SET stock = stock - $quantity, ventas_totales = ventas_totales + $quantity WHERE id_producto = $productId LIMIT 1"
            : "UPDATE productos SET stock = stock - $quantity WHERE id_producto = $productId LIMIT 1";
        $conexion->query($updateSql);
    }

    $newOrder = [
        'id' => $orderId,
        'fecha' => $orderTime,
        'total' => $totalAmount,
        'items' => $orderItems,
    ];

    if (!isset($_SESSION['orders']) || !is_array($_SESSION['orders'])) {
        $_SESSION['orders'] = [];
    }
    array_unshift($_SESSION['orders'], $newOrder);
    $_SESSION['cart'] = [];
    $orderCreated = true;
    $order = $newOrder;
}

$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$favoritesCount = isset($_SESSION['favorites']) && is_array($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Ingeniosos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/advanced.css">
    <link rel="stylesheet" href="../assets/css/utilities.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="productos.php" class="logo">
                <img src="../assets/uploads/logo-ingeniosos.jpeg" alt="Logo Ingeniosos" class="logo-img-real">
                <span class="logo-text">Ingeniosos</span>
            </a>
            <nav class="navbar">
                <button class="nav-btn" onclick="location.href='productos.php';">
                    <i class="fas fa-home"></i> Inicio
                </button>
                <a href="productos.php" class="nav-btn">Libros Didácticos</a>
                <a href="historial.php" class="nav-btn">Historial</a>
            </nav>
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Buscar libros..." disabled>
            </div>
            <div class="header-icons">
                <button class="icon-btn" title="Mi Perfil"><i class="fas fa-user"></i></button>
                <a href="favoritos.php" class="icon-btn" title="Favoritos"><i class="fas fa-heart"></i><span class="icon-badge"><?php echo intval($favoritesCount); ?></span></a>
                <a href="carrito.php" class="icon-btn" title="Carrito"><i class="fas fa-shopping-cart"></i><span class="icon-badge"><?php echo intval($cartCount); ?></span></a>
            </div>
        </div>
    </header>

    <main class="cart-page">
        <?php if ($orderCreated && $order !== null) : ?>
            <div class="checkout-success">
                <h1>¡Gracias por tu compra!</h1>
                <p>Tu orden <strong><?php echo htmlspecialchars($order['id']); ?></strong> se ha registrado correctamente.</p>
                <?php if ($stockWarning) : ?>
                    <p class="notice">Algunos productos fueron ajustados por disponibilidad de stock.</p>
                <?php endif; ?>
                <div class="order-summary-card">
                    <h2>Resumen de tu pedido</h2>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($order['fecha']); ?></p>
                    <p><strong>Total pagado:</strong> Bs <?php echo number_format($order['total'], 2, ',', '.'); ?></p>
                    <div class="order-items-list">
                        <?php foreach ($order['items'] as $item) : ?>
                            <div class="order-item-row">
                                <span><?php echo htmlspecialchars($item['nombre']); ?> x <?php echo intval($item['cantidad']); ?></span>
                                <strong>Bs <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="checkout-actions">
                    <a href="historial.php" class="btn-add-cart">Ver historial de compras</a>
                    <a href="productos.php" class="btn-details">Seguir comprando</a>
                </div>
            </div>
        <?php else : ?>
            <div class="cart-heading">
                <h1>Confirmar compra</h1>
                <div class="cart-actions">
                    <a href="productos.php">Seguir comprando</a>
                </div>
            </div>

            <?php if ($stockWarning) : ?>
                <div class="notice warning">Algunos productos en el carrito fueron ajustados a la disponibilidad actual.</div>
            <?php endif; ?>

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
                    <?php foreach ($cartItems as $item) : ?>
                        <tr>
                            <td>
                                <div class="cart-item">
                                    <div class="cart-product">
                                        <img src="<?php echo !empty($item['imagen']) ? '../assets/uploads/productos/' . htmlspecialchars($item['imagen']) : '../assets/uploads/productos/default-product.png'; ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                                        <div class="cart-product-info">
                                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                            <p><?php echo htmlspecialchars($item['categoria'] ?? $item['nombre_categoria'] ?? 'Sin categoría'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>Bs <?php echo number_format($item['precio'], 2, ',', '.'); ?></td>
                            <td><?php echo intval($item['quantity']); ?></td>
                            <td>Bs <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h2>Resumen de compra</h2>
                <div class="cart-summary-row">
                    <span>Total</span>
                    <strong>Bs <?php echo number_format($totalAmount, 2, ',', '.'); ?></strong>
                </div>
                <form method="post" action="cheackout.php">
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit">Confirmar compra</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
