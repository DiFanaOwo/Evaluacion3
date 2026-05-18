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

    // Validar patrón contraseña
    elseif (
        !preg_match(
            "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/",
            $password
        )
    ) {

        $mensaje = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula y un número";

    }

    // Confirmar contraseña
    elseif ($password != $confirmarPassword) {

        $mensaje = "Las contraseñas no coinciden";

    }

    else {

        // Verificar correo existente
        $sql = "SELECT id_usuario FROM usuario WHERE correo = ?";

        $stmt = $conexion->prepare($sql);

        $stmt->bind_param("s", $correo);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "El correo ya está registrado";

        } else {

            // Encriptar contraseña
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

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

    <link rel="stylesheet" href="css.css">

</head>

<body>

    <div class="auth-card">

        <h2 class="auth-title">
            Crear Cuenta
        </h2>

        <p class="text-center mb-4">
            Únete a nuestra tienda
        </p>

        <?php if ($mensaje != ""): ?>

            <div class="alert alert-warning">
                <?php echo $mensaje; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <input
                type="text"
                name="nombre"
                class="form-control mb-3"
                placeholder="Nombre Completo"
                required>

            <input
                type="email"
                name="correo"
                class="form-control mb-3"
                placeholder="Correo Electrónico"
                required>

            <input
                type="password"
                name="password"
                class="form-control mb-2"
                placeholder="Contraseña"
                required
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                title="Mínimo 8 caracteres, una mayúscula, una minúscula y un número">

           <small class="d-block mb-3" style="color: black;">
            Debe contener mínimo 8 caracteres,
            una mayúscula, una minúscula y un número.
            </small>

            <input
                type="password"
                name="confirmar_password"
                class="form-control mb-4"
                placeholder="Confirmar Contraseña"
                required>

            <button class="btn-auth">
                Registrarse
            </button>

        </form>

        <div class="text-center mt-4">

            <a href="../login/login.php" class="auth-link">
                Ya tengo cuenta
            </a>

        </div>

    </div>

</body>

</html>