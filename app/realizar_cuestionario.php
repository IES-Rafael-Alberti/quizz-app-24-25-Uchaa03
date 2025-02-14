<?php
session_start();
require 'config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=missing_quiz_id");
    exit();
}

$quiz_id = $_GET['id'];

// Recupera los datos del cuestionario
$stmtQuiz = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = :quiz_id");
$stmtQuiz->execute(['quiz_id' => $quiz_id]);
$quiz = $stmtQuiz->fetch();

if (!$quiz) {
    header("Location: dashboard.php?error=quiz_not_found");
    exit();
}

// Recupera las preguntas asociadas
$stmtQuestions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
$stmtQuestions->execute(['quiz_id' => $quiz_id]);
$questions = $stmtQuestions->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($quiz['title']); ?> - Realizar Cuestionario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin: auto; padding: 20px; }
        .question { margin-bottom: 20px; padding: 15px; border-radius: 5px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-3"> <?php echo htmlspecialchars($quiz['title']); ?> </h1>
    <p class="text-muted"> <?php echo htmlspecialchars($quiz['description']); ?> </p>

    <form action="config/process_realizar_cuestionario.php" method="post">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <?php foreach ($questions as $question): ?>
            <div class="question p-3 border rounded">
                <p><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                <?php foreach (["A" => "option_a", "B" => "option_b", "C" => "option_c", "D" => "option_d"] as $key => $option): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $key; ?>" required>
                        <label class="form-check-label">
                            <?php echo htmlspecialchars($question[$option]); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary mt-3">Enviar Respuestas</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
