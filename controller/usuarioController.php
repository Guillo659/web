<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';
require_once('../model/usuarioModel.php');
$usuarioModel = new UsuarioModel();
switch ($action) {
    case 'find_access':
        echo $usuarioModel->find_access();
        break;

    default:
        echo json_encode(['error' => true, 'message' => 'Acción inválida', 'data' => []]);
        break;
}
