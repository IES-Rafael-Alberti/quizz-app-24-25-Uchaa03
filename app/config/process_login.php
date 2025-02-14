<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    // Validación básica
    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=missing_fields');
        exit();
    }

    try {
        // Consulta para obtener el usuario
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifica si el usuario existe y si la contraseña es correcta
        if ($user && password_verify($password, $user['password'])) {
            // Regeneramos la sesión para mayor seguridad
            session_regenerate_id(true);

            // Guardamos los datos en la sesión
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'estudiante'; // Valor por defecto

            header('Location: ../dashboard.php');
        } else {
            // Credenciales incorrectas
            header('Location: ../login.php?error=invalid_credentials');
        }
        exit();
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        header('Location: ../login.php?error=db_error');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
