<?php
session_start();

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require_once "../controller/config.php";

    // Obtener los datos del formulario
    $titulo = '';
    $texto = strip_tags($_POST['description']);
    $publictype = 'p';
    if (isset($_SESSION['post_image'])) {
        $imagen_ruta = $_SESSION['post_image'];
        $_SESSION['post_image'] = null;
    }

    // Insertar el post en la base de datos
    $user_id = $_SESSION["id"];
    $sql = "INSERT INTO posts (title, content, imagen, publictype, authorid) VALUES (?, ?, ?, ?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("sssss", $titulo, $texto, $imagen_ruta, $publictype, $user_id);

    if ($stmt->execute()) {
        // Redireccionar al usuario a una página de éxito o mostrar un mensaje de éxito
        header("Location: ../index.php");
        exit();
    } else {
        // Mostrar un mensaje de error si la inserción falla
        echo "Error al subir el post: " . $link->error;
    }

    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $link->close();
} else {
    // Si se intenta acceder al script directamente sin enviar el formulario, redirigir al formulario
    header("Location: ../view/formulario_post.html");
    exit();
}
?>
