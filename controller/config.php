<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'dvypmrbzqd');
define('DB_PASSWORD', 'GefpaWF9bN');
define('DB_NAME', 'dvypmrbzqd');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}
?>
