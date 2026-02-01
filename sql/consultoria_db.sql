-- =====================================================
-- BASE DE DATOS PARA CONSULTORÍA WEB
-- Ejecutar en phpMyAdmin o MySQL
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS consultoria_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE consultoria_db;

-- =====================================================
-- TABLA: administradores
-- =====================================================
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA: contactos (clientes que envían mensajes)
-- =====================================================
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    empresa VARCHAR(100),
    servicio_interes VARCHAR(100),
    mensaje TEXT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido TINYINT(1) DEFAULT 0,
    respondido TINYINT(1) DEFAULT 0
);

-- =====================================================
-- INSERTAR ADMINISTRADOR POR DEFECTO
-- Usuario: admin | Contraseña: admin123
-- =====================================================
INSERT INTO administradores (usuario, password, nombre_completo, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@consultoria.com');

-- La contraseña encriptada es: admin123
-- Para cambiarla, usa password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT) en PHP
