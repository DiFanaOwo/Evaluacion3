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

        header("Location: dashboard.php");
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
    <title>Verificar 2FA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-secondary d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow" style="width:400px;">

    <h2 class="text-center mb-4">Verificación 2FA</h2>

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

        <button class="btn btn-success w-100">
            Verificar
        </button>

    </form>

</div>

</body>
</html>