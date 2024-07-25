<?php
require_once('../controller/config.php');
require_once('../helpers/general.php');
class UsuarioModel {
    private $resp;
    public function __construct() {
        $this->resp = ['error' => false, 'message' => '', 'data' => [], 'ex' => null];
    }

    public function find_access() {
        try {
            $name = cleanInput($_POST['name']);
            global $link;
            $sql = "SELECT username, password FROM usuarios WHERE BINARY name =?;";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            $this->resp['data'] = $result != null ? [$result] : [];
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $this->resp['error'] = true;
            $this->resp['message'] = 'Error al procesar encontrar credenciales';
            $this->resp['ex'] = $e->getMessage();
        }
        return json_encode($this->resp);
    }

    public function __destruct() {
        global $link;
        mysqli_close($link);
    }
}
