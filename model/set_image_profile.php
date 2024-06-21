<?php
session_start();
// Verificamos si se subió un archivo
if(isset($_FILES["imagen"])) {
    $file = $_FILES["imagen"];

    // Ruta donde se guardará la imagen original
    $targetDirectory = "../public/uploads/";
    $fileName = uniqid() . '_' . basename($file["name"]);
    $targetFile = $targetDirectory . $fileName;
    $path = 'public/uploads/' . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Permitir sólo ciertos formatos de archivo
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $uploadOk = 0;
    }

    // Si todo está bien, intentar subir el archivo
    if ($uploadOk == 1) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            // Convertir la imagen al formato WebP
            $imagen_original = $targetFile;
            $img_uniq_name = uniqid() . '.webp';
            $imagen_webp = $targetDirectory .  $img_uniq_name;

            // Obtener información sobre la imagen
            $imagen_info = getimagesize($imagen_original);
            $imagen_type = $imagen_info[2]; // Tipo de imagen

            // Cargar la imagen original según su tipo
            switch ($imagen_type) {
                case IMAGETYPE_JPEG:
                    $imagen = imagecreatefromjpeg($imagen_original);
                    break;
                case IMAGETYPE_PNG:
                    $imagen = imagecreatefrompng($imagen_original);
                    break;
                case IMAGETYPE_GIF:
                    $imagen = imagecreatefromgif($imagen_original);
                    break;
                // Puedes agregar otros tipos de imagen según sea necesario
                default:
                    // Si el tipo de imagen no es compatible, eliminamos el archivo y retornamos un mensaje de error
                    unlink($imagen_original);
                    echo "Unsupported image type.";
                    exit;
            }

            // Guardar la imagen en formato WebP
            if (imagewebp($imagen, $imagen_webp)) {
                 // Eliminar el archivo original
                 unlink($imagen_original);
                 $_SESSION['image_profile'] = $imagen_webp;
                // Devolver la ruta del archivo WebP
                echo $imagen_webp;
            } else {
                echo "Error converting image to WebP.";
            }

            // Liberar memoria
            imagedestroy($imagen);
        } else {
            echo "Hubo un error al subir el archivo.";
        }
    } else {
        echo "El archivo no es una imagen válida o es demasiado grande.";
    }
} else {
    echo "No se ha seleccionado ninguna imagen.";
}

?>
