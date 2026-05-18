<?php
session_start();

$cartCount = 0;
$favoritesCount = 0;

/* FAVORITOS */
if (!isset($_SESSION['favorites']) || !is_array($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

$favoriteIds = array_values(array_filter($_SESSION['favorites'], 'intval'));

/* CARRITO */
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}

/* CONTADOR FAVORITOS */
if (!empty($favoriteIds)) {
    $favoritesCount = count($favoriteIds);
}

/* VERIFICAR SESIÓN */
$isLoggedIn = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingeniosos - Tienda de Libros Didácticos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/advanced.css">
    <link rel="stylesheet" href="../assets/css/utilities.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- DECORACIÓN GEOMÉTRICA DE FONDO -->
    <div class="geometric-decoration">
        <div class="shape-circle" style="width: 300px; height: 300px; background: var(--accent-yellow); top: 100px; left: -100px;"></div>
        <div class="shape-circle" style="width: 200px; height: 200px; background: var(--accent-pink); top: 50%; right: -50px;"></div>
        <div class="shape-circle" style="width: 250px; height: 250px; background: var(--accent-purple); bottom: 50px; left: 10%;"></div>
    </div>

    <!-- HEADER FISHER-PRICE STYLE -->
<header class="header">
    <div class="header-content">

        <!-- LOGO -->
        <a href="index.php" class="logo">
            <img src="../assets/uploads/logo-ingeniosos.jpeg" alt="Logo Ingeniosos" class="logo-img-real">
            <span class="logo-text">Ingeniosos</span>
        </a>

        <!-- NAVBAR CENTRAL -->
        <nav class="navbar">
            <button class="nav-btn active">
                <i class="fas fa-home"></i> Inicio
            </button>

            <a href="productos.php" class="nav-btn">
                <i class="fas fa-book"></i> Libros Didácticos
            </a>

            <a href="historial.php" class="nav-btn">
                <i class="fas fa-clock"></i> Historial
            </a>
        </nav>

        <!-- SEARCH BAR -->
        <div class="search-container">
            <input type="text" class="search-box" placeholder="¿Qué estás buscando?">
        </div>

        <!-- HEADER ICONS -->
<div class="header-icons">

    <?php if (isset($_SESSION["usuario"])): ?>

        <!-- PERFIL -->
        <div class="profile-menu">

            <button class="icon-btn" id="profileBtn" title="Mi Perfil">
                <i class="fas fa-user"></i>
            </button>

            <div class="profile-dropdown" id="profileDropdown">

                <div class="profile-name">
                    <?php echo $_SESSION['nombre'] ?? 'Usuario'; ?>
                </div>

                <div class="profile-email">
                    <?php echo $_SESSION['correo'] ?? ''; ?>
                </div>

                <a href="../login/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>

            </div>

        </div>

    <?php else: ?>

        <!-- LOGIN -->
        <a href="../login/login.php" class="login-btn">
            <i class="fas fa-right-to-bracket"></i>
            <span>Iniciar sesión</span>
        </a>

    <?php endif; ?>

    <!-- FAVORITOS -->
    <a href="favoritos.php" class="icon-btn" title="Favoritos">
        <i class="fas fa-heart"></i>
        <span class="icon-badge">
            <?php echo intval($favoritesCount); ?>
        </span>
    </a>

    <!-- CARRITO -->
    <a href="carrito.php" class="icon-btn" title="Carrito">
        <i class="fas fa-shopping-cart"></i>
        <span class="icon-badge">
            <?php echo intval($cartCount); ?>
        </span>
    </a>

</div>
    </div>
</header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- SECCIÓN HERO -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">¡Bienvenido a Ingeniosos!</h1>
                <p class="hero-subtitle">
                    Descubre una colección especial de libros didácticos diseñados para estimular la creatividad, 
                    el aprendizaje y la imaginación de los más pequeños.
                </p>
                <button type="button" class="hero-btn" onclick="document.getElementById('categories-section').scrollIntoView({ behavior: 'smooth' });">
                    <i class="fas fa-star"></i> Explorar Ahora
                </button>
            </div>
        </section>

        <!-- TÍTULO DE SECCIÓN CATEGORÍAS -->
        <div class="text-center mb-4">
            <h2 class="section-title">¡Gran diversión para los más pequeños!</h2>
            <p class="section-subtitle">Elige una categoría y descubre los libros perfectos para cada edad.</p>
        </div>

        <!-- GRID DE CATEGORÍAS -->
        <div id="categories-section" class="categories-grid">
            <?php
            require_once __DIR__ . '/../config/conexion.php';

            $categories = [];
            if (isset($conexion)) {
                $check = $conexion->query("SHOW TABLES LIKE 'categorias'");
                if ($check && $check->num_rows > 0) {
                    $result = $conexion->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $categories[] = [
                                'id_categoria' => $row['id_categoria'],
                                'nombre' => $row['nombre_categoria']
                            ];
                        }
                        $result->free();
                    }
                }
            }

            if (empty($categories)) {
                $categories = [
                    ['id_categoria' => null, 'nombre' => '0-6 meses'],
                    ['id_categoria' => null, 'nombre' => '6-12 meses'],
                    ['id_categoria' => null, 'nombre' => '1-2 años'],
                    ['id_categoria' => null, 'nombre' => '3-4 años'],
                    ['id_categoria' => null, 'nombre' => 'Más de 5 años'],
                    ['id_categoria' => null, 'nombre' => 'Ver todo'],
                ];
            }

            foreach ($categories as $cat) :
                $catName = $cat['nombre'];
                $link = 'categoria.php';
                if (!empty($cat['id_categoria'])) {
                    $link .= '?categoria_id=' . intval($cat['id_categoria']);
                } elseif (strtolower(trim($catName)) !== 'ver todo') {
                    $link .= '?categoria=' . urlencode($catName);
                }

                $imagePath = '';
                $imagenBase = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $catName)));
                foreach (['jpeg', 'jpg', 'png'] as $ext) {
                    $candidate = "../assets/uploads/productos/{$imagenBase}.{$ext}";
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
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($catName); ?>" class="category-image">
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

        <section class="promo-section">
            <article class="promo-card">
                <i class="fas fa-truck-fast"></i>
                <h3>Envío rápido</h3>
                <p>Recibe tus libros didácticos en tiempo récord directamente en tu puerta.</p>
            </article>
            <article class="promo-card">
                <i class="fas fa-award"></i>
                <h3>Selección curada</h3>
                <p>Libros revisados para estimular habilidades cognitivas y creatividad.</p>
            </article>
            <article class="promo-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Garantía de satisfacción</h3>
                <p>Compra con confianza y regresa cuando quieras a revisar tus favoritos.</p>
            </article>
        </section>

        <?php
        $products = [];
        $filterCondition = "productos.estado IN ('activo', 'disponible')";
        $selectedCategoryName = '';
        $selectedBrandName = '';
        $searchQuery = '';
        $sortQuery = 'productos.nombre ASC';
        $brands = [];
        $hasBrand = false;
        $hasDescription = false;
        $showProducts = false;

        if (isset($conexion)) {
            $columnResult = $conexion->query("SHOW COLUMNS FROM productos");
            if ($columnResult) {
                while ($column = $columnResult->fetch_assoc()) {
                    if ($column['Field'] === 'marca') {
                        $hasBrand = true;
                    }
                    if ($column['Field'] === 'descripcion') {
                        $hasDescription = true;
                    }
                }
                $columnResult->free();
            }

            if ($hasBrand) {
                $brandResult = $conexion->query("SELECT DISTINCT marca FROM productos WHERE marca IS NOT NULL AND TRIM(marca) <> '' ORDER BY marca ASC");
                if ($brandResult) {
                    while ($row = $brandResult->fetch_assoc()) {
                        $brands[] = $row['marca'];
                    }
                    $brandResult->free();
                }
            }
        }

        if (isset($_GET['categoria_id'])) {
            $categoriaId = intval($_GET['categoria_id']);
            if ($categoriaId > 0) {
                $filterCondition .= " AND productos.id_categoria = $categoriaId";
                $showProducts = true;
            }
        } elseif (isset($_GET['categoria'])) {
            $categoriaName = $conexion->real_escape_string($_GET['categoria']);
            $filterCondition .= " AND (categorias.nombre_categoria = '$categoriaName' OR productos.categoria = '$categoriaName')";
            $selectedCategoryName = $_GET['categoria'];
            $showProducts = true;
        }

        if ($hasBrand && isset($_GET['marca']) && trim($_GET['marca']) !== '') {
            $selectedBrandName = $_GET['marca'];
            $brandValue = $conexion->real_escape_string($selectedBrandName);
            $filterCondition .= " AND productos.marca = '$brandValue'";
            $showProducts = true;
        }

        if (isset($_GET['q']) && trim($_GET['q']) !== '') {
            $searchQuery = trim($_GET['q']);
            $escapedSearch = $conexion->real_escape_string($searchQuery);
            $keyword = "%$escapedSearch%";
            $searchConditions = [
                "productos.nombre LIKE '$keyword'",
                "categorias.nombre_categoria LIKE '$keyword'"
            ];
            if ($hasDescription) {
                $searchConditions[] = "productos.descripcion LIKE '$keyword'";
            }
            if ($hasBrand) {
                $searchConditions[] = "productos.marca LIKE '$keyword'";
            }
            $filterCondition .= ' AND (' . implode(' OR ', $searchConditions) . ')';
            $showProducts = true;
        }

        if (isset($_GET['sort'])) {
            if ($_GET['sort'] === 'price_desc') {
                $sortQuery = 'productos.precio DESC';
            } elseif ($_GET['sort'] === 'price_asc') {
                $sortQuery = 'productos.precio ASC';
            }
        }

        if ($showProducts && isset($conexion)) {
            $sql = "SELECT productos.*, categorias.nombre_categoria
                    FROM productos
                    LEFT JOIN categorias ON productos.id_categoria = categorias.id_categoria
                    WHERE $filterCondition
                    ORDER BY $sortQuery";
            $result = $conexion->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                $result->free();
            }
        }

        $pageTitle = 'Libros didácticos';
        $pageSubtitle = 'Todos los productos disponibles en esta tienda.';
        if (!empty($searchQuery)) {
            $pageTitle = 'Resultados para "' . htmlspecialchars($searchQuery) . '"';
            $pageSubtitle = 'Libros didácticos que coinciden con tu búsqueda.';
        } elseif (!empty($selectedCategoryName)) {
            $pageTitle = 'Libros de ' . htmlspecialchars($selectedCategoryName);
            $pageSubtitle = 'Explora los productos de esta categoría.';
        } elseif (!empty($selectedBrandName)) {
            $pageTitle = 'Libros de ' . htmlspecialchars($selectedBrandName);
            $pageSubtitle = 'Explora los productos de esta marca.';
        }
        ?>

        <?php if ($showProducts) : ?>
            <div id="products-section" class="products-toolbar">
                <div>
                    <h2 class="section-title"><?php echo $pageTitle; ?></h2>
                    <p class="section-subtitle"><?php echo $pageSubtitle; ?></p>
                </div>
                <div class="products-controls">
                    <form method="get" class="search-form">
                        <input type="search" name="q" class="search-box" placeholder="Buscar libros, autores o temas" value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <?php if ($hasBrand && !empty($brands)) : ?>
                            <select name="marca" class="brand-filter">
                                <option value="">Todas las marcas</option>
                                <?php foreach ($brands as $brand) : ?>
                                    <option value="<?php echo htmlspecialchars($brand); ?>" <?php echo ($brand === $selectedBrandName) ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                        <?php if (isset($_GET['categoria_id'])): ?>
                            <input type="hidden" name="categoria_id" value="<?php echo intval($_GET['categoria_id']); ?>">
                        <?php elseif (isset($_GET['categoria'])): ?>
                            <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
                        <?php endif; ?>
                        <button type="submit" class="search-submit"><i class="fas fa-search"></i></button>
                    </form>
                    <span class="products-count"><?php echo count($products); ?> productos</span>
                    <form method="get" class="sort-form">
                        <?php if (isset($_GET['categoria_id'])): ?>
                            <input type="hidden" name="categoria_id" value="<?php echo intval($_GET['categoria_id']); ?>">
                        <?php elseif (isset($_GET['categoria'])): ?>
                            <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
                        <?php endif; ?>
                        <?php if (!empty($searchQuery)) : ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <?php endif; ?>
                        <?php if (!empty($selectedBrandName)) : ?>
                            <input type="hidden" name="marca" value="<?php echo htmlspecialchars($selectedBrandName); ?>">
                        <?php endif; ?>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_asc') ? 'selected' : ''; ?>>Precio menor a mayor</option>
                            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_desc') ? 'selected' : ''; ?>>Precio mayor a menor</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="products-grid">
                <?php
                if (empty($products)) {
                    echo '<div class="no-products">No hay productos disponibles en esta categoría.</div>';
                } else {
                    foreach ($products as $prod) :
                        $productImage = '';
                        if (isset($conexion)) {
                            $imageQuery = "SELECT imagen FROM producto_imagenes WHERE id_producto = " . intval($prod['id_producto']) . " LIMIT 1";
                            $imgResult = $conexion->query($imageQuery);
                            if ($imgResult && $imgResult->num_rows > 0) {
                                $imageRow = $imgResult->fetch_assoc();
                                $productImage = $imageRow['imagen'];
                            }
                        }
                        $productCategory = !empty($prod['nombre_categoria']) ? $prod['nombre_categoria'] : (!empty($prod['categoria']) ? $prod['categoria'] : 'Sin categoría');
                        $productUrl = 'detalle_producto.php?id=' . intval($prod['id_producto']);
                        ?>
                        <?php $productId = intval($prod['id_producto']);
                        $isFavorite = in_array($productId, $favoriteIds, true);
                        ?>
                        <article class="product-card">
                            <div class="product-image-wrapper">
                                <?php if (!empty($productImage)) : ?>
                                    <img src="../assets/uploads/productos/<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>" class="product-image">
                                <?php else : ?>
                                    <svg viewBox="0 0 400 300" class="product-placeholder">
                                        <rect width="400" height="300" fill="#f1f7fb"/>
                                        <text x="200" y="160" font-size="24" text-anchor="middle" fill="#94a3b8">Sin imagen</text>
                                    </svg>
                                <?php endif; ?>
                                <a href="<?php echo $productUrl; ?>" class="product-overlay">
                                    <span>Ver más</span>
                                </a>
                            </div>
                            <div class="product-card-body">
                                <div class="product-card-top">
                                    <span class="badge-category"><?php echo htmlspecialchars($productCategory); ?></span>
                                    <form method="post" action="favoritos.php" class="favorite-form">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                        <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                        <button type="submit" class="favorite-btn<?php echo $isFavorite ? ' active' : ''; ?>" title="Agregar a favoritos">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </form>
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
                    <?php endforeach;
                }
                ?>
            </div>
        <?php else : ?>
            <div class="select-category-state">
                <h2 class="section-title">Selecciona una categoría</h2>
                <p class="section-subtitle">Elige una categoría y aquí aparecerán los productos disponibles.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-section">
                <h3>Sobre Ingeniosos</h3>
                <p>Somos una tienda de libros didácticos pensada para que los niños aprendan jugando. Encontrarás opciones para cada edad y etapa de aprendizaje.</p>
            </div>
            <div class="footer-section">
                <h3>Beneficios</h3>
                <ul>
                    <li>Envío exprés a todo el país</li>
                    <li>Selección curada por expertos</li>
                    <li>Protección contra devoluciones</li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <p>ingeniosos@example.com</p>
                <p>+591 700 000 000</p>
                <p>La Paz, Bolivia</p>
            </div>
            <div class="footer-section footer-links">
                <h3>Enlaces rápidos</h3>
                <a href="productos.php">Inicio</a>
                <a href="favoritos.php">Favoritos</a>
                <a href="carrito.php">Carrito</a>
                <a href="historial.php">Historial</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© Ingeniosos 2026. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="../assets/js/app.js"></script>

    <script>
const profileBtn = document.getElementById("profileBtn");
const profileDropdown = document.getElementById("profileDropdown");

profileBtn.addEventListener("click", () => {
    profileDropdown.classList.toggle("show");
});

document.addEventListener("click", (e) => {
    if (!document.querySelector(".profile-menu").contains(e.target)) {
        profileDropdown.classList.remove("show");
    }
});
</script>
</body>
</html>
