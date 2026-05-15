<?php

session_start();

// Verificar login
if(!isset($_SESSION["usuario"])){

    header("Location: ../login/login.php");
    exit();

}

// Verificar rol admin
if($_SESSION["rol"] != "admin"){

    header("Location: ../dashboard.php");
    exit();

}

?>