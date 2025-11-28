<?php
session_start();
require 'conexion.php'; 

// Proteger acceso
if(!isset($_SESSION["usuario"])){
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$rol = $_SESSION["rol"];
$turno = $_SESSION["turno"]; 

// Consulta a la tabla específica del turno (usando $tabla_recibos de conexion.php)
$sql = "SELECT * FROM " . $tabla_recibos . " ORDER BY id DESC";
$result = $conexion->query($sql);

// Define la condición para mostrar XXXXX: solo el rol "visitante"
$ocultar_datos = ($rol == "visitante");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard de Recibos | Turno <?= ucfirst($turno) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
<style>
/* ------------------------------------------------------------------------- */
/* --- VARIABLES Y GENERALES --- */
/* ------------------------------------------------------------------------- */
:root {
    --color-primary: #00bcd4; /* Cian Brillante */
    --color-secondary: #ff9800; /* Naranja para énfasis */
    --color-background: #121212; /* Fondo oscuro (Dark Mode) */
    --color-surface: #1e1e1e; /* Superficie oscura */
    --color-text: #e0e0e0; /* Texto claro */
    --shadow-deep: 0 10px 30px rgba(0, 188, 212, 0.4);
    --shadow-light: 0 2px 4px rgba(0,0,0,0.4);
}

body{
    font-family: 'Montserrat', sans-serif;
    background-color: var(--color-background);
    margin: 0;
    padding: 0;
    color: var(--color-text);
    /* Fondo animado sutil */
    background: linear-gradient(135deg, #121212 0%, #1a1a1a 100%);
    animation: backgroundShift 30s ease infinite alternate;
}

/* Animación de fondo */
@keyframes backgroundShift {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}

.container{
    width: 95%;
    max-width: 1300px;
    margin: 50px auto;
    padding-bottom: 50px;
    /* Animación de entrada general */
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn{
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: translateY(0);}
}

/* ------------------------------------------------------------------------- */
/* --- CABECERA (LOGO, TÍTULO Y LOGOUT) --- */
/* ------------------------------------------------------------------------- */
.header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 30px;
    border-bottom: 3px solid var(--color-primary);
}

.logo-container {
    display: flex;
    align-items: center;
}

.logo-container img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
    box-shadow: var(--shadow-light);
    transition: transform 0.3s ease;
}
.logo-container img:hover {
    transform: rotate(10deg) scale(1.1); /* Efecto en hover */
    box-shadow: 0 0 10px var(--color-primary);
}

h1{
    font-size: 2.2rem;
    color: var(--color-primary);
    font-weight: 800;
    margin: 0;
    letter-spacing: 1px;
    text-shadow: 0 0 5px rgba(0, 188, 212, 0.5); /* Brillo sutil */
}
h1 small {
    display: block;
    font-size: 0.8rem;
    font-weight: 400;
    color: #999;
    margin-top: 5px;
}

/* ------------------------------------------------------------------------- */
/* --- BOTÓN AGREGAR (CALL TO ACTION) --- */
/* ------------------------------------------------------------------------- */
.add-btn{
    background: var(--color-secondary); /* Naranja */
    color: var(--color-surface);
    padding: 12px 25px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.add-btn:hover{
    background: #ffb300;
    transform: translateY(-3px); /* Efecto 3D */
    box-shadow: 0 8px 20px rgba(255, 152, 0, 0.6);
}

/* ------------------------------------------------------------------------- */
/* --- LOGOUT (Barra Superior) --- */
/* ------------------------------------------------------------------------- */
.logout-top{
    padding: 8px 15px;
    background: #333; /* Fondo oscuro */
    color: var(--color-text);
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid #555;
}
.logout-top:hover{
    background: #444;
    color: var(--color-primary);
    transform: scale(1.05);
}


/* ------------------------------------------------------------------------- */
/* --- TABLA DE DATOS (DATA GRID) --- */
/* ------------------------------------------------------------------------- */
table{
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0;
    background: var(--color-surface);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-deep); 
    margin-top: 25px;
    border: 1px solid #333;
}

/* Encabezados y Celdas de tabla */
th, td{
    padding: 14px 12px;
    border-bottom: 1px solid #333; /* Separador sutil */
    font-size: 0.9rem;
    text-align: left; 
    transition: background-color 0.3s ease;
}

th{
    background: #282828; /* Encabezado más oscuro */
    color: var(--color-primary); /* Texto Cian */
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 1px solid var(--color-primary); /* Línea de resalte */
}

/* RESALTADO Y TRANSICIONES */
tr:nth-child(even) { background-color: #212121; } /* Filas alternas */
tr:hover {
    background-color: #2e2e2e !important; 
    box-shadow: inset 4px 0 0 var(--color-secondary); /* Barra lateral Naranja */
    transform: scale(1.005); /* Zoom sutil en la fila */
    transition: all 0.2s ease;
}

/* FORMATO NUMÉRICO Y TOTAL */
td:nth-child(6), td:nth-child(7), td:nth-child(8), td:nth-child(9), td:nth-child(10) {
    font-weight: 600;
    text-align: right; 
    color: #4caf50; /* Verde brillante para números */
}
td:nth-child(10) { /* Total */
    font-weight: 800;
    color: var(--color-primary);
    background-color: #2f3e46; /* Fondo distinto para Total */
    text-shadow: 0 0 5px rgba(0, 188, 212, 0.5);
}

/* SOLUCIÓN AL PROBLEMA DE BOTONES DE ACCIÓN */
td:last-child {
    min-width: 150px; 
    white-space: nowrap; 
}
th:last-child {
    min-width: 150px;
}

/* ------------------------------------------------------------------------- */
/* --- BOTONES DE ACCIÓN --- */
/* ------------------------------------------------------------------------- */
.action-buttons {
    display: flex;
    gap: 8px; 
    align-items: center;
}

.btn{
    padding: 8px 12px;
    border-radius: 4px;
    color: var(--color-surface); /* Texto oscuro */
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.4);
    text-transform: uppercase;
}

.edit{ background:#ffb300; /* Amarillo/Naranja */ }
.edit:hover{ background:#e69a00; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(255, 152, 0, 0.4); }

.delete{ background:#ef5350; /* Rojo */ }
.delete:hover{ background:#c62828; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(239, 83, 80, 0.4); }
</style>

</head>
<body>

<div class="container">

    <div class="header-bar">
        <div class="logo-container">
            <img src="jx.jpeg" alt="Logo de Gestión">
            <h1>Gestión de Recibos <small>Turno <?= ucfirst($turno) ?></small></h1>
        </div>
        
        <div style="display: flex; gap: 15px; align-items: center;">
            <?php 
            // 2. Restricción de permiso: Solo 'admin' puede ver el botón Agregar
            if($rol == "admin"){ 
            ?>
                <a href="agregar.php" class="add-btn">Añadir Nuevo Recibo</a>
            <?php 
            } 
            ?>
            <a class="logout-top" href="logout.php">Cerrar Sesión (<?= $usuario ?>)</a>
        </div>
    </div>

    <table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre del Alumno</th>
        <th>Rol</th>
        <th>Fecha</th>
        <th>Semestre</th>
        <th style="text-align: right;">Aportación</th>
        <th style="text-align: right;">Paraescolar</th>
        <th style="text-align: right;">Credencial</th>
        <th style="text-align: right;">Otro</th>
        <th style="text-align: right;">TOTAL</th>
        <th>Firma Alumno</th>
        <th>Firma Entrega</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($result->num_rows > 0) {
        // Inicializar el contador para aplicar animación de fila
        $delay_count = 0; 
        while($row = $result->fetch_assoc()){
            $delay_count++;
            // Aplicar un retraso de animación a cada fila para el efecto "cascada"
            echo '<tr style="animation: slideIn 0.5s ease-out backwards; animation-delay: ' . ($delay_count * 0.05) . 's;">';
    ?>
    
    <style>
    @keyframes slideIn {
        from {opacity: 0; transform: translateX(-20px);}
        to {opacity: 1; transform: translateX(0);}
    }
    </style>

        <td><?= $row["id"] ?></td>

        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["nombre"]) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["rol"]) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["fecha"]) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["semestre"]) ?></td>
        
        <td><?= ($ocultar_datos) ? "XXXXX" : '$' . number_format($row["aportacion_v"], 2) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : '$' . number_format($row["paraescolar"], 2) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : '$' . number_format($row["credencial"], 2) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : '$' . number_format($row["otro"], 2) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : '$' . number_format($row["total_pagar"], 2) ?></td>

        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["firma_alumno"]) ?></td>
        <td><?= ($ocultar_datos) ? "XXXXX" : htmlspecialchars($row["firma_entrega"]) ?></td>

        <td>
            <div class="action-buttons">
            <?php 
            // 3. Restricción de permiso: Solo 'admin' puede ver los botones de acción
            if($rol=="admin"){ 
            ?>
                <a class="btn edit" href="editar.php?id=<?= $row['id'] ?>">Editar</a>
                <a class="btn delete" href="eliminar.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('¿Confirma la eliminación del recibo #<?= $row['id'] ?>?');">Eliminar</a>
            <?php 
            } else { 
                echo "—"; // Alumno y Visitante ven un guion en la columna de acciones
            } 
            ?>
            </div>
        </td>
    </tr>

    <?php 
        } 
    } else {
        // Mostrar mensaje si no hay resultados
        echo '<tr><td colspan="13" style="text-align:center; padding: 20px; color: #888;">No se encontraron recibos en el turno ' . ucfirst($turno) . '.</td></tr>';
    }
    ?>
    </tbody>
    </table>

</div>
</body>
</html>
