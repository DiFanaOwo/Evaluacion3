<?php

session_start();

// Verificar si existe sesión
if(!isset($_SESSION["usuario"])){

    header("Location: ../login/login.php");
    exit();

}

?>