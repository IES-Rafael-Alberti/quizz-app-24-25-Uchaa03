<?php
session_start();
require 'db_connection.php';

// Verifica que el usuario esté autenticado y sea instructor
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'instructor')) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge y limpia los datos del cuestionario
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Recoge los arreglos de preguntas
    $question_texts = isset($_POST['question_text']) ? $_POST['question_text'] : [];
    $option_a = isset($_POST['option_a']) ? $_POST['option_a'] : [];
    $option_b = isset($_POST['option_b']) ? $_POST['option_b'] : [];
    $option_c = isset($_POST['option_c']) ? $_POST['option_c'] : [];
    $option_d = isset($_POST['option_d']) ? $_POST['option_d'] : [];
    $correct_options = isset($_POST['correct_option']) ? $_POST['correct_option'] : [];

    // Validación básica
    if (empty($title) || empty($description) || count($question_texts) === 0) {
        header("Location: ../crear_cuestionario.php?error=missing_fields");
        exit();
    }

    // Usuario que crea el cuestionario
    $created_by = $_SESSION['user_id'];

    try {
        // Inicia una transacción
        $pdo->beginTransaction();

        // Inserta el cuestionario en la tabla quizzes
        $stmtQuiz = $pdo->prepare("INSERT INTO quizzes (title, description, created_by) VALUES (:title, :description, :created_by)");
        $stmtQuiz->execute([
            'title' => $title,
            'description' => $description,
            'created_by' => $created_by
        ]);
        // Obtiene el ID del cuestionario recién insertado
        $quiz_id = $pdo->lastInsertId();

        // Prepara la consulta para insertar cada pregunta
        $stmtQuestion = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (:quiz_id, :question_text, :option_a, :option_b, :option_c, :option_d, :correct_option)");

        // Recorre cada pregunta y sus opciones (se asume que todos los arrays tienen la misma cantidad de elementos)
        for ($i = 0; $i < count($question_texts); $i++) {
            $qtext = trim($question_texts[$i]);
            $a = trim($option_a[$i]);
            $b = trim($option_b[$i]);
            $c = trim($option_c[$i]);
            $d = trim($option_d[$i]);
            $correct = trim($correct_options[$i]);

            // Opcional: Validar que ninguno de estos campos esté vacío
            if (empty($qtext) || empty($a) || empty($b) || empty($c) || empty($d) || empty($correct)) {
                continue; // O bien, manejar el error según se requiera
            }

            $stmtQuestion->execute([
                'quiz_id' => $quiz_id,
                'question_text' => $qtext,
                'option_a' => $a,
                'option_b' => $b,
                'option_c' => $c,
                'option_d' => $d,
                'correct_option' => strtoupper($correct)  // Asegúrate de que la respuesta sea mayúscula
            ]);
        }

        // Confirma la transacción
        $pdo->commit();
        header("Location: ../dashboard.php?success=quiz_created");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al crear el cuestionario: " . $e->getMessage());
        header("Location: ../crear_cuestionario.php?error=db_error");
        exit();
    }
} else {
    header("Location: ../crear_cuestionario.php");
    exit();
}

