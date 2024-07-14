<?php

$action = isset($_GET['action']) ? $_GET['action'] : '';
require_once('../model/notificationModel.php');
$notificationModel = new NotificationModel();
switch ($action) {
    case 'get_all':
        echo $notificationModel->getNotifications();
        break;
    case 'get':
        echo $notificationModel->getNotifications();
        break;
    default:
        echo json_encode(['error' => true, 'message' => 'Acción inválida', 'data' => []]);
        break;
}
