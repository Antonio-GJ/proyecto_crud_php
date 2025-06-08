<?php
// create_task.php

// 1. Incluir el archivo de conexión a la base de datos
require_once 'config/database.php'; // $conn estará disponible aquí

// 2. Verificar si se recibieron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Recoger y limpiar (sanitizar) los datos del formulario
    // Usamos trim() para quitar espacios en blanco al inicio y al final
    // Usamos htmlspecialchars() para convertir caracteres especiales a entidades HTML y prevenir XSS (Cross-Site Scripting)
    $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

    // 4. Validación básica (server-side)
    if (empty($titulo)) {
        // Si el título está vacío, redirigir de vuelta con un mensaje de error (o manejar de otra forma)
        header("Location: index.php?status=error&message=El título no puede estar vacío");
        exit(); // Detener la ejecución del script
    }

    // 5. Preparar la consulta SQL para insertar los datos (Usando Sentencias Preparadas)
    // Las sentencias preparadas son CRUCIALES para prevenir Inyecciones SQL.
    $sql = "INSERT INTO tareas (titulo, descripcion) VALUES (?, ?)";

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular los parámetros: "ss" significa que ambos parámetros son strings (cadenas de texto)
        // El orden debe coincidir con los '?' en la consulta SQL
        $stmt->bind_param("ss", $titulo, $descripcion);

        // Ejecutar la sentencia
        if ($stmt->execute()) {
            // Si la inserción fue exitosa, redirigir a index.php con un mensaje de éxito
            header("Location: index.php?status=success&message=Tarea añadida correctamente");
        } else {
            // Si hubo un error en la ejecución, redirigir con un mensaje de error
            // En una aplicación real, podrías loguear $stmt->error para más detalles
            header("Location: index.php?status=error&message=Error al añadir la tarea: " . urlencode($stmt->error));
        }

        // Cerrar la sentencia
        $stmt->close();
    } else {
        // Si hubo un error al preparar la sentencia
        header("Location: index.php?status=error&message=Error al preparar la consulta: " . urlencode($conn->error));
    }

    // 6. Cerrar la conexión a la base de datos
    $conn->close();

} else {
    // Si alguien intenta acceder a create_task.php directamente sin enviar datos por POST
    echo "Acceso no permitido.";
    // O redirigir a index.php
    // header("Location: index.php");
}
exit(); // Asegurar que el script termina aquí
?>