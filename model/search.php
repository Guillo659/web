<?php
include '../controller/config.php';

$texto = $_POST['texto'];

$query = "SELECT posts.id, posts.title, posts.imagen, posts.materia, posts.publictype, usuarios.id as user_id, usuarios.username, usuarios.name, usuarios.profileimage, usuarios.prole, posts.date_created, 
         SUBSTRING(posts.content, 1, 100) as post_content FROM posts INNER JOIN usuarios ON posts.authorid = usuarios.id  WHERE (title LIKE ? OR content LIKE ?) AND visibility = 1 AND publictype = 's';";

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
