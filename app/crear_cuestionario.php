<?php
session_start();

// Verifica que el usuario esté autenticado y sea profesor
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'instructor')) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuestionario</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .question-container { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        label { font-weight: bold; }
        textarea, input[type="text"] { width: 100%; }
        .button { display: inline-block; padding: 8px 12px; background-color: #007bff; color: #fff; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; }
        .button:hover { background-color: #0056b3; }
    </style>
    <script>
        // Función para agregar dinámicamente un bloque de pregunta
        function agregarPregunta() {
            let container = document.getElementById("questions-container");
            let index = container.children.length + 1;
            let html = `
        <div class="question-container">
            <h3>Pregunta ${index}</h3>
            <label>Enunciado:</label>
            <textarea name="question_text[]" rows="2" required></textarea><br><br>
            <label>Opción A:</label>
            <input type="text" name="option_a[]" required><br><br>
            <label>Opción B:</label>
            <input type="text" name="option_b[]" required><br><br>
            <label>Opción C:</label>
            <input type="text" name="option_c[]" required><br><br>
            <label>Opción D:</label>
            <input type="text" name="option_d[]" required><br><br>
            <label>Respuesta Correcta:</label>
            <select name="correct_option[]" required>
                <option value="">Selecciona...</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</head>
<body>
<h1>Crear Nuevo Cuestionario</h1>
<form action="config/process_crear_cuestionario.php" method="post">
    <label for="title">Título del Cuestionario:<br>
    <input type="text" name="title" required>
    </label>
    <br><br>
    <label>Descripción:<br>
    <textarea name="description" rows="3" required></textarea>
    </label>
    <br><br>
    <h2>Preguntas</h2>
    <div id="questions-container">
        <!-- Aquí se agregarán dinámicamente las preguntas -->
    </div>
    <button type="button" class="button" onclick="agregarPregunta()">Agregar Pregunta</button>
    <br><br>
    <button type="submit" class="button">Guardar Cuestionario</button>
</form>
</body>
</html>

