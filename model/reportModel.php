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

    public function obtenerReportes() {
        try {
            global $link;
            $sql = "WITH RankedReports AS (
                        SELECT
                            report.id AS report_id,
                            report.post_id,
                            report.reason,
                            report.date_created AS report_date_created,
                            posts.title AS post_title,
                            posts.content AS post_content,
                            posts.date_created AS post_date_created,
                            posts.imagen AS post_imagen,
                            posts.materia AS post_materia,
                            posts.publictype,
                            usuarios.id AS usuario_id,
                            usuarios.name AS usuario_name,
                            usuarios.prole AS usuario_prole,
                            usuarios.profileimage AS usuario_imagen,
                            ROW_NUMBER() OVER (PARTITION BY report.post_id ORDER BY report.date_created DESC) AS rn
                        FROM report
                        INNER JOIN posts ON report.post_id = posts.id
                        INNER JOIN usuarios ON posts.authorid = usuarios.id
                    )
                    SELECT
                        report_id,
                        post_id,
                        reason,
                        report_date_created,
                        post_title,
                        post_content,
                        post_date_created,
                        post_imagen,
                        post_materia,
                        publictype,
                        usuario_id,
                        usuario_name,
                        usuario_prole,
                        usuario_imagen
                    FROM RankedReports
                    WHERE rn = 1;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_execute($stmt);
            $this->resp['data'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al obtener los reportes';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function obtenerReporteByPosts() {
        $this->resetResponse();
        global $link;
        $post_id = intval($_POST['post_id']);
        try {
            $sql = "SELECT report.id AS 'report_id', report.post_id, report.usuario_id, report.reason, report.date_created AS 'report_date_created', usuarios.name AS 'usuario_name', usuarios.profileimage AS 'usuario_imagen', usuarios.prole AS 'usuario_prole', posts.visibility AS 'post_visibility' FROM report INNER JOIN usuarios ON report.usuario_id=usuarios.id INNER JOIN posts ON report.post_id=posts.id WHERE report.post_id=?;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "i", $post_id);
            mysqli_stmt_execute($stmt);
            $this->resp['data'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al obtener los reportes del post';
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
