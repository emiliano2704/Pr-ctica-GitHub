<?php
$host = 'localhost';
$db   = 'crud_db';
$user = 'root';
$pass = ''; // Por defecto en XAMPP la contraseña está vacía
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // La primera conexión no selecciona una base para poder crearla si es necesario.
     $servidor = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
     $servidor->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci");

     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
     $pdo->exec("CREATE TABLE IF NOT EXISTS productos_tecnologicos (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          nombre VARCHAR(120) NOT NULL,
          categoria VARCHAR(80) NOT NULL,
          marca VARCHAR(80) NOT NULL,
          precio DECIMAL(10,2) NOT NULL,
          stock INT UNSIGNED NOT NULL DEFAULT 0,
          creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
