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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 30px; }
        .card { border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .form-control { border-radius: 8px; }
        .btn-primary { width: 100%; }
        .question { background-color: #fff; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .question h5 { font-size: 18px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center">Editar Cuestionario</h2>
        <form action="config/process_editar_cuestionario.php" method="post">
            <!-- Enviar ID del cuestionario -->
            <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz['quiz_id']); ?>">

            <div class="mb-3">
                <label class="form-label">Título:</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción:</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($quiz['description']); ?></textarea>
            </div>

            <h3 class="mt-4">Preguntas</h3>
            <?php foreach ($questions as $index => $q): ?>
                <div class="question border p-3">
                    <h5>Pregunta <?php echo $index + 1; ?></h5>
                    <input type="hidden" name="question_id[]" value="<?php echo htmlspecialchars($q['question_id']); ?>">

                    <div class="mb-2">
                        <label class="form-label">Enunciado:</label>
                        <textarea name="question_text[]" class="form-control" rows="2" required><?php echo htmlspecialchars($q['question_text']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Opción A:</label>
                            <input type="text" name="option_a[]" class="form-control" value="<?php echo htmlspecialchars($q['option_a']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Opción B:</label>
                            <input type="text" name="option_b[]" class="form-control" value="<?php echo htmlspecialchars($q['option_b']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Opción C:</label>
                            <input type="text" name="option_c[]" class="form-control" value="<?php echo htmlspecialchars($q['option_c']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Opción D:</label>
                            <input type="text" name="option_d[]" class="form-control" value="<?php echo htmlspecialchars($q['option_d']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Respuesta Correcta:</label>
                        <select name="correct_option[]" class="form-select" required>
                            <option value="A" <?php if ($q['correct_option'] == 'A') echo 'selected'; ?>>A</option>
                            <option value="B" <?php if ($q['correct_option'] == 'B') echo 'selected'; ?>>B</option>
                            <option value="C" <?php if ($q['correct_option'] == 'C') echo 'selected'; ?>>C</option>
                            <option value="D" <?php if ($q['correct_option'] == 'D') echo 'selected'; ?>>D</option>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary mt-3">Guardar Cambios</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

