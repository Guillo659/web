<?php
session_start();
// Incluir el archivo de configuración de la base de datos
require_once('../controller/config.php');

// Verificar si se recibieron los datos del formulario
if(isset($_POST['profile_name']) && isset($_POST['profile_bio'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Consultar los datos actuales del usuario
    $consulta = "SELECT name, bio, profileimage FROM usuarios WHERE id = ?";
    $stmt = $link->prepare($consulta);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario_actual = $resultado->fetch_assoc();

    // Obtener los datos del formulario
    $nombre = strip_tags($_POST['profile_name']);
    $bio = strip_tags($_POST['profile_bio']);
    $image = isset($_SESSION['image_profile']) ? $_SESSION['image_profile'] : $usuario_actual['profileimage'];

    // Comprobar si los datos son diferentes
    if($usuario_actual['name'] != $nombre || $usuario_actual['bio'] != $bio || $usuario_actual['profileimage'] != $image) {
        // Realizar la actualización en la base de datos
        if (isset($_SESSION['image_profile'])) {
            $sql = "UPDATE usuarios SET name = ?, bio = ?, profileimage = ? WHERE id = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $bio, $image, $usuario_id);
            unset($_SESSION['image_profile']);
        } else {
            $sql = "UPDATE usuarios SET name = ?, bio = ? WHERE id = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("ssi", $nombre, $bio, $usuario_id);
        }
        if($stmt->execute()) {
            header("Location: ../index.php");
            exit();
            } else {
            echo "Error al actualizar los datos en la base de datos: " . $link->error;
        }
    } else {
        echo "Los datos del formulario son iguales a los datos actuales del usuario. No se realizó ninguna actualización.";
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $link->close();
} else {
    echo "No se recibieron los datos completos del formulario.";
}
?>
