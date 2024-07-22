<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';
require_once('../model/reportModel.php');
$reportModel = new ReportModel();
switch ($action) {
    case 'reportar':
        echo $reportModel->reportar();
        break;

    case 'obtener_reportes':
        echo $reportModel->obtenerReportes();
        break;

    case 'obtener_reportes_post':
        echo $reportModel->obtenerReporteByPosts();
        break;

    default:
        echo json_encode(['error' => true, 'message' => 'Acción inválida', 'data' => []]);
        break;
}
