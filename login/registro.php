<?php
include("../config/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);
    $confirmarPassword = trim($_POST["confirmar_password"]);

    // Validar campos vacíos
    if (
        empty($nombre) ||
        empty($correo) ||
        empty($password) ||
        empty($confirmarPassword)
    ) {

        $mensaje = "Todos los campos son obligatorios";

    }
    // Validar confirmación de contraseña
    elseif ($password != $confirmarPassword) {

        $mensaje = "Las contraseñas no coinciden";

    }
    else {

        // Verificar si el correo ya existe
        $sql = "SELECT id_usuario FROM usuario WHERE correo = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $correo);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "El correo ya está registrado";

        } else {

            // Encriptar contraseña
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar usuario
            $sqlInsert = "INSERT INTO usuario
            (nombre, correo, contraseña, rol)
            VALUES (?, ?, ?, 'cliente')";

            $stmtInsert = $conexion->prepare($sqlInsert);

            $stmtInsert->bind_param(
                "sss",
                $nombre,
                $correo,
                $passwordHash
            );

            if ($stmtInsert->execute()) {

                $mensaje = "Usuario registrado correctamente";

            } else {

                $mensaje = "Error al registrar";

            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="width: 400px; border-radius: 15px;">

        <h2 class="text-center mb-4">
            Registro de Usuario
        </h2>

        <?php if ($mensaje != ""): ?>

            <div class="alert alert-info">
                <?php echo $mensaje; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control mb-3"
                placeholder="Nombre Completo">

            <input
                type="email"
                name="correo"
                class="form-control mb-3"
                placeholder="Correo Electrónico">

            <input
                type="password"
                name="password"
                class="form-control mb-3"
                placeholder="Contraseña">

            <input
                type="password"
                name="confirmar_password"
                class="form-control mb-3"
                placeholder="Confirmar Contraseña">

            <button class="btn btn-primary w-100">
                Registrarse
            </button>

        </form>

        <a href="login.php" class="text-center mt-3">
            Ya tengo cuenta
        </a>

    </div>

</body>

</html>