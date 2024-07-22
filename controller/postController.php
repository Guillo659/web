<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';
require_once('../model/postModel.php');
$postModel = new PostModel();
switch ($action) {
    case 'set_post_visibility':
        echo $postModel->setVisibilityPost();
        break;

    default:
        echo json_encode(['error' => true, 'message' => 'Acción inválida', 'data' => []]);
        break;
}
