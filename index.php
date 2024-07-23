<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Xd -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" href="public/images/logo.webp">
    <link rel="stylesheet" href="src/css/style-home.css">
    <link rel="stylesheet" href="src/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="src/js/script.js" defer></script>
</head>

<body>
    <main>
        <header>
            <nav>
                <img src="public/images/logo.webp" alt="icon page">
                <div class="div-search flex-column">
                    <div class="criterio">
                        <span class="material-symbols-rounded">search</span>
                        <input id="search" type="text" placeholder="Search">
                    </div>
                    <div id="answer">
                        <div class="container-answer"></div>
                    </div>
                </div>
            </nav>
        </header>
        <?php

        use function PHPSTORM_META\type;

        require_once "controller/config.php";
        session_start();
        /*
    Sanitizacion XSS-DOM-Based...

    STDOUT con UTF-8: htmlentities();
        $row['titulo'] = '<script>alert("El niño atacó esta web");</script>';
     ->   htmlentities($row['titulo']);  <-
        Resultado: &lt;script&gt;alert(&quot;El ni&ntilde;o atac&oacute; esta web&quot;);&lt;/script&gt;

    STDINT con UTF-8: strip_tag();
        $content = '<script>window.location = "http://www.google.com";</script>';
     ->   strip_tag($content);  <-
        Resultado que se almacenaría: alert("El niño atacó esta web");     
*/
        $sudo = false;
        $role = "";

        if (isset($_SESSION['username'])) {
            $usuario = $_SESSION['username'];
            $sql = "SELECT id, name, role FROM usuarios WHERE username = '$usuario'";

            $result = mysqli_query($link, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['name'] = $row['name'];
                $_SESSION['usuario_id'] = $row['id'];
                if ($row['role']) {
                    $sudo = true;
                }
            } else {
                $_SESSION['usuario_id'] = "none";
                $_SESSION['name'] = 'none';
            }

            $_SESSION['sudo'] = $sudo;
            $_SESSION['role'] = $role;
            mysqli_free_result($result);
        } elseif (!isset($_SESSION['username'])) {
            $usuario = "none";
            $_SESSION['usuario_id'] = "none";
            $_SESSION['username'] = "none";
        }
        $_SESSION['usuario'] = $usuario;
        $_SESSION['sudo'] = $sudo;

        $nombre_usuario = isset($_SESSION['username']) ? $_SESSION['username'] : null;
        function obtener_ruta_imagen_perfil($username, $link) {

            $sql = "SELECT profileimage FROM usuarios WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $username);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $profile_image);
                    if (mysqli_stmt_fetch($stmt)) {
                        mysqli_stmt_close($stmt);
                        $_SESSION['image'] = file_exists($profile_image) ? $profile_image : "/public/images/pred.jpeg";
                        return file_exists($profile_image) ? $profile_image : "/public/images/pred.jpeg";
                    } else {
                        $profile_image = "/public/images/pred.jpeg";
                        return $profile_image;
                    }
                }
            }
        }

        function obtenerConteoLikes($post_id) {
            global $link; // Hacer referencia a la conexión a la base de datos

            $sql = "SELECT likes, comments FROM posts WHERE id = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $post_id);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $likes, $comments); // Definir dos variables para almacenar los resultados
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);

                    $conteo = [
                        'likes' => $likes,
                        'comments' => $comments
                    ];
                    return $conteo;
                }
            }
            return [
                'likes' => 0,
                'comments' => 0
            ];
        }

        $ruta_imagen_perfil = obtener_ruta_imagen_perfil($nombre_usuario, $link); // Implementa esta función según tu lógica de base de datos

        $sql = "SELECT posts.id, posts.title, posts.content, posts.imagen, posts.materia, posts.publictype, usuarios.id as user_id, usuarios.username, usuarios.name, usuarios.profileimage, usuarios.prole, posts.date_created FROM posts INNER JOIN usuarios ON posts.authorid = usuarios.id where posts.visibility = 1 ORDER BY posts.date_created DESC;";
        $result = $link->query($sql);

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $mobile_agents = array('iPhone', 'iPad', 'Android', 'webOS', 'BlackBerry', 'Windows Phone');

        $is_mobile = false;

        foreach ($mobile_agents as $agent) {
            if (stripos($user_agent, $agent) !== false) {
                $is_mobile = true;
                break;
            }
        }

        ?>
        <nav class="nav-tools">
            <a href="/" aria-label="Go home" class="selected">
                <span aria-hidden="true" class="material-symbols-rounded">home</span>
            </a>
            <a href="/view/article.php" aria-label="Go articles">
                <span aria-hidden="true" class="material-symbols-rounded">list_alt_add</span>
            </a>
            <?php if ($usuario != "none") : ?>
                <button aria-label="More">
                    <span aria-hidden="true" class="material-symbols-rounded">add</span>
                </button>
                <button aria-label="Notifications" onclick="mostrarNotificacionesSinLeer();">
                    <span aria-hidden="true" class="material-symbols-rounded">notifications</span>
                </button>
                <a href="/view/profile.php?id=<?= $_SESSION['usuario_id'] ?>" aria-label="Go profile">
                    <img src="<?= $_SESSION['image'] ?>" alt="Profile photo">
                </a>
            <?php else : ?>
                <a href="/view/login.php" aria-label="Go login">
                    <span class="material-symbols-rounded">account_circle</span>
                </a>
            <?php endif ?>
        </nav>

        <div class="container" id="container">
            <section class="estudios">
                <nav class="nav-categ">
                    <?php

                    $sql = "SELECT materia, COUNT(materia) AS cantidad FROM posts WHERE visibility = 1 GROUP BY materia ORDER BY cantidad DESC";

                    $resultado = $link->query($sql);

                    if ($resultado->num_rows > 0) {
                        $counter = 0;
                    ?>
                        <a href="#" data-materia="todas" class="selected">Para ti</a>
                    <?php
                        while ($fila = $resultado->fetch_assoc()) {
                            if ($fila['materia']) {
                                // echo '<a href="#">'.$fila['materia'].'</a>';  <- Enlace viejo
                                echo '<a href="#" data-materia="' . htmlspecialchars($fila['materia']) . '">' . htmlspecialchars($fila['materia']) . '</a>';
                            }

                            if ($is_mobile) {
                                $counter += 1;
                                if ($counter >= 3) {
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                </nav>
                <div id="cantainer_articles">
                    <?php
                    if ($result->num_rows > 0) {
                        $contador = 0;
                        while ($row = $result->fetch_assoc()) {
                            $fecha_creacion = date('Y-m-d', strtotime($row['date_created']));

                            $fecha_actual = date('Y-m-d');

                            $subfechapost = date('d', strtotime($row['date_created']));
                            $subfechaact = date('d');
                            $subfechatotal = $subfechaact - $subfechapost;

                            $formato_fecha = 'g:i A';
                            $day = "";

                            if ($fecha_actual === $fecha_creacion) {
                                $day = "Hoy ";
                            } else if ($subfechatotal === 1) {
                                $day = "Ayer ";
                            } else if ($subfechatotal === 2) {
                                $day = "Antier ";
                            } else {
                                $formato_fecha = 'M d, Y';
                            }
                            if ($row["publictype"] === 's') {
                                $sub = substr($row["content"], 0, 342);

                                $classRole = match ($row["prole"]) {
                                    '0' => 'Hacker',
                                    '1' => 'Student',
                                    '2' => 'Teacher',
                                    '3' => 'Developer',
                                    '4' => 'Graduate',
                                    '5' => 'Directive',
                                    default => false,
                                };
                    ?>
                                <article class="article" data-article-id="<?= $row["id"] ?>">
                                    <section class="art-info">
                                        <div class="head-user" data-user-id="<?= $row["user_id"] ?>">
                                            <img src="<?= file_exists($row["profileimage"]) ? $row["profileimage"] : "/public/images/pred.jpeg"; ?>" alt="Profile image">
                                            <div class="user-date">
                                                <a <?php if ($classRole) echo "class='$classRole'" ?> role="h2" href="view/profile.php?id=<?= $row["user_id"] ?>"><?= $row["name"] ?></a>
                                                <span title="<?= date(" D · g:i A ", strtotime($row['date_created'])) ?>"><?= $day, date($formato_fecha, strtotime($row['date_created'])); ?></span>
                                            </div>
                                            <button class="btn-more" aria-label="Options" data-article-id="<?= $row['id']; ?>">
                                                <span aria-hidden="true" class="material-symbols-rounded">more_horiz</span>
                                            </button>
                                        </div>
                                        <div class="art-text">
                                            <a role="h1" href="view/view.php?id=<?= $row["id"] ?>">
                                                <?= $row["title"] ?>
                                            </a>
                                            <a role="p" tabindex="-1" href="view/view.php?id=<?= $row["id"] ?>">
                                                <?= $sub ?>
                                            </a>
                                        </div>
                                        <div class="art-ctg">
                                            <?php

                                            $categoryClass = match ($row['materia']) {
                                                'Filosofía' => 'green',
                                                'Sociales' => 'red',
                                                'Matemáticas' => 'blue',
                                                'Inglés' => 'skyblue',
                                                'Tecnología' => 'purple',
                                                'Castellano' => 'yellow',
                                                'Química' => 'green',
                                                'Física' => 'rose',
                                                default => 'gray'
                                            };

                                            echo '<span class="' . $categoryClass . '">' . $row["materia"] . '</span>';
                                            ?>
                                        </div>
                                    </section>
                                    <?php
                                    $tmp_art_id = $row['id'];
                                    if ($row["imagen"] != 0 && $row['imagen'] != "public/0") : ?>
                                        <?php $ruta = str_replace('..', '', $row['imagen']); ?>
                                        <?php if (file_exists($ruta)) { ?>
                                            <img class="art-img" src="<?= $ruta ?>" alt="Post image">
                                        <?php } else { ?>
                                            <img class="art-img" title="No se encontró el archivo" src="/public/images/img-load-failed.png" alt="Post image">
                                        <?php } ?>
                                    <?php endif ?>
                                </article>
                    <?php
                            }
                        }
                    } else {
                        echo "Posts not found.";
                    }
                    ?>
                </div>
            </section>
            <section></section>
            <section class="posts">
                <?php
                $result->data_seek(0);

                while ($row = $result->fetch_assoc()) {
                    $fecha_creacion = date('Y-m-d', strtotime($row['date_created']));

                    $fecha_actual = date('Y-m-d');

                    $subfechapost = date('d', strtotime($row['date_created']));
                    $subfechaact = date('d');
                    $subfechatotal = $subfechaact - $subfechapost;

                    $formato_fecha = 'g:i A';
                    $day = "";

                    if ($fecha_actual === $fecha_creacion) {
                        $day = "Hoy ";
                    } else if ($subfechatotal === 1) {
                        $day = "Ayer ";
                    } else if ($subfechatotal === 2) {
                        $day = "Antier ";
                    } else {
                        //Only date, no time
                        $formato_fecha = 'M d, Y';
                    }
                    if ($row["publictype"] === 'p') {

                        $sql = 'select * from likes where post_id = ? and username = ?';
                        $stmt_tmp = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt_tmp, "is", $row['id'], $_SESSION['username']);
                        mysqli_stmt_execute($stmt_tmp);
                        $result_tmp = mysqli_stmt_get_result($stmt_tmp);
                        $hasLike = (mysqli_num_rows($result_tmp) > 0) ? true : false;
                        mysqli_stmt_close($stmt_tmp);
                ?>
                        <article class="post" data-post-id="<?= $row['id'] ?>">
                            <section class="head-user" data-user-id="<?= $row["user_id"] ?>">
                                <img src="<?= file_exists($row["profileimage"]) ? $row["profileimage"] : "/public/images/pred.jpeg"; ?>" alt="Profile image">
                                <div class="user-date">
                                    <a href="view/profile.php?id=<?= $row["user_id"] ?>">
                                        <?= $row["name"] ?>
                                    </a>
                                    <span title="<?= date('D · g:i A', strtotime($row['date_created'])) ?>"><?= $day, date($formato_fecha, strtotime($row['date_created'])) ?></span>
                                </div>
                                <button class="btn-more" aria-label="Options" data-post-id="<?= $row['id']; ?>">
                                    <span aria-hidden="true" class="material-symbols-rounded">more_horiz</span>
                                </button>
                            </section>
                            <section class="post-content">
                                <p class="post-text">
                                    <?= $row["content"] ?>
                                </p>
                                <?php if (($row['imagen']) != '') : ?>
                                    <img class="post-img" src="<?= $row['imagen'] ?>" alt="Post image">
                                <?php endif ?>
                            </section>
                            <section class="post-rate">
                                <div class="post-rate-info">
                                    <button disabled class="post-like-counter"><?= obtenerConteoLikes($row['id'])['likes'] ?></button>
                                    <button aria-label="Show comments" class="post-comment-counter"><?= obtenerConteoLikes($row['id'])['comments'] ?></button>
                                </div>
                                <div class="post-rate-buttons">
                                    <button <?php if ($hasLike) echo 'class="selected"' ?> aria-label="Like">
                                        <span aria-hidden="true" class="material-symbols-rounded">thumb_up</span>
                                    </button>
                                    <button aria-label="Add comment">
                                        <span aria-hidden="true" class="material-symbols-rounded">notes</span>
                                    </button>
                                </div>
                                <?php if ($_SESSION['usuario_id'] != "none") : ?>
                                    <div class="post-add-comment">
                                        <form>
                                            <input autocomplete="off" disabled type="text" placeholder="Escribe un comentario" class="input-comment"></input>
                                            <button disabled type="button" aria-label="Send comment" class="btn-send">
                                                <span aria-hidden="true" class="material-symbols-rounded">send</span>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif ?>
                            </section>
                        </article>
                <?php
                    }
                }
                ?>
            </section>
            <?php

            // Liberar memoria y cerrar la conexión
            $result->free();
            $link->close();
            ?>
    </main>
    <script>
        let isSubmitting = false

        function likePost(postId, likeCounter, btn) {
            if (USER_ID === 'none') return
            btn.disabled = true
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const json = JSON.parse(this.response)
                    const likeCount = json.likes
                    likeCounter.innerText = likeCount
                    if (json.isAdded) {
                        btn.classList.add('selected')
                    } else {
                        btn.classList.remove('selected')
                    }
                }
                btn.disabled = false
            }
            xhttp.open("GET", "model/like_post.php?post_id=" + postId, true)
            xhttp.send()
        }

        function sendComment(post, postId) {
            if (isSubmitting) return

            const input = post.querySelector('.input-comment')

            if (input.value.trim() === '') {
                return input.focus()
            }

            isSubmitting = true

            const btn = post.querySelector('.btn-send')
            const icon = btn.querySelector('span')

            input.disabled = true
            btn.disabled = true
            icon.innerText = 'progress_activity'
            icon.classList.add('rotating')

            const xhttp = new XMLHttpRequest()

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        const response = JSON.parse(this.responseText)
                        if (response.status === 'success') {
                            if (post.matches('.post')) {
                                post.querySelector('button[aria-label="Add comment"]').click()
                            } else {
                                post = document.querySelector(`.post[data-post-id="${postId}"]`)
                            }

                            const commentElement = document.createElement('article')

                            commentElement.classList.add('comment')

                            commentElement.innerHTML = `
                            <img src="${USER_IMG}" alt="Profile image">
                            <div class="comment-content">
                                <div class="comment-author">
                                    <a href="view/profile.php?id=${USER_ID}">${USER_NAME}</a>
                                    <span> · Now</span>
                                </div>
                                <p>${input.value}</p>
                            </div>
                        `

                            const noCommentsMsg = cache.modal.comments.querySelector('.no-comments')
                            if (noCommentsMsg) cache.modal.comments.removeChild(noCommentsMsg)
                            cache.modal.comments.appendChild(commentElement)
                            commentElement.scrollIntoView()

                            input.value = ''
                            post.querySelector('.post-comment-counter').innerText = response.count
                        } else {
                            alert('Error al enviar el comentario.')
                        }
                    } else {
                        console.error('Error en la solicitud: ' + this.status)
                    }
                    btn.disabled = false
                    input.disabled = false
                    icon.innerHTML = 'send'
                    icon.classList.remove('rotating')
                }
            }

            xhttp.open("POST", "model/add_comment.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("post_id=" + postId + "&comment=" + encodeURIComponent(input.value));

            isSubmitting = false
        }

        function getComments(postId) {
            let xhr = new XMLHttpRequest()

            xhr.open('GET', 'model/get_comments.php?post_id=' + postId)

            const commentsSection = cache.modal.comments
            const iconLoad = modal.querySelector('.icon-load')
            commentsSection.innerHTML = ''

            toggleClass(iconLoad, 'visible')

            xhr.onload = () => {
                if (xhr.status === 200) {
                    let comments = JSON.parse(xhr.response)
                    let lastComment = undefined

                    toggleClass(iconLoad, 'visible')

                    comments.forEach(comment => {
                        let lastDay = undefined
                        const dateNow = new Date()
                        const dayNow = dateNow.getDate()
                        const monthNow = dateNow.getMonth() + 1
                        const yearNow = dateNow.getFullYear()
                        const dateComment = new Date(comment.date_created_comment)
                        const day = dateComment.getDate()
                        const month = dateComment.getMonth() + 1
                        const year = dateComment.getFullYear()
                        let hours = dateComment.getHours()
                        let min = dateComment.getMinutes()
                        if (min < 10) min = '0' + min.toString()

                        let ampm = 'AM'

                        if (hours === 0) hours = 12
                        else if (hours > 12) {
                            hours -= 12;
                            ampm = 'PM'
                        }

                        if (monthNow === month && yearNow === year) {
                            if (dayNow === day) lastDay = 'Hoy'
                            else if (dayNow - 1 === day) lastDay = 'Ayer'
                            else if (dayNow - 2 === day) lastDay = 'Antier'
                            fecha = `${lastDay} ${hours}:${min} ${ampm}`
                        }

                        if (!lastDay) {
                            const monthName = dateComment.toLocaleDateString('en-US', {
                                month: 'short'
                            })
                            fecha = `${monthName} ${day}, ${year}`
                        }

                        const weekDay = dateComment.toLocaleDateString('en-US', {
                            weekday: 'short'
                        })

                        title = `${weekDay} · ${hours}:${min} ${ampm}`

                        const commentElement = document.createElement('article')
                        commentElement.classList.add('comment')
                        commentElement.innerHTML = `
                        <img src="${comment.profileimage}" alt="Profile image">
                        <div class="comment-content">
                            <div class="comment-author">
                                <a href="view/profile.php?id=${comment.authorid}">${comment.authorname}</a>
                                <span title="${title}"> · ${fecha}</span>
                            </div>
                            <p>${comment.content}</p>
                        </div>
                    `
                        lastComment = commentElement
                        commentsSection.appendChild(commentElement)
                    })
                    if (!lastComment) {
                        const p = document.createElement('p')
                        p.className = 'no-comments'
                        p.innerText = 'Aún no hay comentarios'
                        commentsSection.appendChild(p)
                        lastComment = p
                    }
                    lastComment.scrollIntoView()
                } else {
                    console.error('Error en la solicitud: ' + xhr.status)
                }
            }
            xhr.send()
        }

        const USER_IS_MOD = Boolean(<?= $sudo ? 1 : 0 ?>);
        const USER_ID = "<?= $_SESSION['usuario_id'] ?>";
        const USER_NAME = "<?= isset($_SESSION['name']) ? $_SESSION['name'] : 'none' ?>";
        const USER_IMG = "<?= isset($_SESSION['image']) ? $_SESSION['image'] : 0 ?>";
    </script>
    <script src="src/js/reportes.js"></script>
    <script src="src/js/modales.js"></script>
    <script src="src/js/notificacion.js"></script>
    <script src="src/js/post.js"></script>
</body>

</html>