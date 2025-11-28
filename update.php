<?php
session_start();
require 'conexion.php';

// Proteger acceso: solo el admin puede actualizar
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin"){
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capturar datos
    $id          = intval($_POST['id'] ?? 0); 
    $nombre      = $_POST['nombre'] ?? '';
    $rol         = $_POST['rol'] ?? '';
    $fecha       = $_POST['fecha'] ?? date('Y-m-d');
    $semestre    = $_POST['semestre'] ?? '';
    $turno       = $_POST['turno'] ?? 'matutino'; // <-- CAPTURA EL TURNO
    
    // Convierte valores monetarios
    $aportacion  = floatval(str_replace(',', '.', $_POST['aportacion_v'] ?? 0));
    $paraescolar = floatval(str_replace(',', '.', $_POST['paraescolar'] ?? 0));
    $credencial  = floatval(str_replace(',', '.', $_POST['credencial'] ?? 0));
    $otro        = floatval(str_replace(',', '.', $_POST['otro'] ?? 0));
    
    $firma_al    = $_POST['firma_alumno'] ?? '';
    $firma_en    = $_POST['firma_entrega'] ?? '';

    // 2. Calcular total
    $total = $aportacion + $paraescolar + $credencial + $otro;

    // 3. SENTENCIA PREPARADA (ACTUALIZA 'turno')
    $sql = "UPDATE recibos SET 
        nombre = ?, 
        rol = ?, 
        fecha = ?, 
        semestre = ?, 
        turno = ?,          
        aportacion_v = ?, 
        paraescolar = ?, 
        credencial = ?, 
        otro = ?, 
        total_pagar = ?, 
        firma_alumno = ?, 
        firma_entrega = ? 
        WHERE id = ?";

    $stmt = $conexion->prepare($sql);
    
    // Vincula 13 parámetros (12 campos + 1 ID)
    $stmt->bind_param("sssssdddddssi", 
        $nombre, $rol, $fecha, $semestre, $turno, $aportacion, $paraescolar, $credencial, $otro, $total, $firma_al, $firma_en, $id
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conexion->close();
        // Redireccionar al index
        header("Location: index.php"); 
        exit();
    } else {
        error_log("Error al actualizar el recibo: " . $stmt->error);
        $stmt->close();
        header("Location: index.php?error=update_fallido");
        exit();
    }
} else {
    // Si no es un método POST, redirigir
    header("Location: index.php");
    exit();
}
