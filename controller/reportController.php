<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';
require_once('../model/reportModel.php');
$reportModel = new ReportModel();
switch ($action) {
    case 'reportar':
        echo $reportModel->reportar();
        break;

    default:
        echo json_encode(['error' => true, 'message' => 'Acción inválida', 'data' => []]);
        break;
}
