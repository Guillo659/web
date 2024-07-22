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

// Incluye la biblioteca SimpleHTMLDOM
include('../src/lib/simplehtmldom/simple_html_dom.php');

// URL de la página web a scrapear

$url = $_POST['url'];

// Crea un objeto SimpleHTMLDOM para la página web
$html = file_get_html($url);

// Obtener el body del documento
$body = $html->find('body', 0);
$h1 = $html->find('h1', 0);

// Array de etiquetas que deseas buscar
$etiquetas_deseadas = array('img', 'h2', 'h3', 'h4', 'p', 'iframe');

// Array para almacenar el contenido de las etiquetas deseadas
$contenido_etiquetas = array();

// Función para buscar y almacenar contenido de etiquetas deseadas en orden
function buscar_y_almacenar_contenido($nodo, &$contenido_etiquetas, $etiquetas_deseadas) {
    // Iterar sobre los hijos del nodo
    foreach ($nodo->children() as $hijo) {
        // Si el hijo tiene hijos, llamar recursivamente a la función
        if ($hijo->has_child()) {
            buscar_y_almacenar_contenido($hijo, $contenido_etiquetas, $etiquetas_deseadas);
        }
        // Buscar y almacenar contenido de etiquetas deseadas
        if (in_array($hijo->tag, $etiquetas_deseadas)) {
            $contenido_etiquetas[] = $hijo->outertext;
        }
    }
}

// Llamar a la función para buscar y almacenar contenido de etiquetas deseadas
buscar_y_almacenar_contenido($body, $contenido_etiquetas, $etiquetas_deseadas);

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
        <!-- Aquí se mostrarán los datos -->
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest"></script>


<script>
    const editor = new EditorJS({

holder: 'editorjs',

placeholder:"Type here", 

tools: {

  header: Header,

  image: SimpleImage,

  list: List,

  quote: Quote,

  inlineCode: InlineCode,

  linkTool: LinkTool,

  embed: Embed,

},

tools: {

    header: {

      class: Header,

      config: {

        placeholder: 'Enter a header',

        levels: [2, 3, 4],

        defaultLevel: 2

      }

    },

    paragraph: {
      class: Paragraph,
      inlineToolbar: true,
    },

    image: SimpleImage,

    list: {

        class: List,

        inlineToolbar: true

      },

      embed: {

        class: Embed,

        inlineToolbar: true,

        config: {

          services: {

            youtube: true,

            coub: true,

            facebook: true

          }

        }

      },

      /*linkTool: {

        class: LinkTool,

        config: {

          endpoint: 'http://localhost/model/set_link.php', // Your backend endpoint for url data fetching,

        }

      },*/

      quote: {

        class: Quote,

        inlineToolbar: true,

        shortcut: 'CMD+SHIFT+O',

        config: {

          quotePlaceholder: 'Enter a quote',

          captionPlaceholder: 'Quote\'s author',

        },

      },

      attaches: {

        class: AttachesTool,

        config: {

          endpoint: 'http://localhost/model/set_attaches.php'

        }

      },

      inlineCode: {

        class: InlineCode,

        shortcut: 'CMD+SHIFT+M',

      },

      image: {

        class: ImageTool,

        config: {

            /**

             * Custom uploader

             */

            uploader: {

                /**

                 * Upload file to the server and return an uploaded image data

                 * @param {File} file - file selected from the device or pasted by drag-n-drop

                 * @return {Promise.<{success, file: {url}}>}

                 */

                uploadByFile(file){

                    // Create a FormData object

                    const formData = new FormData();

                    formData.append('file', file);

                    

                    // Send the FormData object to the server

                    return fetch('../model/set_article_img.php', {

                        method: 'POST',

                        body: formData

                    })

                    .then(response => response.json())

                    .then(data => {

                        return data;

                    });

                },

                

                uploadByUrl(url){

                    // Send the URL to the server

                    return fetch('../model/set_article_img.php', {

                        method: 'POST',

                        headers: {

                            'Content-Type': 'application/json'

                        },

                        body: JSON.stringify({url: url})

                    })

                    .then(response => response.json())

                    .then(data => {

                        return data;

                    });

                }

            } // Cierre del objeto uploader

        } // Cierre del objeto config

    },// Cierre del objeto image
  },
  data: {
    blocks: 
      <?php
// Array para almacenar los bloques de párrafos
$paragraphBlocks = array();

// Imprimir el contenido de las etiquetas deseadas en el orden en que aparecen en la página
foreach ($contenido_etiquetas as $contenido) {
    // Utilizar una expresión regular para buscar la etiqueta <p>
    if (preg_match('/<p\b[^>]*>(.*?)<\/p>/s', $contenido, $matches)) {
        // Almacenar el texto del párrafo en el array
        $paragraphBlocks[] = array(
            'type' => 'paragraph',
            'data' => array(
                'text' => $matches[1]
            )
        );
    } elseif (preg_match('/<h2\b[^>]*>(.*?)<\/h2>/s', $contenido, $matches)) {
      // Almacenar el texto del párrafo en el array
      $paragraphBlocks[] = array(
          'type' => 'header',
          'data' => array(
              'text' => $matches[1],
              'level' => 2
          )
      );
  } elseif (preg_match('/<h3\b[^>]*>(.*?)<\/h3>/s', $contenido, $matches)) {
    // Almacenar el texto del párrafo en el array
    $paragraphBlocks[] = array(
        'type' => 'header',
        'data' => array(
            'text' => $matches[1],
            'level' => 3
        )
    );
  } elseif (preg_match('/<h4\b[^>]*>(.*?)<\/h4>/s', $contenido, $matches)) {
    // Almacenar el texto del párrafo en el array
    $paragraphBlocks[] = array(
        'type' => 'header',
        'data' => array(
            'text' => $matches[1],
            'level' => 4
        )
    );
  } elseif (preg_match('/<img\b[^>]*src="([^"]*)"[^>]*>/s', $contenido, $matches)) {
    $paragraphBlocks[] = array(
        'type' => 'image',
        'data' => array(
            'url' => $matches[1]
        )
    );
  }
}

// Convertir el array a JSON
$jsonOutput = json_encode($paragraphBlocks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo $jsonOutput;
?>
  }
});

</script>

<script>
        // Obtener referencia al botón
        const boton = document.querySelector('button[aria-label="Save article"]')
        const btnChoose = document.querySelector('#select-subject button')
        const title = document.getElementById('title');

        title.oninput = function() {
            this.style.minHeight = 'auto'
            this.style.minHeight = `${this.scrollHeight}px`  
        }

        // Agregar event listener para manejar el clic en el botón
        boton.addEventListener('click', function () {
            // Guardar los datos del editor y mostrarlos
            editor.save().then((outputData) => {
                mostrarDatos(outputData);
            }).catch((error) => {
                console.log("Error al guardar los datos del editor", error);
            });
        });

        // Función para mostrar los datos en el contenedor
        async function mostrarDatos(data) {
            if (!btnChoose.value) {
                alert('Escoge una materia')
                return btnChoose.focus()
            }
            let tituloPost = 0;
            let paragrafoPost = 0;
            let photoPost = 0;
            let tituloGet;
            let materiaSeleccionada = btnChoose.value;
    tituloGet = title.value;
    if (tituloPost === 0) {
        tituloPost = tituloGet;
    }
    let contenido = "";
    const dataContainer = document.getElementById('data-container');
    dataContainer.innerHTML = '';

    for (const block of data.blocks) {
        if (block.type === "paragraph") {
            contenido += `<p class="space">${block.data.text}</p>`;
            if (paragrafoPost === 0) {
                paragrafoPost = block.data.text;
            }
        } else if (block.type === "image") {
            contenido += `<img class="space" src="${block.data.file.url}" alt="${block.data.caption}">`;           
            if (photoPost === 0) {
                photoPost = block.data.file.url;
            }
        } else if (block.type === "header") {
            if (block.data.level === 2) {
                contenido += `<h2 class="space">${block.data.text}</h2>`;
            } else if (block.data.level === 3) {
                contenido += `<h3 class="space">${block.data.text}</h3>`;
            } else if (block.data.level === 4) {
                contenido += `<h4 class="space">${block.data.text}</h4>`;
            }
        } else if (block.type === "list") {
            if (block.data.style === "unordered") {
                contenido += `<ul class="space">`;
                for (const i of block.data.items) {
                    contenido += `<li>${i}</li>`;
                }
                contenido += `</ul>`;
            } else if (block.data.style === "ordered") {
                contenido += `<ol class="space">`;
                for (const i of block.data.items) {
                    contenido += `<li>${i}</li>`;
                }
                contenido += `</ol>`;
            }
        } else if (block.type === "quote") {
            contenido += `<p class="space">“${block.data.text}” ${block.data.caption}</p>`;
        } else if (block.type === "attaches") {
            contenido += `<embed class="space" src="${block.data.file.url}" width="100%" height="200px" />`;

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

// Objeto con los datos a enviar
var datos = {
    titulo: tituloPost,
    contenido: paragrafoPost,
    image: photoPost,
    all: contenido,
    materia: materiaSeleccionada
};

// Hacer la solicitud AJAX
$.ajax({
    type: "POST",
    url: "../model/subir_articulo.php", // Ruta al archivo PHP en tu servidor
    data: datos,
    success: function(response) {
        // Manejar la respuesta del servidor si es necesario
        console.log("Post guardado correctamente");
        window.location.href = "../index.php";
    },
    error: function(xhr, status, error) {
        // Manejar errores si la solicitud falla
        console.error("Error al guardar el post:", error);
    }
});


}
    </script>

</body>
</html>