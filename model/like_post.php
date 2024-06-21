<?php
// like_post.php

require_once "../controller/config.php";

session_start();

$isAdded  = false;

// Verificar si se ha enviado un ID de publicación válido
if(isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];
    
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM likes WHERE post_id = ? AND username = ?";

    // Preparar la consulta
    if($stmt = mysqli_prepare($link, $sql)) {
        // Vincular parámetros a la declaración preparada
        mysqli_stmt_bind_param($stmt, "is", $postId, $username);
        // Ejecutar la consulta
        if(mysqli_stmt_execute($stmt)) {
            // Obtener el resultado de la consulta
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result)>0) {
                // Si el usuario ya ha dado like, eliminar el like
                $sql = "DELETE FROM likes WHERE post_id = ? AND username = ?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "is", $postId, $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                // Disminuir el conteo de likes en la publicación
                $sql = "UPDATE posts SET likes = likes - 1 WHERE id = ?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "i", $postId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt); 
            } else {
                // Si el usuario no ha dado like, agregar el like
                $sql = "INSERT INTO likes (post_id, username) VALUES (?, ?)";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "is", $postId, $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Incrementar el conteo de likes en la publicación
                $sql = "UPDATE posts SET likes = likes + 1 WHERE id = ?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "i", $postId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $isAdded = true;
            }
            // Obtener el nuevo conteo de likes para la publicación y devolverlo como respuesta
            $sql = "SELECT likes FROM posts WHERE id = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $likes);
            if(mysqli_stmt_fetch($stmt)){
                mysqli_stmt_close($stmt);
                $data = array (
                    'likes' => $likes,
                    'isAdded' => $isAdded 
                );
                echo json_encode($data); // Devolver el nuevo conteo de likes 
            }
        }
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($link);
?>
