<?php
// config/database.example.php

$db_host = 'localhost';
$db_name = 'nombre_de_tu_bd'; // Placeholder
$db_user = 'tu_usuario_bd';     // Placeholder
$db_pass = 'tu_contraseña_bd';  // Placeholder

// Intentar conectar a la base de datos
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar la conexión
if ($conn->connect_error) {
    // Si hay un error, muestra el error y termina la ejecución
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el juego de caracteres a UTF-8 (recomendado)
if (!$conn->set_charset("utf8mb4")) {
    // printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", $conn->error);
    // Considera manejar este error de forma más robusta en una aplicación real
}

// Opcional: Podrías imprimir un mensaje si la conexión es exitosa (solo para depuración inicial)
// echo "Conexión exitosa a la base de datos.";

// No cierres la conexión aquí si vas a incluir este archivo en otros scripts que la necesiten.
// $conn->close();
?>