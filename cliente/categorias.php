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
$useCategoriasTable = false;

if (isset($conexion)) {
    $check = $conexion->query("SHOW TABLES LIKE 'categorias'");
    if ($check && $check->num_rows > 0) {
        $col = $conexion->query("SHOW COLUMNS FROM categorias LIKE 'id_categoria'");
        $col2 = $conexion->query("SHOW COLUMNS FROM categorias LIKE 'nombre_categoria'");
        if ($col && $col->num_rows > 0 && $col2 && $col2->num_rows > 0) {
            $useCategoriasTable = true;
        }
    }

    if ($useCategoriasTable) {
        $result = $conexion->query("SELECT id_categoria, nombre_categoria, medida FROM categorias ORDER BY nombre_categoria ASC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id_categoria' => $row['id_categoria'],
                    'nombre' => $row['nombre_categoria'],
                    'imagen' => ''
                ];
            }
            $result->free();
        }
    } else {
        $result = $conexion->query("SHOW TABLES LIKE 'productos'");
        if ($result && $result->num_rows > 0) {
            $col = $conexion->query("SHOW COLUMNS FROM productos LIKE 'categoria'");
            if ($col && $col->num_rows > 0) {
                $result2 = $conexion->query("SELECT DISTINCT categoria AS nombre FROM productos ORDER BY categoria ASC");
                if ($result2 && $result2->num_rows > 0) {
                    while ($row = $result2->fetch_assoc()) {
                        $categories[] = [
                            'id_categoria' => null,
                            'nombre' => $row['nombre'],
                            'imagen' => ''
                        ];
                    }
                    $result2->free();
                }
            }
        }
    }
}

if (empty($categories)) {
    $categories = [
        ['id_categoria' => 1, 'nombre' => 'A4', 'imagen' => 'a4.jpeg'],
        ['id_categoria' => 2, 'nombre' => 'A5', 'imagen' => 'a5.jpeg'],
        ['id_categoria' => 3, 'nombre' => 'Cartulina', 'imagen' => 'cartulina.jpeg'],
        ['id_categoria' => 4, 'nombre' => 'Temas escolares', 'imagen' => 'temas-escolares.jpeg'],
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Ingeniosos</title>
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
                <a href="productos.php" class="nav-btn">Libros</a>
                <button class="nav-btn active">Categorías</button>
            </nav>

            <div class="search-container">
                <input type="text" class="search-box" placeholder="Buscar categoría..." disabled>
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
                <h1 class="hero-title">Categorías de libros</h1>
                <p class="hero-subtitle">Elige una categoría para ver los productos disponibles.</p>
            </div>
        </section>

        <div class="text-center mb-4">
            <h2 class="section-title">Categorías</h2>
            <p class="section-subtitle">Estas son las opciones disponibles en tu tienda.</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $category) :
                $categoryName = $category['nombre'];
                $imagePath = '';
                if (!empty($category['imagen'])) {
                    $imagePath = "../assets/uploads/productos/" . $category['imagen'];
                }
                if (empty($imagePath)) {
                    $imagenBase = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $categoryName)));
                    foreach (['jpeg', 'jpg', 'png'] as $ext) {
                        $candidate = "../assets/uploads/productos/{$imagenBase}.{$ext}";
                        if (file_exists(__DIR__ . '/' . $candidate)) {
                            $imagePath = $candidate;
                            break;
                        }
                    }
                }
                $link = 'productos.php';
                if (!empty($category['id_categoria'])) {
                    $link .= '?categoria_id=' . intval($category['id_categoria']);
                } else {
                    $link .= '?categoria=' . urlencode($categoryName);
                }
            ?>
                <a href="<?php echo htmlspecialchars($link); ?>" class="category-card-link">
                    <div class="category-card">
                        <div class="category-image-wrapper">
                            <?php if (!empty($imagePath)) : ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($categoryName); ?>" class="category-image">
                            <?php else : ?>
                                <svg viewBox="0 0 200 220" class="placeholder-svg">
                                    <rect width="200" height="220" fill="#f5f5f5"/>
                                    <text x="100" y="110" font-size="22" text-anchor="middle" fill="#555"><?php echo htmlspecialchars($categoryName); ?></text>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="category-info">
                            <h3 class="category-title"><?php echo htmlspecialchars($categoryName); ?></h3>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="../assets/js/app.js"></script>
</body>
</html>
