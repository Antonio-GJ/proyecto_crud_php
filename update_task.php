<?php
// update_task.php

require_once 'config/database.php';

$message = '';
$message_type = 'error';

// Verificar que la solicitud sea por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger y limpiar los datos del formulario
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

    // Validación básica
    if ($id > 0 && !empty($titulo) && !empty($estado)) {

        // Preparar la consulta SQL de ACTUALIZACIÓN (UPDATE)
        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, estado = ? WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Vincular los parámetros: "sssi" -> string, string, string, integer
            $stmt->bind_param("sssi", $titulo, $descripcion, $estado, $id);

            if ($stmt->execute()) {
                // Si se ejecutó correctamente
                $message = "Tarea actualizada correctamente.";
                $message_type = "success";
            } else {
                $message = "Error al actualizar la tarea: " . urlencode($stmt->error);
            }
            $stmt->close();
        } else {
            $message = "Error al preparar la consulta de actualización: " . urlencode($conn->error);
        }
    } else {
        $message = "Datos inválidos. El título y el estado son obligatorios.";
    }
} else {
    $message = "Acceso no permitido. Se requiere método POST.";
}

$conn->close();

// Redirigir de vuelta a index.php con el mensaje
header("Location: index.php?status=" . $message_type . "&message=" . urlencode($message));
exit();
?>