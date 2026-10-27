CREATE DATABASE IF NOT EXISTS sysos CHARACTER SET utf8mb4;
USE sysos;
CREATE TABLE IF NOT EXISTS conceptos(
 id INT AUTO_INCREMENT PRIMARY KEY,
 modulo ENUM('procesos','memoria','archivos','virtualizacion') NOT NULL,
 titulo VARCHAR(80) NOT NULL,
 descripcion TEXT NOT NULL,
 fav TINYINT(1) NOT NULL DEFAULT 0,
 creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO conceptos(modulo,titulo,descripcion,fav) VALUES
('procesos','Condición de carrera','Resultado que depende del orden en que los hilos acceden a un dato compartido. Se evita con mutex o semáforos.',1),
('memoria','Page Fault','La página pedida no está en RAM: el SO la trae del disco y reinicia la instrucción.',0),
('archivos','Inodo','Estructura que guarda metadatos de un archivo (dueño, permisos, bloques) pero no su nombre.',0),
('virtualizacion','CGroups','Mecanismo del kernel que limita CPU, RAM y E/S de un grupo de procesos.',0);
CREATE USER IF NOT EXISTS 'sysos'@'localhost' IDENTIFIED BY 'sysos123';
GRANT ALL ON sysos.* TO 'sysos'@'localhost';
FLUSH PRIVILEGES;
