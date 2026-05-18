<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

include("../middlewares/auth.php");


if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$favoritesCount = isset($_SESSION['favorites']) && is_array($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
$userId = isset($_SESSION['usuario']) ? intval($_SESSION['usuario']) : 0;

$paymentMethods = [];
$paymentMethodIdField = '';
$paymentMethodLabelField = '';
if (isset($conexion)) {
    $methodResult = $conexion->query("SELECT * FROM metodos_pago");
    if ($methodResult) {
        $rows = [];
        while ($row = $methodResult->fetch_assoc()) {
            $rows[] = $row;
        }
        if (!empty($rows)) {
            $paymentMethods = $rows;
            $keys = array_keys($rows[0]);
            foreach ($keys as $key) {
                if ($paymentMethodIdField === '' && stripos($key, 'id') !== false) {
                    $paymentMethodIdField = $key;
                }
            }
            if ($paymentMethodIdField === '') {
                $paymentMethodIdField = $keys[0];
            }
            foreach ($keys as $key) {
                if ($key !== $paymentMethodIdField) {
                    $paymentMethodLabelField = $key;
                    break;
                }
            }
            if ($paymentMethodLabelField === '') {
                $paymentMethodLabelField = $paymentMethodIdField;
            }
        }
        $methodResult->free();
    }
}

$success = false;
$successOrder = null;
$successPaymentLabel = '';
$errorMessage = '';
$selectedPaymentMethod = 0;
$stockWarning = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['product_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    $selectedPaymentMethod = intval($_POST['payment_method_id'] ?? 0);

    if ($action === 'add' && $productId > 0) {
        $stockResult = $conexion->query("SELECT stock FROM productos WHERE id_producto = " . $productId . " LIMIT 1");
        $stock = 0;
        if ($stockResult && $stockResult->num_rows > 0) {
            $stockRow = $stockResult->fetch_assoc();
            $stock = intval($stockRow['stock']);
        }
        $existing = intval($_SESSION['cart'][$productId] ?? 0);
        $newQty = min($stock, $existing + $quantity);
        if ($newQty > 0) {
            $_SESSION['cart'][$productId] = $newQty;
        }
    }

    if ($action === 'update' && $productId > 0 && isset($_SESSION['cart'][$productId])) {
        $stockResult = $conexion->query("SELECT stock FROM productos WHERE id_producto = " . $productId . " LIMIT 1");
        $stock = 0;
        if ($stockResult && $stockResult->num_rows > 0) {
            $stockRow = $stockResult->fetch_assoc();
            $stock = intval($stockRow['stock']);
        }
        $newQty = min($stock, $quantity);
        if ($newQty > 0) {
            $_SESSION['cart'][$productId] = $newQty;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    }

    if ($action === 'remove' && $productId > 0 && isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    if ($action === 'confirm') {
        if (empty($_SESSION['cart'])) {
            $errorMessage = 'Tu carrito está vacío. Agrega un producto antes de confirmar.';
        } elseif (!empty($paymentMethods) && $selectedPaymentMethod <= 0) {
            $errorMessage = 'Selecciona un método de pago para confirmar tu compra.';
        } else {
            $cartItems = [];
            $totalAmount = 0;
            $ids = array_map('intval', array_keys($_SESSION['cart']));
            $placeholders = implode(',', $ids);
            $sql = "SELECT * FROM productos WHERE id_producto IN ($placeholders)";
            $result = $conexion->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $id = intval($row['id_producto']);
                    if (!isset($_SESSION['cart'][$id])) {
                        continue;
                    }
                    $quantity = intval($_SESSION['cart'][$id]);
                    $availableStock = intval($row['stock']);
                    if ($quantity <= 0 || $availableStock <= 0) {
                        unset($_SESSION['cart'][$id]);
                        continue;
                    }
                    if ($quantity > $availableStock) {
                        $quantity = $availableStock;
                        $_SESSION['cart'][$id] = $quantity;
                        $stockWarning = true;
                    }
                    $row['quantity'] = $quantity;
                    $row['subtotal'] = $quantity * floatval($row['precio']);
                    $totalAmount += $row['subtotal'];
                    $cartItems[] = $row;
                }
            }

            if (empty($cartItems)) {
                $errorMessage = 'No hay productos disponibles para procesar la compra.';
            }

            if ($errorMessage === '') {
                $paymentLabel = '';
                foreach ($paymentMethods as $method) {
                    if (intval($method[$paymentMethodIdField]) === $selectedPaymentMethod) {
                        $paymentLabel = $method[$paymentMethodLabelField] ?? 'Pago';
                        break;
                    }
                }
                $successPaymentLabel = $paymentLabel;

                $orderColumns = [];
                $orderValues = [];
                if ($userId > 0) {
                    $orderColumns[] = 'id_usuario';
                    $orderValues[] = intval($userId);
                }
                if ($selectedPaymentMethod > 0) {
                    $orderColumns[] = $paymentMethodIdField;
                    $orderValues[] = intval($selectedPaymentMethod);
                }
                $orderColumns[] = 'total';
                $orderValues[] = number_format($totalAmount, 2, '.', '');
                $orderColumns[] = 'fecha_pedido';
                $orderValues[] = $conexion->real_escape_string(date('Y-m-d H:i:s'));
                $orderColumns[] = 'estado_pedido';
                $orderValues[] = $conexion->real_escape_string('Pagado');

                $escapedValues = array_map(function ($value) use ($conexion) {
                    if (is_numeric($value) && (string)$value === (string)(int)$value) {
                        return intval($value);
                    }
                    return "'" . $conexion->real_escape_string($value) . "'";
                }, $orderValues);

                $insertOrderSql = 'INSERT INTO pedidos (' . implode(', ', $orderColumns) . ') VALUES (' . implode(', ', $escapedValues) . ')';
                if ($conexion->query($insertOrderSql)) {
                    $pedidoId = intval($conexion->insert_id);
                    $hasSalesColumn = false;
                    $columnCheck = $conexion->query("SHOW COLUMNS FROM productos LIKE 'ventas_totales'");
                    if ($columnCheck && $columnCheck->num_rows > 0) {
                        $hasSalesColumn = true;
                        $columnCheck->free();
                    }

                    foreach ($cartItems as $item) {
                        $productId = intval($item['id_producto']);
                        $quantity = intval($item['quantity']);
                        $price = number_format(floatval($item['precio']), 2, '.', '');
                        $subtotal = number_format($item['subtotal'], 2, '.', '');

                        $detailColumns = ['id_pedido', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal'];
                        $detailValues = [intval($pedidoId), $productId, $quantity, $price, $subtotal];
                        $detailSql = 'INSERT INTO detalle_pedido (' . implode(', ', $detailColumns) . ') VALUES (' . implode(', ', $detailValues) . ')';
                        $conexion->query($detailSql);

                        $updateProductSql = 'UPDATE productos SET stock = stock - ' . $quantity;
                        if ($hasSalesColumn) {
                            $updateProductSql .= ', ventas_totales = ventas_totales + ' . $quantity;
                        }
                        $updateProductSql .= ' WHERE id_producto = ' . $productId . ' LIMIT 1';
                        $conexion->query($updateProductSql);
                    }

                    if (!isset($_SESSION['orders']) || !is_array($_SESSION['orders'])) {
                        $_SESSION['orders'] = [];
                    }

                    $successOrder = [
                        'id' => $pedidoId,
                        'fecha' => date('Y-m-d H:i:s'),
                        'total' => $totalAmount,
                        'payment' => $paymentLabel,
                        'items' => array_map(function ($item) {
                            return [
                                'nombre' => $item['nombre'],
                                'cantidad' => $item['quantity'],
                                'subtotal' => $item['subtotal'],
                            ];
                        }, $cartItems),
                    ];

                    array_unshift($_SESSION['orders'], $successOrder);
                    $_SESSION['cart'] = [];
                    $success = true;
                } else {
                    $errorMessage = 'No se pudo registrar el pedido en la base de datos. Intenta nuevamente.';
                }
            }
        }
    }

    if (in_array($action, ['add', 'update', 'remove'])) {
        header('Location: carrito.php');
        exit;
    }
}

$cartItems = [];
$totalAmount = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', $ids);
    $sql = "SELECT * FROM productos WHERE id_producto IN ($placeholders)";
    $result = $conexion->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $id = intval($row['id_producto']);
            if (!isset($_SESSION['cart'][$id])) {
                continue;
            }
            $quantity = intval($_SESSION['cart'][$id]);
            $availableStock = intval($row['stock']);
            if ($quantity > $availableStock) {
                $quantity = $availableStock;
                if ($quantity > 0) {
                    $_SESSION['cart'][$id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$id]);
                    continue;
                }
            }
            $row['quantity'] = $quantity;
            $row['subtotal'] = $quantity * floatval($row['precio']);
            $totalAmount += $row['subtotal'];
            $cartItems[] = $row;
        }
    }
}

function cartCount() {
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

function favoritesCount() {
    return isset($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Ingeniosos</title>
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
                <button class="nav-btn" onclick="location.href='categoria.php';">
                    <i class="fas fa-home"></i> Inicio
                </button>
                <a href="categoria.php" class="nav-btn">
                    <i class="fas fa-book"></i> Libros Didácticos
                </a>
                <a href="historial.php" class="nav-btn">
                    <i class="fas fa-clock"></i> Historial
                </a>
            </nav>
            <div class="search-container">
                <input type="text" class="search-box" placeholder="¿Qué estás buscando?">
            </div>
            <div class="header-icons">
                <button class="icon-btn" title="Mi Perfil"><i class="fas fa-user"></i></button>
                <a href="favoritos.php" class="icon-btn" title="Favoritos"><i class="fas fa-heart"></i><span class="icon-badge"><?php echo favoritesCount(); ?></span></a>
                <a href="carrito.php" class="icon-btn" title="Carrito"><i class="fas fa-shopping-cart"></i><span class="icon-badge"><?php echo cartCount(); ?></span></a>
            </div>
        </div>
    </header>

    <main class="cart-page">
        <div class="cart-heading">
            <h1>Tu carrito</h1>
            <div class="cart-actions">
                <a href="productos.php">Seguir comprando</a>
            </div>
        </div>

        <?php if (empty($cartItems)) : ?>
            <div class="cart-empty">
                <h2>Tu carrito está vacío</h2>
                <p>Agrega libros didácticos y vuelve para verlos aquí.</p>
                <a href="productos.php">Ir a la tienda</a>
            </div>

        <?php else : ?>
            <?php if (!empty($errorMessage)) : ?>
                <div class="notice warning"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
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
                                            <p class="stock-info">Stock disponible: <?php echo intval($item['stock']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>Bs <?php echo number_format($item['precio'], 2, ',', '.'); ?></td>
                            <td>
                                <form method="post" class="cart-update-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo intval($item['id_producto']); ?>">
                                    <div class="quantity-selector">
                                        <button type="button" class="qty-btn" data-action="decrease">-</button>
                                        <input type="number" name="quantity" value="<?php echo intval($item['quantity']); ?>" min="1" max="<?php echo intval($item['stock']); ?>">
                                        <button type="button" class="qty-btn" data-action="increase">+</button>
                                    </div>
                                </form>
                            </td>
                            <td>Bs <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                            <td>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo intval($item['id_producto']); ?>">
                                    <button type="submit" class="cart-remove">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h2>Resumen de compra</h2>
                <div class="payment-method-card">
                    <form method="post" id="checkout-form">
                        <label for="payment_method_id">Método de pago</label>
                        <input type="hidden" name="action" value="confirm">
                        <?php if (!empty($paymentMethods)) : ?>
                            <select name="payment_method_id" id="payment_method_id" required>
                                <option value="">Selecciona un método de pago</option>
                                <?php foreach ($paymentMethods as $method) : ?>
                                    <option value="<?php echo intval($method[$paymentMethodIdField]); ?>" <?php echo ($selectedPaymentMethod === intval($method[$paymentMethodIdField])) ? 'selected' : ''; ?>><?php echo htmlspecialchars($method[$paymentMethodLabelField]); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <p>No se encontraron métodos de pago disponibles en la base de datos.</p>
                        <?php endif; ?>
                        <div class="cart-summary-row">
                            <span>Total</span>
                            <strong>Bs <?php echo number_format($totalAmount, 2, ',', '.'); ?></strong>
                        </div>
                        <button type="submit" class="btn-add-cart">Confirmar compra</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.querySelectorAll('.cart-update-form').forEach(function(form) {
            var input = form.querySelector('input[name="quantity"]');
            var decrease = form.querySelector('.qty-btn[data-action="decrease"]');
            var increase = form.querySelector('.qty-btn[data-action="increase"]');
            var max = parseInt(input.max, 10) || 999;
            var min = parseInt(input.min, 10) || 1;

            decrease.addEventListener('click', function() {
                var current = parseInt(input.value, 10) || 1;
                if (current > min) {
                    input.value = current - 1;
                    form.submit();
                }
            });

            increase.addEventListener('click', function() {
                var current = parseInt(input.value, 10) || 1;
                if (current < max) {
                    input.value = current + 1;
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
