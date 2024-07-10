<?php
include '../controller/config.php';

$texto = $_POST['texto'];

$query = "SELECT id, title, SUBSTRING(content, 1, 100) as content FROM posts WHERE (title LIKE ? OR content LIKE ?) AND visibility = 1 AND publictype = 's';";

$stmt = $link->prepare($query);

$textoParam = '%' . $texto . '%';
$stmt->bind_param('ss', $textoParam, $textoParam);
$stmt->execute();
$result = $stmt->get_result();

$coincidencias = [];

while ($row = $result->fetch_assoc()) {
    $coincidencias[] = $row;
}

echo json_encode($coincidencias);

$stmt->close();
$link->close();
?>
