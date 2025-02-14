<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos enviados del formulario
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['confirm_password']);  // Confirmación de contraseña

    // Validación de los campos
    if (empty($username) || empty($password) || empty($passwordConfirm)) {
        header('Location: register.php?error=missing_fields');
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($password !== $passwordConfirm) {
        header('Location: register.php?error=password_mismatch');
        exit();
    }

    // Verificar que la contraseña tenga al menos 6 caracteres
    if (strlen($password) < 6) {
        header('Location: register.php?error=password_too_short');
        exit();
    }

    // Verificar si el nombre de usuario ya existe
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() > 0) {
        header('Location: register.php?error=user_exists');
        exit();
    }

    // Encriptar la contraseña antes de guardarla
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO Usuarios (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

        // Redirigir al login después del registro exitoso
        header('Location: login.php?success=registered');
        exit();
    } catch (PDOException $e) {
        // Error al insertar en la base de datos
        header('Location: register.php?error=registration_failed');
        exit();
    }
}


