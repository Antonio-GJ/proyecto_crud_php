<?php
// edit_form.php

// Incluir la conexión a la base de datos
require_once 'config/database.php';

$task = null;
$error_message = '';

// Verificar si se ha proporcionado un ID en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_tarea = (int)$_GET['id'];

    // Preparar la consulta para obtener los datos de la tarea
    $sql = "SELECT id, titulo, descripcion, estado FROM tareas WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_tarea);
        $stmt->execute();
        $result = $stmt->get_result(); // Obtener el conjunto de resultados

        if ($result->num_rows === 1) {
            // Si se encontró la tarea, obtener sus datos
            $task = $result->fetch_assoc();
        } else {
            $error_message = "No se encontró ninguna tarea con ese ID.";
        }
        $stmt->close();
    } else {
        $error_message = "Error al preparar la consulta.";
    }
} else {
    $error_message = "No se proporcionó un ID de tarea.";
}

$conn->close();

// Si hubo un error grave (ej. no se encontró la tarea), podríamos detener y mostrar el mensaje
if ($task === null) {
    die($error_message); // Detiene la ejecución y muestra el error. Puedes hacer una página de error más elegante.
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea, select { width: calc(100% - 18px); padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input[type="submit"] { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #0056b3; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Tarea</h1>
        <form action="update_task.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">

            <div>
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($task['titulo']); ?>" required>
            </div>
            <div>
                <label for="descripcion">Descripción (opcional):</label>
                <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($task['descripcion']); ?></textarea>
            </div>
            <div>
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="pendiente" <?php echo ($task['estado'] === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="en_progreso" <?php echo ($task['estado'] === 'en_progreso') ? 'selected' : ''; ?>>En Progreso</option>
                    <option value="completada" <?php echo ($task['estado'] === 'completada') ? 'selected' : ''; ?>>Completada</option>
                </select>
            </div>
            <div>
                <input type="submit" value="Guardar Cambios">
            </div>
        </form>
        <br>
        <a href="index.php">Volver a la lista</a>
    </div>
</body>
</html>