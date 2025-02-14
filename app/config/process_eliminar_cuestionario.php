<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=no_quiz_id");
    exit();
}

$quiz_id = $_GET['id'];

try {
    // Debido a la restricción ON DELETE CASCADE, se borrarán automáticamente las preguntas asociadas.
    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE quiz_id = :quiz_id");
    $stmt->execute(['quiz_id' => $quiz_id]);
    header("Location: dashboard.php?success=quiz_deleted");
    exit();
} catch (PDOException $e) {
    error_log("Error deleting quiz: " . $e->getMessage());
    header("Location: dashboard.php?error=db_error");
    exit();
}
