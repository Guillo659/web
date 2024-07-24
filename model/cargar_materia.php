<?php
include '../controller/config.php';

$materia = $_GET['materia'];
if ($materia == "todas") {
    $sql = "SELECT posts.id, posts.title, posts.content, posts.imagen, posts.materia, posts.publictype, usuarios.id as user_id, usuarios.username, usuarios.name, usuarios.profileimage, usuarios.prole, posts.date_created 
        FROM posts 
        INNER JOIN usuarios ON posts.authorid = usuarios.id 
        WHERE posts.publictype='s' AND posts.visibility = 1 
        ORDER BY posts.date_created DESC";
    $stmt = $link->prepare($sql);
} else {
    $sql = "SELECT posts.id, posts.title, posts.content, posts.imagen, posts.materia, posts.publictype, usuarios.id as user_id, usuarios.username, usuarios.name, usuarios.profileimage, usuarios.prole, posts.date_created 
        FROM posts 
        INNER JOIN usuarios ON posts.authorid = usuarios.id 
        WHERE posts.materia = ? 
        AND posts.visibility = 1 
        ORDER BY posts.date_created DESC";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('s', $materia);
}
$stmt->execute();

$result = $stmt->get_result();

$posts = [];

while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode($posts);

$stmt->close();
$link->close();
