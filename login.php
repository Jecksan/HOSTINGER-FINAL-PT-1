<?php
session_start();
require 'conexion.php'; 

// Si ya está logueado, enviar al index
if(isset($_SESSION["usuario"])){
    header("Location: index.php");
    exit();
}

$mensaje = "";

// --- Lógica del LOGIN MANUAL (Sin cambios) ---
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario"])){
    
    $usuario  = $_POST["usuario"];
    $password = $_POST["password"];
    $turno    = $_POST["turno"]; 

    // Consulta segura para verificar credenciales
    $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $row = $result->fetch_assoc();

        // NOTA: Usando comparación simple; idealmente, usa password_verify() si las contraseñas están hasheadas.
        if($password === $row['password']){
            $_SESSION["usuario"] = $row["usuario"];
            $_SESSION["rol"]     = $row["rol"];       
            $_SESSION["turno"]   = $turno; // Usa el turno elegido manualmente
            header("Location: index.php");
            exit();
        } 
        else {
            $mensaje = "Contraseña incorrecta";
        }
    } 
    else {
        $mensaje = "Usuario incorrecto";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso al Sistema | Secure Login</title>
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
}

body {
    /* Fondo dinámico y oscuro */
    background: var(--color-background);
    font-family: 'Montserrat', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    color: var(--color-text);
    /* Fondo animado: Un degradado sutil en movimiento */
    background: linear-gradient(135deg, #121212 0%, #1a1a1a 100%);
    animation: backgroundShift 30s ease infinite alternate;
    cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><circle cx="10" cy="10" r="4" fill="%2300bcd4"/></svg>'), auto; /* Cursor moderno */
}

/* Animación de fondo */
@keyframes backgroundShift {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}

/* ------------------------------------------------------------------------- */
/* --- CAJA DE LOGIN --- */
/* ------------------------------------------------------------------------- */
.login-box {
    background: var(--color-surface);
    padding: 45px;
    width: 100%;
    max-width: 380px;
    border-radius: 15px; /* Bordes más suaves */
    /* Efecto "Neon Glow" sutil */
    box-shadow: 0 0 50px rgba(0, 188, 212, 0.2), var(--shadow-deep);
    text-align: center;
    animation: dropIn 1s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    overflow: hidden;
    position: relative; /* Para efectos internos */
    border: 1px solid rgba(0, 188, 212, 0.3); /* Borde sutil */
}

/* Animación de caída elástica al cargar */
@keyframes dropIn {
    0% {opacity:0; transform: translateY(-200px) scale(0.7);}
    60% {opacity:1; transform: translateY(10px) scale(1.05);}
    100% {transform: translateY(0) scale(1);}
}

h2 {
    font-size: 2.2rem;
    color: var(--color-primary);
    margin-bottom: 30px;
    font-weight: 800; /* Más audaz */
    text-shadow: 0 0 8px rgba(0, 188, 212, 0.7); /* Texto con brillo */
    letter-spacing: 1px;
}

/* ------------------------------------------------------------------------- */
/* --- FORMULARIO Y CAMPOS --- */
/* ------------------------------------------------------------------------- */
form {
    text-align: left;
}

label {
    display: block;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--color-text);
    margin-top: 20px;
    transition: color 0.3s ease;
}

input, select {
    width: 100%;
    padding: 14px 12px;
    margin: 8px 0 5px 0;
    border-radius: 8px;
    border: 1px solid #333;
    background: #282828; /* Fondo de campo más oscuro */
    color: var(--color-text);
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
    /* Estilo placeholder */
    color: #999; 
}

/* Efecto al enfocar (hover/focus) */
input:focus, select:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 10px rgba(0, 188, 212, 0.7);
    outline: none;
    background: #333;
}

/* BOTÓN DE INGRESO */
button {
    width: 100%;
    padding: 16px;
    background: var(--color-primary);
    color: var(--color-surface); /* Texto oscuro en botón brillante */
    font-weight: 800;
    font-size: 1.1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 40px;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 5px 15px rgba(0, 188, 212, 0.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

button:hover {
    background: #00e5ff; /* Color más claro al pasar el ratón */
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(0, 188, 212, 0.6);
}

.error {
    background: #d32f2f; /* Rojo oscuro de error */
    color: white;
    padding: 12px;
    text-align: center;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    border: 1px solid #ff5252;
    animation: flashError 0.5s;
}

/* Animación de error */
@keyframes flashError {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; background: #ef5350; }
}
</style>
</head>
<body>

<div class="login-box">

    <h2>ACCESO SEGURO</h2>

    <?php if($mensaje != ""){ ?>
    <div class="error"><?= $mensaje ?></div>
    <?php } ?>

    <form method="POST">
        
        <label for="usuario">👤 Usuario</label>
        <input type="text" name="usuario" placeholder="Ingrese su usuario" required>

        <label for="password">🔒 Contraseña</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <label for="turno">📅 Seleccionar Turno</label>
        <select name="turno" required>
            <option value="" disabled selected>Elegir Turno</option>
            <option value="matutino">Matutino</option>
            <option value="vespertino">Vespertino</option>
            <option value="dual">Dual</option>
        </select>

        <button type="submit">INGRESAR AL SISTEMA</button>
    </form>

</div>

</body>
</html>
