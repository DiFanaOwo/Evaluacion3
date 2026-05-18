<?php

session_start();
include("../config/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM usuario WHERE correo = ?";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param("s", $correo);

    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario["contraseña"])) {

            // Generar código 2FA
            $codigo2FA = rand(100000, 999999);

            // Guardar código
            $sqlUpdate = "UPDATE usuario
                          SET codigo_2fa = ?, estado_2fa = 0
                          WHERE id_usuario = ?";

            $stmtUpdate = $conexion->prepare($sqlUpdate);

            $stmtUpdate->bind_param(
                "si",
                $codigo2FA,
                $usuario["id_usuario"]
            );

            $stmtUpdate->execute();

            // Guardar usuario temporal
            $_SESSION["temp_usuario"] = $usuario["id_usuario"];

            // Simulación código
            $_SESSION["codigo_mostrar"] = $codigo2FA;

            header("Location: verificar_2fa.php");
            exit();

        } else {

            $mensaje = "Contraseña incorrecta";

        }

    } else {

        $mensaje = "Correo no encontrado";

    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css.css">

</head>

<body>

    <div class="auth-card">

        <h2 class="auth-title">
            Iniciar Sesión
        </h2>

        <p class="text-center mb-4">
            Bienvenido nuevamente
        </p>

        <?php if($mensaje != ""): ?>

            <div class="alert alert-danger">
                <?php echo $mensaje; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <input
                type="email"
                name="correo"
                class="form-control mb-3"
                placeholder="Correo electrónico"
                required>

            <input
                type="password"
                name="password"
                class="form-control mb-4"
                placeholder="Contraseña"
                required>

            <button class="btn-auth">
                Ingresar
            </button>

        </form>

        <div class="text-center mt-4">

            <a href="registro.php" class="auth-link">
                ¿No tienes cuenta? Regístrate
            </a>

        </div>

    </div>

</body>

</html>