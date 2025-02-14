<?php
session_start();
$error_msg = ''; // Para guardar los mensajes de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos enviados del formulario
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['confirm_password']);  // Confirmación de contraseña

    // Validación de los campos
    if (empty($username) || empty($password) || empty($passwordConfirm)) {
        $error_msg = "Por favor, completa todos los campos.";
    } elseif ($password !== $passwordConfirm) {
        $error_msg = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error_msg = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Verificar si el nombre de usuario ya existe
        require 'config/db_connection.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->rowCount() > 0) {
            $error_msg = "El usuario ya existe.";
        } else {
            // Encriptar la contraseña antes de guardarla
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

                // Redirigir al login después del registro exitoso
                header('Location: login.php?success=registered');
                exit();
            } catch (PDOException $e) {
                $error_msg = $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
</head>
<body>
<h1>Registro</h1>

<!-- Mostrar mensajes de error si existen -->
<?php if (!empty($error_msg)): ?>
    <p style="color:red;"><?php echo $error_msg; ?></p>
<?php endif; ?>

<form action="register.php" method="post">
    <label for="username">Usuario:</label>
    <input type="text" name="username" id="username" value="<?php echo isset($username) ? $username : ''; ?>" required><br><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>

    <label for="confirm_password">Confirmar Contraseña:</label>
    <input type="password" name="confirm_password" id="confirm_password" required><br><br>

    <button type="submit">Registrarse</button>
</form>

<!-- Enlace al login -->
<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</body>
</html>
