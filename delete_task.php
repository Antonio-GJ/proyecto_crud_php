<?php
// delete_task.php

// 1. Incluir el archivo de conexión a la base de datos
require_once 'config/database.php'; // $conn estará disponible aquí

$message = '';
$message_type = 'error'; // Por defecto, asumimos error hasta confirmar éxito

// 2. Verificar si se recibió un ID por GET y no está vacío
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Convertir el ID a un entero para seguridad
    $id_tarea = (int)$_GET['id'];

    // 3. Preparar la consulta SQL para eliminar la tarea (Usando Sentencias Preparadas)
    $sql = "DELETE FROM tareas WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular el parámetro ID: "i" significa que el parámetro es un entero (integer)
        $stmt->bind_param("i", $id_tarea);

        // Ejecutar la sentencia
        if ($stmt->execute()) {
            // Verificar si alguna fila fue afectada (es decir, si la tarea existía y se borró)
            if ($stmt->affected_rows > 0) {
                $message = "Tarea eliminada correctamente.";
                $message_type = "success";
            } else {
                $message = "No se encontró la tarea o ya había sido eliminada.";
                // $message_type sigue siendo 'error' o puedes ser más específico
            }
        } else {
            // Error al ejecutar la sentencia
            $message = "Error al intentar eliminar la tarea: " . urlencode($stmt->error);
        }
        // Cerrar la sentencia
        $stmt->close();
    } else {
        // Error al preparar la sentencia
        $message = "Error al preparar la consulta de eliminación: " . urlencode($conn->error);
    }
} else {
    // No se proporcionó un ID o está vacío
    $message = "ID de tarea no proporcionado o inválido.";
}

// 4. Cerrar la conexión a la base de datos
$conn->close();

// 5. Redirigir de vuelta a index.php con un mensaje de estado
// Usamos urlencode para el mensaje por si contiene caracteres especiales
header("Location: index.php?status=" . $message_type . "&message=" . urlencode($message));
exit(); // Detener la ejecución del script
?>