-- Este script se ejecutará automáticamente la primera vez que inicie el contenedor 'db'.

-- 1. Crear la tabla 'usuarios' que falta
CREATE TABLE IF NOT EXISTS usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
nombre VARCHAR(100)
-- Agrega todas las demás columnas que tu aplicación necesite
);

-- 2. Insertar un usuario de prueba (opcional, pero útil)
-- NOTA: La contraseña debe estar en hash si tu auth.php lo espera.
-- Si solo espera texto plano, usa '12345' aquí. Asumiré texto plano por simplicidad de la prueba.
INSERT INTO usuarios (username, password_hash, nombre) VALUES
('admin', '12345', 'Administrador Inicial');