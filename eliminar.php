<?php
session_start();
require 'conexion.php'; 

// 1. SEGURIDAD: Proteger acceso y verificar ID
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] != "admin" || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id_recibo = intval($_GET['id']);
$turno_actual = $_SESSION["turno"]; 
$mensaje = "";

// 2. Lógica de ELIMINACIÓN
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirmar_eliminar'])){
    
    $sql_delete = "DELETE FROM " . $tabla_recibos . " WHERE id = ?";
    $stmt_delete = $conexion->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_recibo);

    if ($stmt_delete->execute()) {
        // Éxito: Redirigir al index
        header("Location: index.php?mensaje=eliminado");
        exit();
    } else {
        $mensaje = "❌ Error al eliminar el recibo: " . $stmt_delete->error;
    }
    $stmt_delete->close();
}

// 3. Lógica para OBTENER DATOS DEL RECIBO para mostrar en la confirmación
// Esto asegura que el ID realmente existe antes de mostrar la página
$sql_check = "SELECT id, nombre, total_pagar FROM " . $tabla_recibos . " WHERE id = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("i", $id_recibo);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if($result_check->num_rows === 0) {
    header("Location: index.php");
    exit();
}
$row = $result_check->fetch_assoc();
$stmt_check->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmar Eliminación</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
/* --- Estilos de Advertencia (PROFESIONAL) --- */
:root {
    --color-primary: #3f51b5; 
    --color-warning: #ff9800; /* Naranja para advertencia */
    --color-error: #d32f2f; /* Rojo oscuro para acción destructiva */
    --color-background: #f5f5f5;
    --color-surface: #ffffff;
    --shadow-medium: 0 4px 12px rgba(0,0,0,0.15);
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--color-background);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.warning-box {
    background: var(--color-surface);
    padding: 40px;
    width: 100%;
    max-width: 500px;
    border-radius: 12px;
    box-shadow: var(--shadow-medium);
    text-align: center;
    border-top: 5px solid var(--color-warning); /* Línea de advertencia */
}

h1 {
    color: var(--color-error); /* Título de peligro */
    font-weight: 700;
    margin-bottom: 20px;
}

.icon {
    font-size: 3rem;
    color: var(--color-warning);
    margin-bottom: 15px;
    display: block;
}

p {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 30px;
}
strong {
    font-weight: 700;
    color: #333;
}

/* Botones */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.delete-confirm-button {
    background: var(--color-error); /* ROJO DESTRUCCIÓN */
    color: white; 
}
.delete-confirm-button:hover { 
    background: #a10000;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.cancel-button {
    background: #bdbdbd; /* Gris para cancelar */
    color: #333;
}
.cancel-button:hover {
    background: #9e9e9e;
    transform: translateY(-2px);
}
</style>

</head>
<body>

<div class="warning-box">

    <span class="icon">⚠</span>
    <h1>CONFIRMAR ELIMINACIÓN</h1>

    <p>Estás a punto de *eliminar permanentemente* el siguiente recibo del turno <strong><?= ucfirst($turno_actual) ?></strong>:</p>

    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px dashed var(--color-warning);">
        <p style="margin: 0; font-size: 1rem;">
            ID: <strong><?= $row['id'] ?></strong><br>
            Nombre: <strong><?= htmlspecialchars($row['nombre']) ?></strong><br>
            Total: <strong>$<?= number_format($row['total_pagar'], 2) ?></strong>
        </p>
    </div>

    <p style="margin-top: 25px;">Esta acción no se puede deshacer. ¿Deseas continuar?</p>

    <form method="POST">
        <div class="action-buttons">
            <a href="index.php" class="btn cancel-button">Cancelar y Volver</a>
            <button type="submit" name="confirmar_eliminar" value="1" class="btn delete-confirm-button">
                SÍ, ELIMINAR RECIBO
            </button>
        </div>
    </form>

</div>
</body>
</html>
