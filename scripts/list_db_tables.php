<?php
require_once __DIR__ . '/../config/conexion.php';
$res = $conexion->query('SHOW TABLES');
if (!$res) {
    echo 'ERROR: ' . $conexion->error . PHP_EOL;
    exit(1);
}
while ($r = $res->fetch_row()) {
    echo $r[0] . PHP_EOL;
}
