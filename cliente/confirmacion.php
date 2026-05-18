<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
include("../middlewares/auth.php");

if (!isset($_SESSION['last_order']) || !is_array($_SESSION['last_order'])) {
    header('Location: productos.php');
    exit;
}

$order = $_SESSION['last_order'];
$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$favoritesCount = isset($_SESSION['favorites']) && is_array($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de compra - Ingeniosos</title>
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
                <a href="productos.php" class="nav-btn">
                    <i class="fas fa-book"></i> Libros Didácticos
                </a>
                <a href="historial.php" class="nav-btn">
                    <i class="fas fa-clock"></i> Historial
                </a>
            </nav>
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Buscar libros..." disabled>
            </div>
            <div class="header-icons">
                <button class="icon-btn" title="Mi Perfil"><i class="fas fa-user"></i></button>
                <a href="favoritos.php" class="icon-btn" title="Favoritos"><i class="fas fa-heart"></i><span class="icon-badge"><?php echo $favoritesCount; ?></span></a>
                <a href="carrito.php" class="icon-btn" title="Carrito"><i class="fas fa-shopping-cart"></i><span class="icon-badge"><?php echo $cartCount; ?></span></a>
            </div>
        </div>
    </header>

    <main class="cart-page">
        <div class="checkout-success">
            <h1>¡Gracias por tu compra!</h1>
            <p>Tu pedido <strong>#<?php echo htmlspecialchars($order['id']); ?></strong> se ha confirmado correctamente.</p>
            <div class="order-summary-card">
                <h2>Resumen del pedido</h2>
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
                <a href="productos.php" class="btn-add-cart">Seguir comprando</a>
                <a href="historial.php" class="btn-details">Ver historial</a>
            </div>
        </div>
    </main>
</body>
</html>
