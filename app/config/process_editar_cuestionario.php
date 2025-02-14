<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Arreglos de las preguntas
    $question_ids = isset($_POST['question_id']) ? $_POST['question_id'] : [];
    $question_texts = isset($_POST['question_text']) ? $_POST['question_text'] : [];
    $option_a = isset($_POST['option_a']) ? $_POST['option_a'] : [];
    $option_b = isset($_POST['option_b']) ? $_POST['option_b'] : [];
    $option_c = isset($_POST['option_c']) ? $_POST['option_c'] : [];
    $option_d = isset($_POST['option_d']) ? $_POST['option_d'] : [];
    $correct_options = isset($_POST['correct_option']) ? $_POST['correct_option'] : [];

    if (empty($quiz_id) || empty($title) || empty($description)) {
        header("Location: ../editar_cuestionario.php?id=$quiz_id&error=missing_fields");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Actualizar el cuestionario
        $stmtQuiz = $pdo->prepare("UPDATE quizzes SET title = :title, description = :description WHERE quiz_id = :quiz_id");
        $stmtQuiz->execute([
            'title' => $title,
            'description' => $description,
            'quiz_id' => $quiz_id
        ]);

        // Actualizar cada pregunta
        $stmtQuestion = $pdo->prepare("UPDATE questions SET question_text = :question_text, option_a = :option_a, option_b = :option_b, option_c = :option_c, option_d = :option_d, correct_option = :correct_option WHERE question_id = :question_id");

        for ($i = 0; $i < count($question_ids); $i++) {
            $qid = $question_ids[$i];
            $qtext = trim($question_texts[$i]);
            $a = trim($option_a[$i]);
            $b = trim($option_b[$i]);
            $c = trim($option_c[$i]);
            $d = trim($option_d[$i]);
            $correct = strtoupper(trim($correct_options[$i]));

            $stmtQuestion->execute([
                'question_text' => $qtext,
                'option_a' => $a,
                'option_b' => $b,
                'option_c' => $c,
                'option_d' => $d,
                'correct_option' => $correct,
                'question_id' => $qid
            ]);
        }

        $pdo->commit();
        header("Location: ../dashboard.php?success=quiz_updated");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error updating quiz: " . $e->getMessage());
        header("Location: ../editar_cuestionario.php?id=$quiz_id&error=db_error");
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}

