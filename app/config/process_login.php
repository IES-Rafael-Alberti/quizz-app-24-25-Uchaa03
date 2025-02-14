<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validación básica
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=missing_fields');
        exit();
    }

    try {
        // Consulta para obtener el usuario
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifica si el usuario existe y si la contraseña es correcta
        if ($user && password_verify($password, $user['password'])) {
            // Inicio de sesión exitoso
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Este campo servirá para futuras comprobaciones de permisos

            header('Location: dashboard.php');
        } else {
            // Credenciales incorrectas
            header('Location: login.php?error=invalid_credentials');
        }
    } catch (PDOException $e) {
        // En caso de error con la base de datos, redirigir con un error
        header('Location: login.php?error=db_error');
    }
} else {
    // Si no es una petición POST, redirige al login
    header('Location: login.php');
}
exit();
