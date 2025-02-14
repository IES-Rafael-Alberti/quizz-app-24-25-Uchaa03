<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require 'config/db_connection.php';
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo ($role === 'instructor' ? 'Profesor' : 'Estudiante'); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        header { background-color: #343a40; color: #fff; padding: 15px; }
        header h1 { margin: 0; font-size: 24px; }
    </style>
</head>
<body>
<header class="d-flex justify-content-between align-items-center">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="config/process_logout.php" class="btn btn-danger">Cerrar Sesión</a>
</header>
<div class="container my-4">
    <?php if ($role === 'instructor'): ?>
        <div class="mb-4">
            <h2>Dashboard de Profesor</h2>
            <p>Aquí puedes administrar los cuestionarios:</p>
            <a href="crear_cuestionario.php" class="btn btn-primary">Agregar Cuestionario</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered rounded-3 shadow-sm">
                <thead class="bg-light text-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM quizzes");
                while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="editar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>" class="btn btn-success btn-sm px-3">✏️ Editar</a>
                            <a href="config/process_eliminar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>" class="btn btn-danger btn-sm px-3" onclick="return confirm('¿Eliminar este cuestionario?');">🗑️ Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div>
            <h2>Dashboard de Estudiante</h2>
            <p>Aquí puedes ver y realizar los cuestionarios disponibles:</p>
            <div class="table-responsive">
                <table class="table table-hover table-bordered rounded-3 shadow-sm">
                    <thead class="bg-light text-dark">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM quizzes");
                    while ($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="realizar_cuestionario.php?id=<?php echo $row['quiz_id']; ?>" class="btn btn-primary btn-sm px-3">📝 Realizar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
