<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'nfxzwsuypk');
define('DB_PASSWORD', 'zVWw8QNjZu');
define('DB_NAME', 'nfxzwsuypk');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}
?>
