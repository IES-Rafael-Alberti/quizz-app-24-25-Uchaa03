<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../dashboard.php");
    exit();
}

$quiz_id = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
$submittedAnswers = isset($_POST['answers']) ? $_POST['answers'] : [];

if (!$quiz_id || empty($submittedAnswers)) {
    header("Location: ../dashboard.php?error=missing_data");
    exit();
}

// Recupera todas las preguntas del cuestionario
$stmt = $pdo->prepare("SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE quiz_id = :quiz_id");
$stmt->execute(['quiz_id' => $quiz_id]);
$questions = $stmt->fetchAll();

$totalQuestions = count($questions);
$correctCount = 0;
$results = [];

foreach ($questions as $question) {
    $qid = $question['question_id'];
    $correctOption = strtoupper($question['correct_option']);
    $studentAnswer = isset($submittedAnswers[$qid]) ? strtoupper($submittedAnswers[$qid]) : '';
    $isCorrect = ($studentAnswer === $correctOption);
    if ($isCorrect) {
        $correctCount++;
    }
    $results[] = [
        'question_text'  => $question['question_text'],
        'correct_option' => $correctOption,
        'student_answer' => $studentAnswer,
        'is_correct'     => $isCorrect,
        'option_a'       => $question['option_a'],
        'option_b'       => $question['option_b'],
        'option_c'       => $question['option_c'],
        'option_d'       => $question['option_d'],
    ];
}

// Calcula la puntuación del intento actual (en porcentaje)
$currentScore = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

// Actualiza o inserta el número de intentos en la tabla quiz_attempts
$user_id = $_SESSION['user_id'];

try {
    // Busca si ya existe un registro para este usuario y quiz
    $stmtCheck = $pdo->prepare("SELECT attempt_id, attempts FROM quiz_attempts WHERE quiz_id = :quiz_id AND user_id = :user_id");
    $stmtCheck->execute(['quiz_id' => $quiz_id, 'user_id' => $user_id]);
    $attemptData = $stmtCheck->fetch();

    if ($attemptData) {
        // Actualiza incrementando el contador y actualiza la fecha
        $stmtUpdate = $pdo->prepare("UPDATE quiz_attempts SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP WHERE attempt_id = :attempt_id");
        $stmtUpdate->execute(['attempt_id' => $attemptData['attempt_id']]);
        $attempts = $attemptData['attempts'] + 1;
    } else {
        // Inserta un nuevo registro con 1 intento
        $stmtInsert = $pdo->prepare("INSERT INTO quiz_attempts (quiz_id, user_id, attempts) VALUES (:quiz_id, :user_id, 1)");
        $stmtInsert->execute(['quiz_id' => $quiz_id, 'user_id' => $user_id]);
        $attempts = 1;
    }
} catch (PDOException $e) {
    error_log("Error recording quiz attempt: " . $e->getMessage());
    // Continuamos sin detener la ejecución en caso de error en el registro
    $attempts = "N/D";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen del Cuestionario</title>
    <!-- Agregar enlace a Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Resumen del Cuestionario</h1>

    <div class="alert alert-info">
        <p>Total de preguntas: <?php echo $totalQuestions; ?></p>
        <p>Respuestas correctas: <?php echo $correctCount; ?></p>
        <p>Puntuación en este intento: <?php echo $currentScore; ?>%</p>
        <p>Número de intentos realizados: <?php echo $attempts; ?></p>
    </div>

    <h2>Detalle de Resultados</h2>
    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>Pregunta</th>
            <th>Tu respuesta</th>
            <th>Respuesta correcta</th>
            <th>Resultado</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $res): ?>
            <tr>
                <td><?php echo htmlspecialchars($res['question_text']); ?></td>
                <td><?php echo htmlspecialchars($res['student_answer']); ?></td>
                <td><?php echo htmlspecialchars($res['correct_option']); ?></td>
                <td>
                    <?php if ($res['is_correct']): ?>
                        <span class="badge badge-success">Correcto</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Incorrecto</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="../dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
    </div>
</div>

<!-- Agregar scripts de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
