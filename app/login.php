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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center bg-info">
                    <h3>Inicia Sesión</h3>
                </div>
                <div class="card-body">
                    <!-- Mostrar mensaje de error si existe -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            if ($_GET['error'] == 'missing_fields') {
                                echo "Por favor, completa todos los campos.";
                            } elseif ($_GET['error'] == 'invalid_credentials') {
                                echo "Credenciales incorrectas, inténtalo de nuevo.";
                            } elseif ($_GET['error'] == 'db_error') {
                                echo "Hubo un error al conectar con la base de datos. Intenta más tarde.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="config/process_login.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>¿No tienes cuenta? <a href="register.php" class="text-decoration-none">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
