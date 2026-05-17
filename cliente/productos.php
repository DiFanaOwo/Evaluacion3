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
            </nav>

            <!-- SEARCH BAR -->
            <div class="search-container">
                <input type="text" class="search-box" placeholder="¿Qué estás buscando?">
            </div>

            <!-- HEADER ICONS -->
            <div class="header-icons">
                <button class="icon-btn" title="Mi Perfil">
                    <i class="fas fa-user"></i>
                </button>
                <button class="icon-btn" title="Favoritos">
                    <i class="fas fa-heart"></i>
                    <span class="icon-badge">3</span>
                </button>
                <button class="icon-btn" title="Carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="icon-badge">1</span>
                </button>
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
                <button class="hero-btn">
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
        <div class="categories-grid">
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
                $link = 'productos.php';
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

        <?php
        $products = [];
        $filterCondition = "productos.estado IN ('activo', 'disponible')";
        $selectedCategoryName = '';
        $sortQuery = 'productos.nombre ASC';

        if (isset($_GET['categoria_id'])) {
            $categoriaId = intval($_GET['categoria_id']);
            if ($categoriaId > 0) {
                $filterCondition .= " AND productos.id_categoria = $categoriaId";
            }
        } elseif (isset($_GET['categoria'])) {
            $categoriaName = $conexion->real_escape_string($_GET['categoria']);
            $filterCondition .= " AND (categorias.nombre_categoria = '$categoriaName' OR productos.categoria = '$categoriaName')";
            $selectedCategoryName = $_GET['categoria'];
        }

        if (isset($_GET['sort'])) {
            if ($_GET['sort'] === 'price_desc') {
                $sortQuery = 'productos.precio DESC';
            } elseif ($_GET['sort'] === 'price_asc') {
                $sortQuery = 'productos.precio ASC';
            }
        }

        if (isset($conexion)) {
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
        ?>

        <div class="products-toolbar">
            <div>
                <h2 class="section-title"><?php echo !empty($selectedCategoryName) ? 'Libros de ' . htmlspecialchars($selectedCategoryName) : 'Libros didácticos'; ?></h2>
                <p class="section-subtitle"><?php echo !empty($selectedCategoryName) ? 'Explora los productos de esta categoría.' : 'Todos los productos disponibles en esta tienda.'; ?></p>
            </div>
            <div class="products-controls">
                <span class="products-count"><?php echo count($products); ?> productos</span>
                <form method="get" class="sort-form">
                    <?php if (isset($_GET['categoria_id'])): ?>
                        <input type="hidden" name="categoria_id" value="<?php echo intval($_GET['categoria_id']); ?>">
                    <?php elseif (isset($_GET['categoria'])): ?>
                        <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
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
                                <button type="button" class="favorite-btn" title="Agregar a favoritos">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <h3 class="product-title"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                            <p class="product-price">Bs <?php echo number_format($prod['precio'], 2, ',', '.'); ?></p>
                            <p class="product-stock"><?php echo intval($prod['stock']) > 0 ? 'En stock' : 'Agotado'; ?></p>
                            <div class="product-actions">
                                <button type="button" class="btn-add-cart">Añadir al carrito</button>
                                <a href="<?php echo $productUrl; ?>" class="btn-details">Más detalles</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach;
            }
            ?>
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="../assets/js/app.js"></script>
</body>
</html>
