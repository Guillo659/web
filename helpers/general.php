<?php

/**
 * Limpia y valida los datos de entrada para evitar inyecciones SQL y otros ataques.
 *
 * @param string $data El dato a limpiar.
 * @return string El dato limpio y seguro.
 */
function cleanInput($data) {
    // Eliminar espacios en blanco al inicio y al final
    $data = trim($data);
    // Eliminar barras invertidas (\)
    $data = stripslashes($data);
    // Convertir caracteres especiales a entidades HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
