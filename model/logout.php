<?php
// Inicia la sesión
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Finaliza la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión u otra página de tu elección
header("location: ../index.php");
exit;
?>
