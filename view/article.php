<?php
require_once "../controller/config.php";

session_start();
if($_SESSION['username'] === "none") {
    header("Location: login.php");
    exit;
}else {
    $usuario = $_SESSION['username'];
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Definir una lista de cadenas que se encuentran comúnmente en los agentes de usuario de dispositivos móviles
$mobile_agents = array('iPhone','iPad','Android','webOS','BlackBerry','Windows Phone');

// Verificar si el agente de usuario contiene alguna de las cadenas de dispositivos móviles
$is_mobile = false;

foreach($mobile_agents as $agent) {
    if (stripos($user_agent, $agent) !== false) {
        $is_mobile = true;
        break;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../src/css/style-setarticle.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="shortcut icon" href="https://res.cloudinary.com/dvdhtdzwp/image/upload/v1721276122/logoico.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../src/js/script.js" defer></script>
</head>
<body>

<main>
    <div class="content">
        <textarea rows="1" id="title" placeholder="Titulo"></textarea>
        <div id="editorjs"></div>
    </div>

    <div id="googlesites" class="google-sites">
        <form class="sites-form" action="preview.php" method="post">
            <input type="text" name="url" placeholder="Ingrese la URL">
            <button type="submit">Enviar</button>
        </form>
    </div>
      
    <aside id="select-subject" role="select">
        <button aria-label="Choose">
            <span aria-hidden="true" class="material-symbols-rounded">book_4</span>
            <p>Choose</p>
        </button>
        <ul>
            <li tabindex="0" role="option">Filosofía</li>
            <li tabindex="0" role="option">Química</li>
            <li tabindex="0" role="option">Física</li>
            <li tabindex="0" role="option">Matemáticas</li>
            <li tabindex="0" role="option">Tecnología</li>
            <li tabindex="0" role="option">Sociales</li>
            <li tabindex="0" role="option">Castellano</li>
            <li tabindex="0" role="option">Inglés</li>
        </ul>
    </aside>

    <nav class="nav-tools">
        <a href="/" aria-label="Go home">
            <span aria-hidden="true" class="material-symbols-rounded">home</span>
        </a>
        <a href="/view/article.php" aria-label="Go articles" class="selected">
            <span aria-hidden="true" class="material-symbols-rounded">list_alt_add</span>
        </a>
        <button aria-label="Notifications">
            <span aria-hidden="true" class="material-symbols-rounded">notifications</span>
        </button>
        <button aria-label="Save article">SAVE</button>
    </nav>  
</main>

<div id="data-container">
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@2.6.0/dist/quote.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/attaches@1.3.0/dist/bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/inline-code@latest"></script>
    <script src="../src/js/editor.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let mostrarPopupBtn = document.getElementById("sites")
        })

        // Obtener referencia al botón
        const title = document.getElementById('title')
        const botonSave = document.querySelector('button[aria-label="Save article"]')
        const btnChoose = document.querySelector('#select-subject button')

        // Agregar event listener para manejar el clic en el botón
        botonSave.addEventListener('click', () => {
            editor.save().then((outputData) => {
                mostrarDatos(outputData)
            }).catch((error) => {
                console.log("Error al guardar los datos del editor", error)
            })
        })

        title.oninput = function() {
            this.style.minHeight = 'auto'
            this.style.minHeight = `${this.scrollHeight}px`  
        }

        // Función para mostrar los datos en el contenedor
        async function mostrarDatos(data) {
            if (!btnChoose.value) {
                alert('Escoge una materia')
                return btnChoose.focus()
            } else if (title.value.trim() === '') {
                alert('Escribe un título')
                return title.focus()
            }

            let tituloPost = ''
            let paragrafoPost = ''
            let photoPost = 0
            let materiaSeleccionada = btnChoose.value
            let tituloGet = title.value

            if (tituloPost === '') tituloPost = tituloGet
            
            let contenido = ''

            const dataContainer = document.getElementById('data-container')

            dataContainer.innerHTML = ''

            for (const block of data.blocks) {
                switch (block.type) {
                    case 'paragraph':
                        contenido += `<p>${block.data.text}</p>`;
                        if (paragrafoPost === '') paragrafoPost = block.data.text
                    break
                    case 'image':
                        contenido += `<img src="${block.data.file.url}" alt="${block.data.caption}">`;           
                    if (photoPost === 0) photoPost = block.data.file.url
                    break
                    case 'header':
                        let n = block.data.level
                        contenido += `<h${n}>${block.data.text}</h${n}>`
                    break
                    case 'list':
                        if (block.data.style === "unordered") {
                            contenido += `<ul>`
                            for (const i of block.data.items) {
                                contenido += `<li>${i}</li>`
                            }
                            contenido += `</ul>`
                        } else if (block.data.style === "ordered") {
                            contenido += `<ol>`
                            for (const i of block.data.items) {
                                contenido += `<li>${i}</li>`
                            }
                            contenido += `</ol>`
                        }
                    break
                    case 'quote':
                        contenido += `<p>“${block.data.text}” (${block.data.caption})</p>`;
                    break
                    case 'attaches':
                        contenido += `<embed src="${block.data.file.url}" width="100%" height="200px" />`; 
                    break
                }
            }

        //dataContainer.innerHTML = contenido;
        console.log(contenido);
        // En lugar de imprimir el contenido HTML en el contenedor, redirecciona a otra página con el contenido como parámetro en la URL
        // Codificar el contenido en Base64
        /*var contenidoBase64 = btoa(contenido);

        // Construir la URL con el contenido Base64 como parámetro
        var url = "preview.php?contenido=" + encodeURIComponent(contenidoBase64);

        // Redireccionar a la otra página
        window.location.href = url;*/

        const datos = {
            titulo: tituloPost,
            contenido: paragrafoPost,
            image: photoPost,
            all: contenido,
            materia: materiaSeleccionada
        }

    $.ajax({
        type: "POST",
        url: "../model/subir_articulo.php",
        data: datos,
        success: response => {
            window.location.href = "http://localhost"
        },
        error: (xhr, status, error) => {
            console.error("Error al guardar el post:", error)
        }
    })
    }

    
    </script>
</div>
</body>
</html>