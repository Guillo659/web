<?php
session_start();

if (isset($_POST['id'])) {
    if ($_SESSION['sudo'] == true) {
        require_once "../controller/config.php";

        $comment_id = $_POST['id'];

        $sql_delete_comment = "DELETE FROM comentarios WHERE id = ?";
        $stmt_delete_comment = $link->prepare($sql_delete_comment);
        $stmt_delete_comment->bind_param("i", $comment_id);
        if ($stmt_delete_comment->execute()) {
            echo "success";
        } else {
            echo "Error al eliminar el comemtario: " . $link->error;
        }
        $stmt_delete_comment->close();
        $link->close();
    } else {
        echo "No tiene permiso para eliminar el comentario";
    }
} else {
    echo "No se ha proporcionado el ID del comentario";
}
?>