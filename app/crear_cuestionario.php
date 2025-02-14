<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuestionario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function agregarPregunta() {
            let container = document.getElementById("questions-container");
            let index = container.children.length + 1;
            let html = `
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Pregunta ${index}</h5>
          <div class="mb-3">
            <label class="form-label">Enunciado:</label>
            <textarea name="question_text[]" class="form-control" rows="2" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Opción A:</label>
            <input type="text" name="option_a[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Opción B:</label>
            <input type="text" name="option_b[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Opción C:</label>
            <input type="text" name="option_c[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Opción D:</label>
            <input type="text" name="option_d[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Respuesta Correcta:</label>
            <select name="correct_option[]" class="form-select" required>
              <option value="">Selecciona...</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
        </div>
      </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</head>
<body>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3>Crear Nuevo Cuestionario</h3>
                </div>
                <div class="card-body">
                    <form action="config/process_crear_cuestionario.php" method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del Cuestionario:</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción:</label>
                            <textarea name="description" id="description" rows="3" class="form-control" required></textarea>
                        </div>
                        <h4>Preguntas</h4>
                        <div id="questions-container"></div>
                        <button type="button" class="btn btn-secondary mb-3" onclick="agregarPregunta()">Agregar Pregunta</button>
                        <button type="submit" class="btn btn-success w-100">Guardar Cuestionario</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="dashboard.php" class="btn btn-link">Volver al Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


