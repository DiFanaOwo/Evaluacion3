<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$cartCount = 0;
$favoritesCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
if (isset($_SESSION['favorites']) && is_array($_SESSION['favorites'])) {
    $favoritesCount = count($_SESSION['favorites']);
}

$categories = [];
$showProducts = false;
$categoryName = '';
$products = [];
$errorMessage = '';

if (isset($conexion)) {
    $check = $conexion->query("SHOW TABLES LIKE 'categorias'");
    if ($check && $check->num_rows > 0) {
        $result = $conexion->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id_categoria' => $row['id_categoria'],
                    'nombre' => $row['nombre_categoria'],
                ];
            }
            $result->free();
        }
    }

    if (empty($categories)) {
        $result = $conexion->query("SELECT DISTINCT categoria AS nombre FROM productos WHERE TRIM(categoria) <> '' ORDER BY categoria ASC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id_categoria' => null,
                    'nombre' => $row['nombre'],
                ];
            }
            $result->free();
        }
    }

    if ($categories === []) {
        $categories = [
            ['id_categoria' => null, 'nombre' => '0-6 meses'],
            ['id_categoria' => null, 'nombre' => '6-12 meses'],
            ['id_categoria' => null, 'nombre' => '1-2 años'],
            ['id_categoria' => null, 'nombre' => '3-4 años'],
            ['id_categoria' => null, 'nombre' => 'Más de 5 años'],
        ];
    }

    $filterCondition = "productos.estado IN ('activo', 'disponible')";
    if (isset($_GET['categoria_id'])) {
        $categoriaId = intval($_GET['categoria_id']);
        if ($categoriaId > 0) {
            $filterCondition .= " AND productos.id_categoria = $categoriaId";
            $showProducts = true;
            $categoryName = 'Categoría ' . $categoriaId;
            $categoryNameResult = $conexion->query("SELECT nombre_categoria FROM categorias WHERE id_categoria = $categoriaId LIMIT 1");
            if ($categoryNameResult && $categoryNameResult->num_rows > 0) {
                $row = $categoryNameResult->fetch_assoc();
                $categoryName = $row['nombre_categoria'];
            }
        }
    } elseif (isset($_GET['categoria'])) {
        $categoryName = trim($_GET['categoria']);
        if ($categoryName !== '') {
            $escapedName = $conexion->real_escape_string($categoryName);
            $filterCondition .= " AND (categorias.nombre_categoria = '$escapedName' OR productos.categoria = '$escapedName')";
            $showProducts = true;
        }
    }

    if ($showProducts) {
        $sql = "SELECT productos.*, categorias.nombre_categoria AS nombre_categoria FROM productos LEFT JOIN categorias ON productos.id_categoria = categorias.id_categoria WHERE $filterCondition ORDER BY productos.nombre ASC";
        $result = $conexion->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free();
        }
    }
}

$pageTitle = $showProducts ? 'Productos en ' . htmlspecialchars($categoryName) : 'Libros Didácticos';
$pageSubtitle = $showProducts ? 'Explora los libros de esta categoría.' : 'Elige una categoría para iniciar tu búsqueda.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Ingeniosos</title>
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
                <a href="categoria.php" class="nav-btn active">
                    <i class="fas fa-book"></i> Libros Didácticos
                </a>
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

    <main class="main-content">
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo $pageTitle; ?></h1>
                <p class="hero-subtitle"><?php echo $pageSubtitle; ?></p>
            </div>
        </section>

        <?php if (!$showProducts) : ?>
            <div class="text-center mb-4">
                <h2 class="section-title">Selecciona una categoría</h2>
                <p class="section-subtitle">Elige un tema y verás los libros disponibles en esa categoría.</p>
            </div>

            <div class="categories-grid">
                <?php foreach ($categories as $cat) :
                    $catName = $cat['nombre'];
                    $link = 'categoria.php';
                    if (!empty($cat['id_categoria'])) {
                        $link .= '?categoria_id=' . intval($cat['id_categoria']);
                    } else {
                        $link .= '?categoria=' . urlencode($catName);
                    }
                    $imagenBase = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $catName)));
                    $imagePath = '';
                    foreach (['jpeg', 'jpg', 'png'] as $ext) {
                        $candidate = '../assets/uploads/productos/' . $imagenBase . '.' . $ext;
                        if (file_exists(__DIR__ . '/' . $candidate)) {
                            $imagePath = $candidate;
                            break;
                        }
                    }
                ?>
                    <a href="<?php echo htmlspecialchars($link); ?>" class="category-card-link">
                        <div class="category-card">
                            <div class="category-image-wrapper">
                                <?php if (!empty($imagePath)) : ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($catName); ?>" class="category-image">
                                <?php else : ?>
                                    <svg viewBox="0 0 200 220" class="placeholder-svg">
                                        <rect width="200" height="220" fill="#b6e3f5"/>
                                        <text x="100" y="110" font-size="20" text-anchor="middle" fill="#000"><?php echo htmlspecialchars($catName); ?></text>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="category-info">
                                <h3 class="category-title"><?php echo htmlspecialchars($catName); ?></h3>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($showProducts) : ?>
            <div class="products-toolbar">
                <div>
                    <h2 class="section-title">Productos de <?php echo htmlspecialchars($categoryName); ?></h2>
                    <p class="section-subtitle">Elige entre los libros disponibles en esta categoría.</p>
                </div>
            </div>

            <?php if (empty($products)) : ?>
                <div class="no-products">No se encontraron productos en esta categoría.</div>
            <?php else : ?>
                <div class="products-grid">
                    <?php foreach ($products as $prod) :
                        $productImage = '';
                        $imagenBase = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $prod['nombre'])));
                        foreach (['jpeg', 'jpg', 'png'] as $ext) {
                            $candidate = '../assets/uploads/productos/' . $imagenBase . '.' . $ext;
                            if (file_exists(__DIR__ . '/' . $candidate)) {
                                $productImage = $candidate;
                                break;
                            }
                        }
                        $productUrl = 'detalle_producto.php?id=' . intval($prod['id_producto']);
                        $productCategory = !empty($prod['nombre_categoria']) ? $prod['nombre_categoria'] : (!empty($prod['categoria']) ? $prod['categoria'] : 'Sin categoría');
                        ?>
                        <article class="product-card">
                            <div class="product-image-wrapper">
                                <?php if (!empty($productImage)) : ?>
                                    <img src="<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>" class="product-image">
                                <?php else : ?>
                                    <svg viewBox="0 0 400 300" class="product-placeholder">
                                        <rect width="400" height="300" fill="#f1f7fb"/>
                                        <text x="200" y="160" font-size="24" text-anchor="middle" fill="#94a3b8">Sin imagen</text>
                                    </svg>
                                <?php endif; ?>
                                <a href="<?php echo $productUrl; ?>" class="product-overlay"><span>Ver más</span></a>
                            </div>
                            <div class="product-card-body">
                                <div class="product-card-top">
                                    <span class="badge-category"><?php echo htmlspecialchars($productCategory); ?></span>
                                </div>
                                <h3 class="product-title"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                                <p class="product-price">Bs <?php echo number_format($prod['precio'], 2, ',', '.'); ?></p>
                                <p class="product-stock"><?php echo intval($prod['stock']) > 0 ? 'En stock' : 'Agotado'; ?></p>
                                <div class="product-actions">
                                    <form method="post" action="carrito.php" class="product-add-form">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo intval($prod['id_producto']); ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart" <?php echo intval($prod['stock']) <= 0 ? 'disabled' : ''; ?>>Añadir al carrito</button>
                                    </form>
                                    <a href="<?php echo $productUrl; ?>" class="btn-details">Más detalles</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
