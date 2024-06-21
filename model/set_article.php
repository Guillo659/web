<?php
// Incluir archivo de configuración de la base de datos
require_once "../controller/config.php";

// Obtener datos JSON del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibieron datos
if(isset($data->blocks) && !empty($data->blocks)){
    // Iterar sobre los bloques recibidos
    foreach($data->blocks as $block){
        // Extraer los datos relevantes de cada bloque
        $id = $block->id;
        $type = $block->type;
        $text = $block->data->text;

        // Mostrar los datos en pantalla
        echo "ID: $id<br>";
        echo "Type: $type<br>";
        echo "Text: $text<br>";
        echo "<hr>";
    }
} else {
    echo "No se recibieron datos válidos.";
}
?>
