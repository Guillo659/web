<?php

// Verificar si se envió un archivo
if(isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Verificar si no hay archivo seleccionado
    if($file['error'] === UPLOAD_ERR_NO_FILE) {
        $response = array("success" => 0, "error" => "No file selected");
        echo json_encode($response);
        exit;
    }

    $dangerousExtensions = array('php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bat', 'cmd', 'vbs', 'js', 'html', 'htm');

// Validar la extensión del archivo
if (in_array($fileData['extension'], $dangerousExtensions)) {
    echo "Extensión de archivo no permitida.";
} else {
    $filename = basename($file['name']);
    $uploadDir = '../public/uploads/';
    $uploadFile = $uploadDir . $filename;

    if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
        $fileData = array(
            "title" => $filename,
            "size" => $file['size'],
            "extension" => pathinfo($filename, PATHINFO_EXTENSION),
            "url" => '../public/uploads/' . $filename
        );

        // Retornar la respuesta en el formato especificado
        $response = array("success" => 1, "file" => $fileData);
        echo json_encode($response);
    } else {
        $response = array("success" => 0, "error" => "Error uploading file");
        echo json_encode($response);
    }
}

} else {
    $response = array("success" => 0, "error" => "No file part");
    echo json_encode($response);
}
?>
