<?php
// Incluir el archivo de configuración de la base de datos
require_once "../controller/config.php";

// Verificar si se ha recibido un ID de publicación válido
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Consultar la base de datos para obtener los comentarios asociados a la publicación
    $sql = "SELECT comentarios.*, usuarios.profileimage, usuarios.name as authorname FROM comentarios JOIN usuarios ON comentarios.authorid = usuarios.id WHERE comentarios.post_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            // Crear un array para almacenar los comentarios
            $comments = array();

            // Iterar sobre los resultados y almacenar los comentarios en el array
            while ($row = mysqli_fetch_assoc($result)) {
                $comments[] = $row;
            }

            // Devolver los comentarios en formato JSON
            echo json_encode($comments);
        } else {
            echo "Error al ejecutar la consulta";
        }

        // Cerrar la consulta preparada
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la preparación de la consulta";
    }
} else {
    echo "ID de publicación no especificado";
}
?>
