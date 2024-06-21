<?php /*

// Función para obtener los metadatos de una URL
function fetchMetadata($url) {
    // Verificar si la URL proporcionada comienza con http:// o https://
    if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
        // Si no comienza con ninguno de los protocolos, agregar http:// por defecto
        $url = 'http://' . $url;
    }

    // Obtener el contenido HTML de la URL
    $html = file_get_contents($url);

    if ($html === false) {
        return array("success" => 0, "error" => "Unable to fetch URL");
    }

    // Crear un nuevo objeto DOMDocument
    $doc = new DOMDocument();
    // Suprimir los errores de análisis
    libxml_use_internal_errors(true);
    // Cargar el HTML en el objeto DOMDocument
    $doc->loadHTML($html);

    // Inicializar un array para almacenar los metadatos
    $metadata = array();

    // Obtener la URL del sitio
    $metadata['url'] = $url;

    // Obtener el título de la página (si está disponible)
    $title = $doc->getElementsByTagName('title')->item(0);
    if ($title) {
        $metadata['title'] = $title->textContent;
    }

    // Obtener la descripción del sitio (si está disponible)
    $metaTags = $doc->getElementsByTagName('meta');
    foreach ($metaTags as $metaTag) {
        if ($metaTag->getAttribute('name') == 'description') {
            $metadata['description'] = $metaTag->getAttribute('content');
            break;
        }
    }

    // Obtener la imagen del sitio (si está disponible)
    $images = $doc->getElementsByTagName('img');
    if ($images->length > 0) {
        $metadata['image'] = $images->item(0)->getAttribute('src');
    }

    // Verificar si se obtuvieron metadatos relevantes
    if (empty($metadata)) {
        return array("success" => 0, "error" => "No metadata found");
    }

    // Construir la respuesta en el formato especificado
    $response = array(
        "success" => 1,
        "link" => $url,
        "meta" => $metadata
    );

    return $response;
}

// Verificar si se proporcionó una URL a través de GET
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    // Llamar a la función fetchMetadata para obtener los metadatos
    $metadata = fetchMetadata($url);
    // Devolver la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($metadata);
} else {
    // Devolver un mensaje de error si no se proporcionó una URL
    echo json_encode(array("success" => 0, "error" => "No URL provided"));
}
*/
?>
