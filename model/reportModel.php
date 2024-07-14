<?php
require_once('../controller/config.php');
require_once('../helpers/general.php');
class ReportModel {
    private $resp;
    public function __construct() {
        date_default_timezone_set('America/Bogota');
        session_start();
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }

    public function reportar() {
        $this->resetResponse();
        global $link;
        $current_date = new DateTime();
        $format_date = $current_date->format('Y-m-d H:i:s');
        $post_id = intval($_POST['post_id']);
        $reason = cleanInput($_POST['reason']);
        $link->begin_transaction();
        try {
            $sql = "INSERT INTO report(post_id, usuario_id, reason, date_created) VALUES (?,?,?,?);";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "iiss", $post_id, $_SESSION['id'], $reason, $format_date);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            //Verificar el número de reports para el posts
            $sql = "SELECT COUNT(1) AS count_report FROM report WHERE post_id=?;";
            $query = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($query, "i", $post_id);
            mysqli_stmt_execute($query);
            mysqli_stmt_bind_result($query, $count_report);
            mysqli_stmt_fetch($query);
            mysqli_stmt_close($query);
            //Mínimo 5 reportes para mandar la notificación y ocultar el posts
            $this->resp['data'] = ['count_report' => $count_report];
            if ($count_report >= 5) {
                //Agregamos a la blacklist el post
                $sql = "INSERT INTO blacklist(post_id, date_created) VALUES(?,?);";
                $query = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($query, "is", $post_id, $format_date);
                mysqli_stmt_execute($query);
                mysqli_stmt_close($query);

                /*Creamos la notificación para los admin
                * El message de la notificación tiene el siguiente formato:
                * Se agregó el post $title_post del usuario $usuario_name a la lista negra
                */

                //Consultamos el title_post y el name del usuario
                $sql = "SELECT title, name FROM posts INNER JOIN usuarios ON usuarios.id=posts.authorid WHERE posts.id=?";
                $query = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($query, "i", $post_id);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $title_post, $usuario_name);
                mysqli_stmt_fetch($query);
                mysqli_stmt_close($query);

                $sql = "INSERT INTO notification(post_id, type, message, date_created) VALUES(?, 'blacklist', ?, ?);";
                $query = mysqli_prepare($link, $sql);
                $message = "Se agregó el post $title_post del usuario $usuario_name a la lista negra.";
                mysqli_stmt_bind_param($query, "iss", $post_id, $message, $format_date);
                mysqli_stmt_execute($query);
                mysqli_stmt_close($query);

                //Procedemos a ocultar el post
                $sql = "UPDATE posts SET visibility=0 WHERE id=?;";
                $query = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($query, "i", $post_id);
                mysqli_stmt_execute($query);
                mysqli_stmt_close($query);
                $this->resp['is_add_blacklist'] = true;
            }
            $link->commit();
            $this->resp['error'] = false;
            $this->resp['message'] = 'Reporte enviado correctamente';
        } catch (Exception $e) {
            $link->rollback();
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al enviar el reporte: ';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function resetResponse() {
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null, 'is_add_blacklist' => false];
    }

    public function __destruct() {
        global $link;
        mysqli_close($link);
    }
}
