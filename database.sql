-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS proyectos_db;
USE proyectos_db;

-- Crear la tabla de proyectos
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255),
    fecha_creacion DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 