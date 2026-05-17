<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['favorites']) || !is_array($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$images = [];

if ($id > 0 && isset($conexion)) {
    $sql = "SELECT productos.*, categorias.nombre_categoria
            FROM productos
            LEFT JOIN categorias ON productos.id_categoria = categorias.id_categoria
            WHERE productos.id_producto = " . intval($id) . " LIMIT 1";
    $result = $conexion->query($sql);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $result->free();

        $imageQuery = "SELECT imagen FROM producto_imagenes WHERE id_producto = " . intval($id) . " ORDER BY id_imagen ASC";
        $imgResult = $conexion->query($imageQuery);
        if ($imgResult && $imgResult->num_rows > 0) {
            while ($row = $imgResult->fetch_assoc()) {
                $images[] = $row['imagen'];
            }
            $imgResult->free();
        }
    }
}

if (!$product) {
    header('Location: productos.php');
    exit;
}

$categoryName = !empty($product['nombre_categoria']) ? $product['nombre_categoria'] : (!empty($product['categoria']) ? $product['categoria'] : 'Sin categoría');
$breadcrumbCategory = urlencode($categoryName);
$mainImage = !empty($images) ? $images[0] : '';

function imageUrl($image) {
    return '../assets/uploads/productos/' . htmlspecialchars($image);
}

$cartCount = 0;
$favoritesCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
if (isset($_SESSION['favorites']) && is_array($_SESSION['favorites'])) {
    $favoritesCount = count($_SESSION['favorites']);
}
$isFavorite = in_array(intval($product['id_producto']), $_SESSION['favorites'], true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nombre']); ?> - Ingeniosos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/advanced.css">
    <link rel="stylesheet" href="../assets/css/utilities.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="geometric-decoration">
        <div class="shape-circle" style="width: 280px; height: 280px; background: var(--accent-yellow); top: 120px; left: -90px;"></div>
        <div class="shape-circle" style="width: 180px; height: 180px; background: var(--accent-pink); top: 55%; right: -40px;"></div>
        <div class="shape-circle" style="width: 220px; height: 220px; background: var(--accent-purple); bottom: 40px; left: 12%;"></div>
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
                <a href="productos.php" class="nav-btn active">
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
                <button class="icon-btn" title="Mi Perfil">
                    <i class="fas fa-user"></i>
                </button>
                <a href="favoritos.php" class="icon-btn" title="Favoritos">
                    <i class="fas fa-heart"></i>
                    <span class="icon-badge"><?php echo intval($favoritesCount); ?></span>
                </a>
                <a href="carrito.php" class="icon-btn" title="Carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="icon-badge"><?php echo intval($cartCount); ?></span>
                </a>
            </div>
        </div>
    </header>

    <main class="product-detail-page">
        <div class="detail-breadcrumbs">
            <a href="productos.php">Inicio</a>
            <span>/</span>
            <a href="productos.php?categoria=<?php echo $breadcrumbCategory; ?>"><?php echo htmlspecialchars($categoryName); ?></a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['nombre']); ?></span>
        </div>

        <div class="product-detail-grid">
            <div class="product-gallery">
                <div class="product-thumbs">
                    <?php if (empty($images)) : ?>
                        <div class="product-thumb active">
                            <img src="../assets/uploads/productos/default-product.png" alt="Producto sin imagen">
                        </div>
                    <?php else : ?>
                        <?php foreach ($images as $index => $image) : ?>
                            <button type="button" class="product-thumb<?php echo $index === 0 ? ' active' : ''; ?>" data-image="<?php echo htmlspecialchars($image); ?>">
                                <img src="<?php echo imageUrl($image); ?>" alt="Imagen <?php echo $index + 1; ?> de <?php echo htmlspecialchars($product['nombre']); ?>">
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="product-main-image">
                    <?php if (!empty($mainImage)) : ?>
                        <img id="mainProductImage" src="<?php echo imageUrl($mainImage); ?>" alt="<?php echo htmlspecialchars($product['nombre']); ?>">
                    <?php else : ?>
                        <img id="mainProductImage" src="../assets/uploads/productos/default-product.png" alt="Producto sin imagen">
                    <?php endif; ?>
                </div>
            </div>

            <section class="product-detail-info">
                <span class="product-label"><?php echo htmlspecialchars($categoryName); ?></span>
                <h1 class="product-title-detail"><?php echo htmlspecialchars($product['nombre']); ?></h1>
                <p class="product-subtitle">Descubre más detalles y agrega este libro didáctico a tu carrito con un solo clic.</p>

                <div class="product-price-big">Bs <?php echo number_format($product['precio'], 2, ',', '.'); ?></div>

                <form method="post" action="carrito.php" class="detail-actions add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo intval($product['id_producto']); ?>">

                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn" data-action="decrease">-</button>
                        <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo intval($product['stock']); ?>">
                        <button type="button" class="quantity-btn" data-action="increase">+</button>
                    </div>

                    <button type="submit" class="add-cart-btn">Añadir al carrito</button>
                </form>
                <form method="post" action="favoritos.php" class="detail-actions favorite-detail-form">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="product_id" value="<?php echo intval($product['id_producto']); ?>">
                    <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                    <button type="submit" class="favorite-detail-btn<?php echo $isFavorite ? ' active' : ''; ?>">
                        <i class="fas fa-heart"></i> Favorito
                    </button>
                </form>

                <div class="product-meta">
                    <div class="meta-item">
                        Stock
                        <span><?php echo intval($product['stock']) > 0 ? 'En stock' : 'Agotado'; ?></span>
                    </div>
                    <div class="meta-item">
                        Referencia
                        <span><?php echo intval($product['id_producto']); ?></span>
                    </div>
                    <div class="meta-item">
                        Estado
                        <span><?php echo htmlspecialchars($product['estado']); ?></span>
                    </div>
                </div>
            </section>

            <aside class="product-description">
                <h3>Descripción extensa</h3>
                <p><?php echo nl2br(htmlspecialchars(trim($product['descripcion'] ?: 'Este libro didáctico ofrece contenidos divertidos y pedagógicos para el aprendizaje infantil.'))); ?></p>
                <div class="product-tags">
                    <?php if (!empty($product['categoria'])) : ?>
                        <span class="product-tag"><?php echo htmlspecialchars($product['categoria']); ?></span>
                    <?php endif; ?>
                    <span class="product-tag"><?php echo intval($product['stock']) > 0 ? 'Disponible' : 'Sin stock'; ?></span>
                    <span class="product-tag">Libro didáctico</span>
                </div>
            </aside>
        </div>
    </main>

    <script>
        document.querySelectorAll('.product-thumb').forEach(function(button) {
            button.addEventListener('click', function() {
                document.querySelectorAll('.product-thumb').forEach(function(item) {
                    item.classList.remove('active');
                });
                button.classList.add('active');
                var image = button.getAttribute('data-image');
                var main = document.getElementById('mainProductImage');
                if (main && image) {
                    main.src = '../assets/uploads/productos/' + image;
                }
            });
        });

        document.querySelectorAll('.quantity-selector').forEach(function(control) {
            var input = control.querySelector('.quantity-input');
            control.querySelectorAll('.quantity-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var action = btn.getAttribute('data-action');
                    var value = parseInt(input.value, 10) || 1;
                    var min = parseInt(input.min, 10) || 1;
                    var max = parseInt(input.max, 10) || 999;

                    if (action === 'increase' && value < max) {
                        input.value = value + 1;
                    } else if (action === 'decrease' && value > min) {
                        input.value = value - 1;
                    }
                });
            });
        });
    </script>
</body>
</html>
