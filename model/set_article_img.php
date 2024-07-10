<?php
// Función para manejar la carga de archivos desde el dispositivo
function uploadByFile($file) {
    // Directorio de destino para guardar los archivos subidos
    $uploadDirectory = '../public/uploads/';

    // Genera un nombre único para el archivo
    $fileName = uniqid() . '_' . $file['name'];

    // Ruta completa del archivo en el servidor
    $uploadFilePath = $uploadDirectory . $fileName;

    // Intenta mover el archivo cargado al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        // Convertir la imagen al formato WebP
        $imagen_original = $uploadFilePath;
        $img_name = uniqid() . '.webp';
        $return_rute_img = '../public/uploads/' . $img_name;
        $imagen_webp = $uploadDirectory . $img_name;

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
                return array(
                    'success' => 0,
                    'error' => 'Unsupported image type.'
                );
        }

        // Guardar la imagen en formato WebP
        imagewebp($imagen, $imagen_webp);

        // Liberar memoria
        imagedestroy($imagen);

        // Eliminar la imagen original después de convertirla a WebP
        unlink($imagen_original);

        // La carga del archivo y conversión fueron exitosas
        return array(
            'success' => 1,
            'file' => array(
                'url' => $return_rute_img,
                // Puedes agregar cualquier información adicional que desees almacenar
                // Por ejemplo: ancho, alto, id, etc.
            )
        );
    } else {
        // La carga del archivo falló
        return array(
            'success' => 0,
            'error' => 'Failed to upload file.'
        );
    }
}

// Función para manejar la carga mediante la pegatina de una URL
function uploadByUrl($requestData) {
    // Verifica si se proporcionó una URL en los datos de la solicitud
    if (isset($requestData['url'])) {
        // Obtiene la URL de la imagen del cuerpo de la solicitud
        $imageUrl = $requestData['url'];

        // Realiza la descarga del archivo desde la URL proporcionada
        $uploadDirectory = '../public/uploads/'; // Directorio de destino para guardar los archivos descargados
        $fileName = uniqid() . '_' . basename($imageUrl); // Genera un nombre único para el archivo
        $uploadFilePath = $uploadDirectory . $fileName; // Ruta completa del archivo en el servidor

        // Intenta descargar y guardar el archivo desde la URL proporcionada
        if (file_put_contents($uploadFilePath, file_get_contents($imageUrl))) {
            // Convertir la imagen al formato WebP
            $imagen_original = $uploadFilePath;
            $imagen_webp = $uploadDirectory . uniqid() . '_' . pathinfo($fileName, PATHINFO_FILENAME) . '.webp';

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
                    return array(
                        'success' => 0,
                        'error' => 'Unsupported image type.'
                    );
            }

            // Guardar la imagen en formato WebP
            imagewebp($imagen, $imagen_webp);

            // Liberar memoria
            imagedestroy($imagen);

            // Eliminar la imagen original después de convertirla a WebP
            unlink($imagen_original);

            // La descarga, guardado y conversión fueron exitosos
            return array(
                'success' => 1,
                'file' => array(
                    'url' => $imagen_webp,
                    // Puedes agregar cualquier información adicional que desees almacenar
                    // Por ejemplo: ancho, alto, id, etc.
                )
            );
        } else {
            // La descarga o guardado del archivo fallaron
            return array(
                'success' => 0,
                'error' => 'Failed to download or save file.'
            );
        }
    } else {
        // No se proporcionó ninguna URL en los datos de la solicitud
        return array(
            'success' => 0,
            'error' => 'No URL provided.'
        );
    }
}

// Función para manejar la carga mediante arrastrar y soltar o desde el portapapeles
function uploadByFormData($file) {
    // Directorio de destino para guardar los archivos subidos
    $uploadDirectory = '../public/uploads/';

    // Genera un nombre único para el archivo
    $fileName = uniqid() . '_' . $file['name'];

    // Ruta completa del archivo en el servidor
    $uploadFilePath = $uploadDirectory . $fileName;

    // Intenta mover el archivo cargado al directorio de destino
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        // Convertir la imagen al formato WebP
        $imagen_original = $uploadFilePath;
        $imagen_webp = $uploadDirectory . uniqid() . '_' . pathinfo($fileName, PATHINFO_FILENAME) . '.webp';

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
                return array(
                    'success' => 0,
                    'error' => 'Unsupported image type.'
                );
        }

        // Guardar la imagen en formato WebP
        imagewebp($imagen, $imagen_webp);

        // Liberar memoria
        imagedestroy($imagen);

        // Eliminar la imagen original después de convertirla a WebP
        unlink($imagen_original);

        // La carga del archivo y conversión fueron exitosas
        return array(
            'success' => 1,
            'file' => array(
                'url' => $imagen_webp,
                // Puedes agregar cualquier información adicional que desees almacenar
                // Por ejemplo: ancho, alto, id, etc.
            )
        );
    } else {
        // La carga del archivo falló
        return array(
            'success' => 0,
            'error' => 'Failed to upload file.'
        );
    }
}

// Verifica el tipo de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si se está subiendo un archivo desde el dispositivo
    if (isset($_FILES['file'])) {
        $response = uploadByFile($_FILES['file']);
    }
    // Verifica si se está pegando una URL
    elseif (isset($_POST['url'])) {
        // Decodifica los datos JSON del cuerpo de la solicitud
        $requestData = json_decode(file_get_contents('php://input'), true);
        $response = uploadByUrl($requestData);
    }
    // Verifica si se está cargando mediante FormData
    elseif (isset($_FILES['image'])) {
        $response = uploadByFormData($_FILES['image']);
    } else {
        // La solicitud no contiene los datos necesarios
        $response = array(
            'success' => 0,
            'error' => 'Invalid request.'
        );
    }
} else {
    // La solicitud no es del tipo POST
    $response = array(
        'success' => 0,
        'error' => 'Invalid request method.'
    );
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
