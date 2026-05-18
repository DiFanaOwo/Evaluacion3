<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
include("../middlewares/auth.php");

if (!isset($_SESSION['favorites']) || !is_array($_SESSION['favorites'])) {
	$_SESSION['favorites'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';
	$productId = intval($_POST['product_id'] ?? 0);
	$returnUrl = $_POST['return_url'] ?? 'favoritos.php';
	if (preg_match('/^https?:\/\//i', $returnUrl) || strpos($returnUrl, '\\0') !== false) {
		$returnUrl = 'favoritos.php';
	}

	if ($productId > 0 && $action === 'toggle') {
		if (in_array($productId, $_SESSION['favorites'])) {
			$_SESSION['favorites'] = array_values(array_diff($_SESSION['favorites'], [$productId]));
		} else {
			$_SESSION['favorites'][] = $productId;
			$_SESSION['favorites'] = array_values(array_unique($_SESSION['favorites']));
		}
	}

	header('Location: ' . $returnUrl);
	exit;
}

$favoriteIds = array_values(array_filter($_SESSION['favorites'], 'intval'));
$favorites = [];
if (!empty($favoriteIds) && isset($conexion)) {
	$ids = implode(',', array_map('intval', $favoriteIds));
	$result = $conexion->query("SELECT * FROM productos WHERE id_producto IN ($ids)");
	if ($result) {
		while ($row = $result->fetch_assoc()) {
			$favorites[] = $row;
		}
	}
}

$cartCount = 0;
$favoritesCount = count($favoriteIds);
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
	$cartCount = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Favoritos - Ingeniosos</title>
	<link rel="stylesheet" href="../assets/css/styles.css">
	<link rel="stylesheet" href="../assets/css/advanced.css">
	<link rel="stylesheet" href="../assets/css/utilities.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
	<div class="geometric-decoration">
		<div class="shape-circle" style="width: 300px; height: 300px; background: var(--accent-yellow); top: 100px; left: -100px;"></div>
		<div class="shape-circle" style="width: 200px; height: 200px; background: var(--accent-pink); top: 50%; right: -50px;"></div>
		<div class="shape-circle" style="width: 250px; height: 250px; background: var(--accent-purple); bottom: 50px; left: 10%;"></div>
	</div>

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
				<a href="productos.php" class="nav-btn">Libros Didácticos</a>			<a href="historial.php" class="nav-btn">Historial</a>			</nav>
			<div class="search-container">
				<input type="text" class="search-box" placeholder="Buscar tus favoritos..." disabled>
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
				<h1>Favoritos</h1>
				<p class="subtitle">Tus libros favoritos guardados para elegir cuando quieras.</p>
			</div>
			<a href="productos.php" class="btn-secondary">Seguir comprando</a>
		</div>

		<?php if (empty($favorites)) : ?>
			<div class="empty-state">
				<h2>No hay favoritos aún</h2>
				<p>Busca un libro y toca el corazón para guardarlo aquí.</p>
				<a href="productos.php" class="btn-primary">Ver productos</a>
			</div>
		<?php else : ?>
			<div class="products-grid">
				<?php foreach ($favorites as $product) : ?>
					<article class="product-card">
						<div class="product-image-wrapper">
							<?php if (!empty($product['imagen'])) : ?>
								<img src="../assets/uploads/productos/<?php echo htmlspecialchars($product['imagen']); ?>" alt="<?php echo htmlspecialchars($product['nombre']); ?>" class="product-image">
							<?php else : ?>
								<div class="product-placeholder">Sin imagen</div>
							<?php endif; ?>
						</div>
						<div class="product-card-body">
							<div class="product-card-top">
								<span class="badge-category"><?php echo htmlspecialchars($product['categoria'] ?? $product['nombre_categoria'] ?? 'Sin categoría'); ?></span>
								<form method="post" action="favoritos.php" class="favorite-form">
									<input type="hidden" name="action" value="toggle">
									<input type="hidden" name="product_id" value="<?php echo intval($product['id_producto']); ?>">
									<input type="hidden" name="return_url" value="favoritos.php">
									<button type="submit" class="favorite-btn" title="Quitar de favoritos">
										<i class="fas fa-heart"></i>
									</button>
								</form>
							</div>
							<h3 class="product-title"><?php echo htmlspecialchars($product['nombre']); ?></h3>
							<p class="product-price">Bs <?php echo number_format($product['precio'], 2, ',', '.'); ?></p>
							<div class="product-actions">
								<form method="post" action="carrito.php" class="product-add-form">
									<input type="hidden" name="action" value="add">
									<input type="hidden" name="product_id" value="<?php echo intval($product['id_producto']); ?>">
									<input type="hidden" name="quantity" value="1">
									<button type="submit" class="btn-add-cart" <?php echo intval($product['stock']) <= 0 ? 'disabled' : ''; ?>>Añadir al carrito</button>
								</form>
								<a href="detalle_producto.php?id=<?php echo intval($product['id_producto']); ?>" class="btn-details">Ver detalle</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</main>
</body>
</html>
