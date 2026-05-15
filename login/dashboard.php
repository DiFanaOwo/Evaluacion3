<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card p-4 shadow">

        <h1>
            Bienvenido,
            <?php echo $_SESSION["nombre"]; ?>
        </h1>

        <p>
            Rol:
            <strong>
                <?php echo $_SESSION["rol"]; ?>
            </strong>
        </p>

        <a href="logout.php" class="btn btn-danger">
            Cerrar Sesión
        </a>

    </div>

</div>

</body>
</html>