<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["temp_usuario"])) {
    header("Location: login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigoIngresado = trim($_POST["codigo"]);
    $idUsuario = $_SESSION["temp_usuario"];

    $sql = "SELECT * FROM usuario
            WHERE id_usuario = ? AND codigo_2fa = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $idUsuario, $codigoIngresado);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        // Activar sesión
        $_SESSION["usuario"] = $usuario["id_usuario"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["rol"] = $usuario["rol"];

        // Actualizar estado
        $sqlUpdate = "UPDATE usuario
                      SET estado_2fa = 1
                      WHERE id_usuario = ?";

        $stmtUpdate = $conexion->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $usuario["id_usuario"]);
        $stmtUpdate->execute();

        unset($_SESSION["temp_usuario"]);

if ($usuario["rol"] == "admin") {
    header("Location: dashboard.php");
} else {
    header("Location: ../cliente/productos.php");
}

exit();

    } else {
        $mensaje = "Código incorrecto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación 2FA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="css.css">
</head>

<body>

<div class="auth-card">

    <h1 class="auth-title">
        Verificación 2FA
    </h1>

    <p class="auth-subtitle">
        Ingresa el código temporal para continuar
    </p>

    <div class="alert alert-warning text-center">

        Código temporal:

        <strong>
            <?php echo $_SESSION["codigo_mostrar"]; ?>
        </strong>

    </div>

    <?php if($mensaje != ""): ?>

        <div class="alert alert-danger">
            <?php echo $mensaje; ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <input type="text"
               name="codigo"
               class="form-control mb-3"
               placeholder="Ingrese el código">

        <button class="btn-auth">
            <i class="fas fa-check-circle"></i>
            Verificar
        </button>

    </form>

</div>

</body>
</html>