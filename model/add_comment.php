<?php
session_start();

function obtenerConteoLikes($post_id) {
    global $link; // Hacer referencia a la conexión a la base de datos

    // Realiza una consulta a la base de datos para obtener el conteo de likes
    $sql = "SELECT likes, comments FROM posts WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $likes, $comments); // Definir dos variables para almacenar los resultados
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            // Crear un array asociativo para devolver ambos valores
            $conteo = [
                'likes' => $likes,
                'comments' => $comments
            ];
            
            return $conteo;
        }
    }
    // En caso de error, devuelve un array con ambos valores a 0
    return [
        'likes' => 0,
        'comments' => 0
    ];
}

// Verificar si se recibieron datos del formulario mediante POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require_once "../controller/config.php";

    // Recuperar los datos del formulario
    $post_id = $_POST["post_id"];
    $author = $_SESSION['username']; // Ya no recuperamos esto del formulario
    $userid = $_SESSION['usuario_id'];
    $content = strip_tags($_POST["comment"]);

    // Preparar la consulta SQL para insertar el comentario en la base de datos
    $sql = "INSERT INTO comentarios (post_id, author, content) VALUES (?, ?, ?)";

    // Preparar la declaración SQL
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Vincular las variables a la declaración preparada como parámetros
        mysqli_stmt_bind_param($stmt, "iss", $post_id, $author, $content);

        // Ejecutar la declaración
        if (mysqli_stmt_execute($stmt)) {
            // El comentario se agregó correctamente

            // Actualizar el contador de comentarios en la tabla de posts
            $sql = "UPDATE posts SET comments = comments + 1 WHERE id = ?";
            if ($stmtUpdate = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmtUpdate, "i", $post_id);
                mysqli_stmt_execute($stmtUpdate);
                mysqli_stmt_close($stmtUpdate);
            }

            // Obtener el nuevo conteo de comentarios
            $newCount = obtenerConteoLikes($post_id)['comments'];

            // Crear un array asociativo para la respuesta
            $response = [
                'status' => 'success',
                'message' => 'Comentario agregado con éxito',
                'count' => $newCount
            ];

            // Devolver la respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Error al ejecutar la declaración
            $response = [
                'status' => 'error',
                'message' => 'Error al agregar el comentario: ' . mysqli_error($link)
            ];

            // Devolver la respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        }

        // Cerrar la declaración preparada
        mysqli_stmt_close($stmt);
    } else {
        // Error al preparar la declaración SQL
        $response = [
            'status' => 'error',
            'message' => 'Error al preparar la declaración SQL: ' . mysqli_error($link)
        ];

        // Devolver la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($link);
} else {
    // Si se intenta acceder a este script directamente sin enviar datos del formulario, redireccionar a una página de error
    $response = [
        'status' => 'error',
        'message' => 'Acceso no autorizado'
    ];

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
