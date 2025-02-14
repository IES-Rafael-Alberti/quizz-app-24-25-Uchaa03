<?php
session_start();

// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config/db_connection.php'; // Asegúrate de que la ruta sea correcta

// Obtenemos el rol del usuario desde la sesión
$role = $_SESSION['role'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo ($role === 'instructor ' ? 'Profesor' : 'Estudiante'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        header { background-color: #343a40; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; font-size: 20px; }
        header a { color: white; text-decoration: none; padding: 8px 12px; background-color: #dc3545; border-radius: 5px; }
        header a:hover { background-color: #c82333; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        .button { display: inline-block; padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; }
        .edit { background-color: #28a745; }
        .delete { background-color: #dc3545; }
        .start { background-color: #007bff; }
        .button:hover { opacity: 0.8; }
    </style>
</head>
<body>

<header>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="./config/process_logout.php">Cerrar Sesión</a>
</header>

<div class="container">
    <?php if ($role === 'instructor'): ?>
        <h2>Dashboard de Profesor</h2>
        <p>Aquí puedes administrar los cuestionarios:</p>
        <p><a class="button start" href="crear_cuestionario.php">➕ Agregar Cuestionario</a></p>

        <!-- Listado de cuestionarios con opciones de edición y eliminación -->
        <table>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
            <?php
            // Obtener todos los cuestionarios
            $stmt = $pdo->query("SELECT * FROM quizzes");
            while ($row = $stmt->fetch()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a class="button edit" href="editar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>">✏ Editar</a>
                        <a class="button delete" href="eliminar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este cuestionario?');">🗑 Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
        <h2>Dashboard de Estudiante</h2>
        <p>Aquí puedes ver y realizar los cuestionarios disponibles:</p>

        <!-- Listado de cuestionarios sin opciones de edición -->
        <table>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Acción</th>
            </tr>
            <?php
            // Obtener todos los cuestionarios
            $stmt = $pdo->query("SELECT * FROM quizzes");
            while ($row = $stmt->fetch()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a class="button start" href="realizar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>">📝 Realizar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
