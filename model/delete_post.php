<?php
session_start();

if (isset($_POST['id'])) {
    if ($_SESSION['sudo'] == true) {
        require_once "../controller/config.php";

        $post_id = $_POST['id'];

        $sql_delete_post = "UPDATE posts SET visibility = 0 WHERE id = ?";
        $stmt_delete_post = $link->prepare($sql_delete_post);
        $stmt_delete_post->bind_param("i", $post_id);
        if ($stmt_delete_post->execute()) {
            echo "success";
        } else {
            echo "Error al eliminar el post: " . $link->error;
        }
        $stmt_delete_post->close();
        $link->close();
    } else {
        echo "No tiene permiso para eliminar el post";
    }
} else {
    echo "No se ha proporcionado el ID del post";
}
?>
