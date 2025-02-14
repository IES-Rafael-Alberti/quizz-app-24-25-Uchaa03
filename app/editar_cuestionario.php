<?php
session_start();
require 'config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=no_quiz_id");
    exit();
}

$quiz_id = $_GET['id'];

// Recuperar datos del cuestionario
$stmtQuiz = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = :quiz_id");
$stmtQuiz->execute(['quiz_id' => $quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    header("Location: dashboard.php?error=quiz_not_found");
    exit();
}

// Recuperar las preguntas asociadas
$stmtQuestions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
$stmtQuestions->execute(['quiz_id' => $quiz_id]);
$questions = $stmtQuestions->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cuestionario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .question { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        label { font-weight: bold; }
        textarea, input[type="text"] { width: 100%; }
        .button { display: inline-block; padding: 8px 12px; background-color: #007bff; color: #fff; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<h1>Editar Cuestionario</h1>
<form action="config/process_editar_cuestionario.php" method="post">
    <!-- Enviamos el id del cuestionario -->
    <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz['quiz_id']); ?>">

    <label>Título:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required><br><br>

    <label>Descripción:</label>
    <textarea name="description" rows="3" required><?php echo htmlspecialchars($quiz['description']); ?></textarea><br><br>

    <h2>Preguntas</h2>
    <?php foreach ($questions as $index => $q): ?>
        <div class="question">
            <h3>Pregunta <?php echo $index + 1; ?></h3>
            <!-- Enviamos el ID de la pregunta para identificarla -->
            <input type="hidden" name="question_id[]" value="<?php echo htmlspecialchars($q['question_id']); ?>">

            <label>Enunciado:</label>
            <textarea name="question_text[]" rows="2" required><?php echo htmlspecialchars($q['question_text']); ?></textarea><br><br>

            <label>Opción A:</label>
            <input type="text" name="option_a[]" value="<?php echo htmlspecialchars($q['option_a']); ?>" required><br><br>

            <label>Opción B:</label>
            <input type="text" name="option_b[]" value="<?php echo htmlspecialchars($q['option_b']); ?>" required><br><br>

            <label>Opción C:</label>
            <input type="text" name="option_c[]" value="<?php echo htmlspecialchars($q['option_c']); ?>" required><br><br>

            <label>Opción D:</label>
            <input type="text" name="option_d[]" value="<?php echo htmlspecialchars($q['option_d']); ?>" required><br><br>

            <label>Respuesta Correcta:</label>
            <select name="correct_option[]" required>
                <option value="A" <?php if ($q['correct_option'] == 'A') echo 'selected'; ?>>A</option>
                <option value="B" <?php if ($q['correct_option'] == 'B') echo 'selected'; ?>>B</option>
                <option value="C" <?php if ($q['correct_option'] == 'C') echo 'selected'; ?>>C</option>
                <option value="D" <?php if ($q['correct_option'] == 'D') echo 'selected'; ?>>D</option>
            </select>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="button">Guardar Cambios</button>
</form>
</body>
</html>
