<?php
require_once 'db.php';
if ($conexion) {
    echo "Conexión OK. Usuario DB: " . htmlspecialchars($DB_USER);
} else {
    echo "No hay conexión.";
}
?>
