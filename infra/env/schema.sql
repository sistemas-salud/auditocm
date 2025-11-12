CREATE TABLE usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Insert ejemplo (contraseña: "secret123")
INSERT INTO usuarios (username, password_hash) VALUES (
'admin',
-- Generar con PHP: password_hash('secret123', PASSWORD_DEFAULT)
'$2y$10$EXAMPLEHASHSHOULDBEGENERATED',
NOW()
);