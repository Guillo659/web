<?php
// like_post.php

require_once "../controller/config.php";

// Verificar si se ha enviado un ID de publicación válido y un nombre de usuario
if(isset($_GET['post_id']) && isset($_GET['username'])) {
    $postId = $_GET['post_id'];
    $username = $_GET['username'];
    
    // Verificar si el usuario ya ha dado like a esta publicación
    $sql_check_like = "SELECT * FROM likes WHERE id_post = ? AND username = ?";
    if($stmt_check_like = mysqli_prepare($link, $sql_check_like)){
        mysqli_stmt_bind_param($stmt_check_like, "is", $postId, $username);
        mysqli_stmt_execute($stmt_check_like);
        mysqli_stmt_store_result($stmt_check_like);
        
        // Si ya existe un registro de like para esta publicación y este usuario, no hagas nada
        if(mysqli_stmt_num_rows($stmt_check_like) > 0){
            // Ya existe un registro de like para esta publicación y este usuario
            // Puedes mostrar un mensaje o realizar alguna acción si lo deseas
            echo "Ya has dado like a esta publicación.";
        } else {
            // No existe un registro de like para esta publicación y este usuario, así que procede a insertar el like
            $sql_insert_like = "INSERT INTO likes (id_post, username) VALUES (?, ?)";
            if($stmt_insert_like = mysqli_prepare($link, $sql_insert_like)){
                mysqli_stmt_bind_param($stmt_insert_like, "is", $postId, $username);
                mysqli_stmt_execute($stmt_insert_like);
                echo "Like agregado correctamente.";
            } else {
                echo "Error al preparar la consulta de inserción.";
            }
        }
        
        mysqli_stmt_close($stmt_check_like);
    } else {
        echo "Error al preparar la consulta de verificación de like.";
    }
} else {
    echo "ID de publicación o nombre de usuario no válidos.";
}

?>
