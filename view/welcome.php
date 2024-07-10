<?php
session_start();
if(isset($_SESSION['username'])) {
    header("Location: view/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodico</title>
</head>
<body>
    <main>
        <p><a href="../view/login.php">Logueate</a></p>
        <p><a href="../view/register.php">Registrar</a></p>
        <div id="letter-container"></div>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    var container = document.getElementById("letter-container");

    // Función para generar una letra "M" y agregarla al contenedor
    function generateLetter() {
        var letter = document.createElement("span");
        letter.textContent = "M";
        letter.classList.add("letter");
        container.appendChild(letter);

        // Eliminar la letra después de un tiempo
        setTimeout(function() {
            container.removeChild(letter);
        }, 1000); // Ajusta el tiempo según la duración deseada
    }

    // Función para generar letras "M" en intervalos de tiempo
    function generateLetters() {
        setInterval(generateLetter, 500); // Ajusta el intervalo de tiempo entre letras
    }

    // Llamar a la función para generar letras "M"
    generateLetters();
});

    </script>
</body>
</html>