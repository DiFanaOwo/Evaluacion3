<?php
session_start();
$orders = isset($_SESSION['orders']) && is_array($_SESSION['orders']) ? $_SESSION['orders'] : [];
$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$favoritesCount = isset($_SESSION['favorites']) && is_array($_SESSION['favorites']) ? count($_SESSION['favorites']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de compras - Ingeniosos</title>
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
                <a href="historial.php" class="nav-btn active">Historial</a>
            </nav>
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Buscar historial..." disabled>
            </div>
            <div class="header-icons">
                <button class="icon-btn" title="Mi Perfil"><i class="fas fa-user"></i></button>
                <a href="favoritos.php" class="icon-btn" title="Favoritos"><i class="fas fa-heart"></i><span class="icon-badge"><?php echo intval($favoritesCount); ?></span></a>
                <a href="carrito.php" class="icon-btn" title="Carrito"><i class="fas fa-shopping-cart"></i><span class="icon-badge"><?php echo intval($cartCount); ?></span></a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="page-title-row">
            <div>
                <h1>Historial de compras</h1>
                <p class="subtitle">Revisa tus últimas órdenes y el detalle de cada compra.</p>
            </div>
            <a href="productos.php" class="btn-secondary">Seguir comprando</a>
        </div>

        <?php if (empty($orders)) : ?>
            <div class="empty-state">
                <h2>Aún no tienes compras registradas</h2>
                <p>Agrega libros a tu carrito y completa una compra para que aparezca en tu historial.</p>
                <a href="productos.php" class="btn-primary">Ver productos</a>
            </div>
        <?php else : ?>
            <div class="history-page">
                <?php foreach ($orders as $order) : ?>
                    <section class="order-card">
                        <div class="order-header">
                            <div>
                                <span>Orden</span>
                                <h2><?php echo htmlspecialchars($order['id']); ?></h2>
                            </div>
                            <div>
                                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($order['fecha']); ?></p>
                                <p><strong>Total:</strong> Bs <?php echo number_format($order['total'], 2, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="order-items-table">
                            <div class="order-items-row order-items-heading">
                                <span>Producto</span>
                                <span>Cantidad</span>
                                <span>Total</span>
                            </div>
                            <?php foreach ($order['items'] as $item) : ?>
                                <div class="order-items-row">
                                    <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                                    <span><?php echo intval($item['cantidad']); ?></span>
                                    <span>Bs <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
