<?php
// ¡Necesitamos iniciar la sesión para leer $_SESSION['turno']!
session_start();

// --- Credenciales Fijas de Conexión a la Base de Datos ---
// Usaremos un único usuario de MySQL con permisos completos para simplificar,
// ya que la separación de datos se hace a nivel de tabla.
$db_host = "localhost";
$db_user = "u905897753_jecksan_user"; 
$db_pass = "SUPERCOOL11a";           
$db_name = "u905897753_recibo_bd";

// 1. Establecer la conexión con el servidor
$conexion = new mysqli(
    $db_host,
    $db_user,
    $db_pass,
    $db_name
);

if ($conexion->connect_errno) {
    echo "Error al conectar: " . $conexion->connect_error;
    exit();
}

// ---------------------------------------------------------------------------------

// 2. Definir la tabla de recibos basada en el turno
// Asume el turno 'matutino' si la sesión no existe o está vacía.
$turno = $_SESSION['turno'] ?? 'matutino'; 

switch ($turno) {
    case 'matutino':
        // Si tu tabla original se llama 'recibos'
        $tabla_recibos = "recibos"; 
        break;
    case 'vespertino':
        // Asegúrate de que esta tabla exista en tu base de datos
        $tabla_recibos = "recibos_vespertino";
        break;
    case 'dual':
        // Asegúrate de que esta tabla exista en tu base de datos
        $tabla_recibos = "recibos_dual";
        break;
    default:
        // Opción de seguridad por si el valor del turno es inesperado
        $tabla_recibos = "recibos"; 
}

// ¡IMPORTANTE! La variable $tabla_recibos ahora está disponible para usarse 
// en index.php, agregar.php, editar.php, etc.
?>
