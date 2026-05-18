<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario"])) {

    header("Location: /evaluacion3Web/Evaluacion3/login/login.php");
    exit();

}

?>