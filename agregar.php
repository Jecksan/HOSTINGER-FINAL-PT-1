<?php
session_start();
// Asegúrate de que conexion.php define $tabla_recibos según $_SESSION['turno']
require 'conexion.php'; 

// 1. SEGURIDAD: Proteger acceso. Solo el rol 'admin' puede agregar.
if(!isset($_SESSION["usuario"]) || $_SESSION["rol"] != "admin"){
    header("Location: index.php");
    exit();
}

// Variables de sesión
$usuario_actual = $_SESSION["usuario"];
$rol_actual = $_SESSION["rol"];
$turno_actual = $_SESSION["turno"]; 

$mensaje = ""; // Mensaje de éxito o error

// Lógica de procesamiento del formulario
if($_SERVER["REQUEST_METHOD"] === "POST"){

    // Captura y saneamiento de datos POST
    $nombre        = $_POST['nombre'];
    $rol_recibo    = $_POST['rol_recibo']; 
    $fecha         = $_POST['fecha'];
    $semestre      = $_POST['semestre'];
    
    // CAPTURA Y CONVERSIÓN A DECIMAL (floatval)
    $aportacion_v  = floatval($_POST['aportacion_v']);
    $paraescolar   = floatval($_POST['paraescolar']);
    $credencial    = floatval($_POST['credencial']);
    $otro          = floatval($_POST['otro']);
    $total_pagar   = floatval($_POST['total_pagar']); 
    
    $firma_alumno  = $_POST['firma_alumno'];
    $firma_entrega = $_POST['firma_entrega'];
    
    // CONSULTA SQL: Usa la variable $tabla_recibos
    $sql = "INSERT INTO " . $tabla_recibos . " 
            (nombre, rol, fecha, semestre, aportacion_v, paraescolar, credencial, otro, total_pagar, firma_alumno, firma_entrega) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

    $stmt = $conexion->prepare($sql);

    // CORRECCIÓN CRÍTICA DE BIND_PARAM: ssssdddddds (11 tipos)
    $stmt->bind_param("ssssdddddds", 
        $nombre, 
        $rol_recibo, 
        $fecha, 
        $semestre,
        $aportacion_v, 
        $paraescolar, 
        $credencial, 
        $otro, 
        $total_pagar, 
        $firma_alumno, 
        $firma_entrega
    );

    if ($stmt->execute()) {
        $mensaje = "✅ Recibo agregado exitosamente al turno " . ucfirst($turno_actual) . ".";
        header("Location: index.php"); 
        exit();
    } else {
        $mensaje = "❌ Error al agregar el recibo: " . $stmt->error;
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agregar Recibo (<?= ucfirst($turno_actual) ?>)</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
/* --- Estilos del formulario (PROFESIONAL) --- */
:root {
    --color-primary: #3f51b5; /* Azul índigo */
    --color-secondary: #00bcd4; /* Cian */
    --color-success: #4caf50; /* Verde para acciones positivas */
    --color-error: #ef5350; /* Rojo para errores */
    --color-background: #f5f5f5;
    --color-surface: #ffffff;
    --shadow-medium: 0 4px 12px rgba(0,0,0,0.15);
}

body{
    font-family: 'Roboto', sans-serif; 
    background-color: var(--color-background); 
    margin: 0; 
    padding: 0;
    color: #333;
}
.container{
    width: 95%; 
    max-width: 800px; 
    margin: 40px auto;
    animation: fadeIn 0.8s ease-out;
}
@keyframes fadeIn{
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}

h1{
    text-align: center; 
    color: var(--color-primary);
    font-weight: 500;
    margin-bottom: 30px;
}
h2 {
    color: #555;
    font-size: 1.2rem;
    font-weight: 400;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
    margin-top: 30px;
}

form { 
    background: var(--color-surface); 
    padding: 35px; 
    border-radius: 12px; 
    box-shadow: var(--shadow-medium); 
}

label { 
    display: block; 
    margin-top: 15px; 
    font-weight: 500; 
    color: #555;
    font-size: 0.9rem;
}

input[type="text"], input[type="date"], input[type="number"], select { 
    width: 100%; 
    padding: 12px 10px; 
    margin-top: 6px; 
    border: 1px solid #ddd; 
    border-radius: 4px; 
    box-sizing: border-box; 
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
}
input:focus, select:focus {
    border-color: var(--color-secondary);
    box-shadow: 0 0 0 2px rgba(0, 188, 212, 0.2);
    outline: none;
}

/* Campo Total (Solo lectura) */
#total_pagar {
    background-color: #e3f2fd !important;
    font-weight: 700 !important;
    color: var(--color-primary) !important;
}

/* BOTÓN PRINCIPAL: GUARDAR RECIBO */
.main-button { 
    background: var(--color-success); /* Verde para la acción de guardar */
    color: white; 
    padding: 15px 25px; 
    border: none; 
    border-radius: 4px; 
    cursor: pointer; 
    margin-top: 30px; 
    font-weight: 700; 
    font-size: 1.1rem;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    width: auto;
}
.main-button:hover { 
    background: #388e3c;
    transform: translateY(-2px); 
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

/* BOTÓN SECUNDARIO: VOLVER */
.back-link {
    float: right; 
    text-decoration: none; 
    color: #757575; 
    margin-top: 40px;
    padding: 10px;
    transition: color 0.3s;
    font-weight: 500;
}
.back-link:hover {
    color: var(--color-primary);
}

.mensaje { 
    padding: 10px; 
    margin-top: 10px; 
    border-radius: 4px; 
    text-align: center; 
    font-weight: 500;
}
.mensaje.success { background-color: #e8f5e9; color: var(--color-success); border-left: 5px solid var(--color-success); }
.mensaje.error { background-color: #ffebee; color: var(--color-error); border-left: 5px solid var(--color-error); }
</style>

<script>
function calcularTotal() {
    const getVal = (id) => parseFloat(document.getElementById(id).value) || 0;
    
    const aportacion = getVal('aportacion_v');
    const paraescolar = getVal('paraescolar');
    const credencial = getVal('credencial');
    const otro = getVal('otro');

    const total = aportacion + paraescolar + credencial + otro;

    document.getElementById('total_pagar').value = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', calcularTotal);
</script>

</head>
<body>

<div class="container">

<h1>Agregar Nuevo Recibo (Turno <?= ucfirst($turno_actual) ?>)</h1>

<?php if($mensaje != "" && strpos($mensaje, '✅') === false){ ?>
<div class="mensaje error">
    <?= $mensaje ?>
</div>
<?php } ?>

<form method="POST">
    
    <h2>Datos del Recibo</h2>

    <label for="nombre">Nombre del Alumno:</label>
    <input type="text" name="nombre" required>
    
    <label for="rol_recibo">Rol del Recibo:</label>
    <select name="rol_recibo" required>
        <option value="alumno1">Alumno1</option>
        <option value="visitante1">Visitante1</option>
        <option value="admin">Admin</option>
    </select>
    
    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
    
    <label for="semestre">Semestre:</label>
    <input type="text" name="semestre" required>
    
    <hr style="margin-top: 30px; border-color: #eee;">
    
    <h2>Conceptos a Pagar</h2>
    
    <label for="aportacion_v">Aportación:</label>
    <input type="number" step="0.01" min="0" id="aportacion_v" name="aportacion_v" value="0.00" oninput="calcularTotal()" required>

    <label for="paraescolar">Paraescolar:</label>
    <input type="number" step="0.01" min="0" id="paraescolar" name="paraescolar" value="0.00" oninput="calcularTotal()" required>

    <label for="credencial">Credencial:</label>
    <input type="number" step="0.01" min="0" id="credencial" name="credencial" value="0.00" oninput="calcularTotal()" required>

    <label for="otro">Otro:</label>
    <input type="number" step="0.01" min="0" id="otro" name="otro" value="0.00" oninput="calcularTotal()" required>

    <label for="total_pagar">Total a Pagar:</label>
    <input type="text" id="total_pagar" name="total_pagar" value="0.00" readonly>
    
    <hr style="margin-top: 30px; border-color: #eee;">
    
    <h2>Firmas</h2>
    
    <label for="firma_alumno">Firma Alumno:</label>
    <input type="text" name="firma_alumno">
    
    <label for="firma_entrega">Firma Entrega (Usuario actual):</label>
    <input type="text" name="firma_entrega" value="<?= $usuario_actual ?>" readonly style="background:#f9f9f9;">

    <button type="submit" class="main-button">Guardar Recibo</button>
    <a href="index.php" class="back-link">← Volver al listado</a>
</form>

</div>
</body>
</html>
