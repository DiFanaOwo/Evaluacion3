<?php

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

$base_url = "/Evaluacion3/";

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Ingeniosos</title>

    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/styles.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/advanced.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/utilities.css"
    >

    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <style>

        html,
        body{
            height:100%;
        }

        body{
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .page-content{
            flex:1;
        }

    </style>

</head>

<body>

<!-- DECORACIÓN -->

<div class="geometric-decoration">

    <div
        class="shape-circle"
        style="
            width: 300px;
            height: 300px;
            background: var(--accent-yellow);
            top: 100px;
            left: -100px;
        "
    ></div>

</div>

<!-- HEADER -->

<header class="header">

    <div class="header-content">

        <!-- LOGO -->

        <a href="#" class="logo">

            <img
                src="../../assets/uploads/logo-ingeniosos.jpeg"
                alt="Logo Ingeniosos"
                class="logo-img-real"
            >

            <span class="logo-text">
                Ingeniosos
            </span>

        </a>


        

        <!-- SEARCH -->

        <div class="search-container">

            <input
                type="text"
                class="search-box"
                placeholder="¿Qué estás buscando?"
            >

        </div>

        <!-- ICONOS -->

        <div class="header-icons">

            <!-- PERFIL -->

                        <a
                href="<?= $base_url ?>login/login.php"
                class="icon-btn"
                title="Cerrar sesión"
            >

                <i class="fas fa-user"></i>

            </a>

            <?php if(isset($_SESSION["rol"]) && $_SESSION["rol"] == "cliente") { ?>

                <!-- FAVORITOS -->

                <a
                    href="<?= $base_url ?>cliente/favoritos.php"
                    class="icon-btn"
                    title="Favoritos"
                >

                    <i class="fas fa-heart"></i>

                    <span class="icon-badge">
                        3
                    </span>

                </a>

                <!-- CARRITO -->

                <a
                    href="<?= $base_url ?>cliente/carrito.php"
                    class="icon-btn"
                    title="Carrito"
                >

                    <i class="fas fa-shopping-cart"></i>

                    <span class="icon-badge">
                        1
                    </span>

                </a>

            <?php } ?>

            <?php if(isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>

                <!-- HISTORIAL -->

                <a
                    href="<?= $base_url ?>admin/ventas/index.php"
                    class="icon-btn"
                    title="Historial de Compras"
                >

                    <i class="fas fa-receipt"></i>

                </a>

            <?php } ?>

        </div>

    </div>

</header>

<!-- CONTENIDO -->

<div class="page-content">