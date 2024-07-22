<?php
require_once('../controller/config.php');

class PostModel {
    private $resp;

    public function __construct() {
        date_default_timezone_set('America/Bogota');
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }

    public function setVisibilityPost() {
        try {
            $post_id = intval($_POST['post_id']);
            $visibility = intval($_POST['visibility']);
            global $link;
            $sql = "UPDATE posts SET visibility=? WHERE id =?;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $visibility, $post_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $this->resp['message'] = 'Visibilidad del post cambiada correctamente';
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al cambiar la visibilidad del post';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function resetResponse() {
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }

    public function __destruct() {
        global $link;
        mysqli_close($link);
    }
}
