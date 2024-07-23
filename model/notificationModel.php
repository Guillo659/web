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

    public function getNotificationsUnreaded() {
        if (intval($_SESSION['role']) == 1) {
            try {
                global $link;
                $sql = "SELECT
              usuarios.id AS 'usuario_id',
              usuarios.name AS 'usuario_name',
              usuarios.prole AS usuario_prole,
              usuarios.profileimage AS 'usuario_imagen',
              notification.id AS 'notificacion_id',
              notification.message AS 'notificacion_message',
              notification.post_id,
              notification.date_created AS 'notificacion_date_created',
              posts.title AS 'post_title',
              posts.content AS 'post_content',
              posts.imagen AS 'post_imagen',
              posts.materia AS 'post_materia',
              posts.date_created AS post_date_created,
              posts.publictype
              FROM notification INNER JOIN posts ON notification.post_id=posts.id INNER JOIN usuarios ON posts.authorid=usuarios.id
              WHERE is_read = 0 ORDER BY notificacion_date_created ASC;";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_execute($stmt);
                $this->resp['data'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            } catch (Exception $e) {
                $this->resp['error'] = true;
                $this->resp['message'] = 'Error al obtener las notificaciones no leidas';
                $this->resp['ex'] = $e->getMessage();
            }
        } else {
            $this->resp['error'] = true;
            $this->resp['message'] = 'No tiene permisos para acceder a esta información';
        }
        return json_encode($this->resp);
    }

    public function markNotificationReaded() {
        try {
            global $link;
            $this->resetResponse();
            $id = intval($_POST['id']);
            $current_date = new DateTime();
            $format_date = $current_date->format('Y-m-d H:i:s');
            $sql = "UPDATE notification SET is_read = 1, date_readed=? WHERE id =?;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "si", $format_date, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $this->resp['message'] = 'Notificación marcada como leída';
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al marcar la notificación como leída';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function resetResponse() {
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }
}
