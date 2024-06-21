<?php
session_start();

// Recibir los datos enviados desde la solicitud AJAX
$titulo = strip_tags($_POST['titulo']);
$contenido = strip_tags($_POST['contenido']);
$image = $_POST['image'];
$publictype = 's';
$user_id = $_SESSION["id"];
$all = $_POST['all']; // Ya esta sanitizado (a priori) <- proxima auditoria importante auditar article.php
$materia = $_POST['materia'];

// Conectar a la base de datos (reemplaza con tus credenciales)
require_once "../controller/config.php";

$path = $image;

// Preparar la consulta SQL para insertar los datos en la tabla posts
$sql_insertar_post = "INSERT INTO posts (title, content, imagen, materia, publictype, authorid) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_insertar_post = $link->prepare($sql_insertar_post);
$stmt_insertar_post->bind_param("ssssss", $titulo, $contenido, $path, $materia, $publictype, $user_id);

// Ejecutar la consulta para insertar el post
if ($stmt_insertar_post->execute()) {
    // Obtener el ID del post insertado
    $post_id = $stmt_insertar_post->insert_id;

    // Preparar la consulta SQL para insertar los datos en la tabla contentPost
    $sql_insertar_content = "INSERT INTO contentPost (post_id, title, content, materia) VALUES (?, ?, ?, ?)";
    $stmt_insertar_content = $link->prepare($sql_insertar_content);
    $stmt_insertar_content->bind_param("isss", $post_id, $titulo, $all, $materia);

    // Ejecutar la consulta para insertar los datos en la tabla contentPost
    if ($stmt_insertar_content->execute()) {
        // Si la consulta se ejecuta correctamente, devolver una respuesta al cliente
        echo "Datos insertados correctamente en contentPost";
    } else {
        // Si hay un error en la ejecución de la consulta, devolver un mensaje de error
        echo "Error al insertar datos en contentPost: " . $link->error;
    }
} else {
    // Si hay un error en la ejecución de la consulta para insertar el post, devolver un mensaje de error
    echo "Error al guardar el post: " . $link->error;
}

// Cerrar las consultas y liberar los recursos
$stmt_insertar_post->close();
$stmt_insertar_content->close();
$link->close();
?>
