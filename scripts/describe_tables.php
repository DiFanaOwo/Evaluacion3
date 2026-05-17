<?php
require_once __DIR__ . '/../config/conexion.php';
$tables = ['metodos_pago', 'pedidos', 'detalle_pedido'];
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $res = $conexion->query("SHOW COLUMNS FROM $table");
    if (!$res) {
        echo "ERROR: " . $conexion->error . "\n";
        continue;
    }
    while ($row = $res->fetch_assoc()) {
        echo implode(' | ', [$row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Extra']]) . PHP_EOL;
    }
    echo PHP_EOL;
}
