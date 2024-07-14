<?php
require_once('../controller/config.php');

class NotificationModel {
    private $resp;

    public function __construct() {
        date_default_timezone_set('America/Bogota');
        session_start();
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }

    public function getNotifications() {
        try {
            global $link;
            $sql = "SELECT * FROM notification;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_execute($stmt);
            $this->resp['data'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al obtener las notificaciones';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function getNotification() {
        try {
            global $link;
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM notifications WHERE id =?;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $this->resp['data'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al obtener la notificación';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function resetResponse() {
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }
}
