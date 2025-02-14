<?php

require './config/db_connection.php';
//Para comprobar si me conectaba a la base de datos
try {
    $stmt = $pdo->query("SELECT 'Conexión exitosa' AS mensaje");
    $row = $stmt->fetch();
    echo $row['mensaje'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

