<?php
// Fichero de conexión a la base de datos
$host    = 'db';        // Nombre del servicio en Docker
$db      = 'quiz_app';  // Nombre de la base de datos (según tu docker-compose)
$user    = 'root';      // Usuario definido en el docker-compose
$pass    = 'root';  // Contraseña definida en el docker-compose
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Mejora la seguridad contra inyecciones SQL
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}