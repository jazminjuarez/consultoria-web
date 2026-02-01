<?php
// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Cambiar según tu configuración
define('DB_PASS', '');               // Cambiar según tu configuración
define('DB_NAME', 'consultoria_db');

// Conexión a la base de datos
function conectarDB() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    $conexion->set_charset("utf8mb4");
    return $conexion;
}

// Configuración de correo (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'tucorreo@gmail.com');    // Cambiar por tu correo
define('SMTP_PASS', 'tu_contraseña_app');     // Usar contraseña de aplicación de Google
define('SMTP_PORT', 587);
define('MAIL_FROM', 'tucorreo@gmail.com');
define('MAIL_NAME', 'Consultoría Web');

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
