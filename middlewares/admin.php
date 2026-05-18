<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario"])) {

    header("Location: /evaluacion3Web/Evaluacion3/login/login.php");
    exit();

}

if ($_SESSION["rol"] != "admin") {

    header("Location: /evaluacion3Web/Evaluacion3/index.php");
    exit();

}

?>