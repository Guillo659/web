<?php
require_once "../controller/config.php";

// Realizar consulta para obtener todos los posts
$sql = "SELECT title, content, imagen FROM posts";
$result = $link->query($sql);

// Verificar si se encontraron resultados
if ($result->num_rows > 0) {
    // Mostrar los posts encontrados
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . $row["title"] . "</h2>";
        echo "<p>" . $row["content"] . "</p>";
        echo "<img src='" . $row["imagen"] . "' alt='Imagen del post'>";
        echo "</div>";
    }
} else {
    echo "No se encontraron posts.";
}

// Liberar memoria y cerrar la conexión
$result->free();
$link->close();
?>

<p><a href="../model/logout.php">Cerrar Sesion</a></p>