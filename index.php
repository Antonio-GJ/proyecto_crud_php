<?php
// index.php

// 1. Incluir el archivo de conexión a la base de datos
// Lo necesitamos tanto para mostrar mensajes como para leer las tareas
require_once 'config/database.php'; // $conn estará disponible aquí

$message = '';
$message_type = ''; // 'success' o 'error'

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message_type = 'success';
        $message = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : '¡Operación exitosa!';
    } elseif ($_GET['status'] == 'error') {
        $message_type = 'error';
        $message = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Algo salió mal.';
    }
}

// 2. Preparar la consulta SQL para LEER todas las tareas
// Ordenamos por fecha_creacion en orden descendente para ver las más nuevas primero
$sql_select_tasks = "SELECT id, titulo, descripcion, estado, fecha_creacion FROM tareas ORDER BY fecha_creacion DESC";
$result_tasks = $conn->query($sql_select_tasks);

// No cerramos $conn aquí todavía porque la necesitaremos si hay errores en la parte HTML o más adelante
// $conn->close(); // Lo haremos al final si ya no se necesita. De hecho, PHP lo cierra automáticamente al final del script.

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lista de Tareas CRUD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 700px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: calc(100% - 18px); padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input[type="submit"] { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #218838; }
        hr { margin-top: 20px; margin-bottom: 20px; border: 0; border-top: 1px solid #eee; }
        
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; color: white; }
        .message.success { background-color: #4CAF50; }
        .message.error { background-color: #f44336; }

        .task-list { list-style-type: none; padding: 0; }
        .task-item { background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 4px; }
        .task-item h3 { margin-top: 0; margin-bottom: 5px; }
        .task-item p { margin-bottom: 10px; }
        .task-item .meta { font-size: 0.9em; color: #777; }
        .task-item .actions a { text-decoration: none; padding: 5px 10px; margin-right: 5px; border-radius: 3px; color: white; }
        .task-item .actions .edit-btn { background-color: #007bff; }
        .task-item .actions .edit-btn:hover { background-color: #0056b3; }
        .task-item .actions .delete-btn { background-color: #dc3545; }
        .task-item .actions .delete-btn:hover { background-color: #c82333; }
        .no-tasks { color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Añadir Nueva Tarea</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="create_task.php" method="POST">
            <div>
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div>
                <label for="descripcion">Descripción (opcional):</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            <div>
                <input type="submit" value="Añadir Tarea">
            </div>
        </form>

        <hr>
        <h2>Lista de Tareas</h2>
        <div id="lista-tareas-container">
            <?php if ($result_tasks && $result_tasks->num_rows > 0): ?>
                <ul class="task-list">
                    <?php
                    // 3. Iterar sobre los resultados y mostrar cada tarea
                    // fetch_assoc() recupera una fila de resultados como un array asociativo
                    while ($task = $result_tasks->fetch_assoc()):
                    ?>
                        <li class="task-item">
                            <h3><?php echo htmlspecialchars($task['titulo']); ?></h3>
                            <?php if (!empty($task['descripcion'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($task['descripcion'])); ?></p>
                            <?php endif; ?>
                            <p class="meta">
                                Estado: <strong><?php echo htmlspecialchars($task['estado']); ?></strong><br>
                                Creada: <?php echo date("d/m/Y H:i:s", strtotime($task['fecha_creacion'])); ?>
                            </p>
                            <div class="actions">
                                <a href="edit_form.php?id=<?php echo $task['id']; ?>" class="edit-btn">Editar</a>
                                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás realmente seguro de que quieres eliminar esta tarea?');">Eliminar</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="no-tasks">No hay tareas pendientes. ¡Añade alguna!</p>
            <?php endif; ?>
            <?php
            // Liberar el conjunto de resultados si ya no se necesita
            if ($result_tasks) {
                $result_tasks->free();
            }
            // Cerrar la conexión (PHP la cierra automáticamente al final del script si no se hace explícitamente)
            // $conn->close();
            ?>
        </div>
    </div>
</body>
</html>