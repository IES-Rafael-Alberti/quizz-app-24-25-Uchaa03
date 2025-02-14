<?php
session_start();

// Si el usuario ya está logueado, redirige al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Cuestionarios</title>
</head>
<body>
<h1>Inicia Sesión</h1>

<!-- Mostrar mensaje de error si existe -->
<?php if (isset($_GET['error'])): ?>
    <p style="color:red;">
        <?php
        if ($_GET['error'] == 'missing_fields') {
            echo "Por favor, completa todos los campos.";
        } elseif ($_GET['error'] == 'invalid_credentials') {
            echo "Credenciales incorrectas, inténtalo de nuevo.";
        } elseif ($_GET['error'] == 'db_error') {
            echo "Hubo un error al conectar con la base de datos. Intenta más tarde.";
        }
        ?>
    </p>
<?php endif; ?>

<form action="config/process_login.php" method="post">
    <label for="username">Usuario:</label>
    <input type="text" name="username" id="username" required><br><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required><br><br>

    <button type="submit">Entrar</button>
</form>

<!-- Enlace al registro -->
<p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>

</body>
</html>
