<?php
session_start();

// Incluir el archivo de configuración
require_once('../controller/config.php');

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

// Obtener el ID del usuario cuyo perfil se está visitando a través de GET
if (isset($_GET['id']) && $_GET['id']) {
    $perfil_usuario_id = $_GET['id'];

    $sql = "SELECT * FROM usuarios WHERE id = ?";

    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $perfil_usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $edit_profile = ($_SESSION['usuario'] == $usuario['username']) ? true : false;
    } else {
        // echo "No se encontraron datos de usuario.";
    }

    $resultado->close();
} else {
    // echo "No se proporcionó un ID de usuario.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../public/images/logo.webp" type="image/x-icon">
    <link rel="stylesheet" href="../src/css/style-profile.css">
    <link rel="stylesheet" href="../src/css/style-home.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../src/js/script.js" defer></script>
    <title><?= isset($usuario['name']) ? $usuario['name'] : 'Labs' ?></title>
</head>
<body>
    <main>
        <header>
            <nav>
                <img src="../public/images/logo.webp" alt="icon page">
                <div class="div-search">
                    <span aria-hidden="true" class="material-symbols-rounded">search</span>
                    <input id="search" type="text" placeholder="Search">
                </div>
            </nav>
        </header>

        <nav class="nav-tools">
            <a href="/" aria-label="Go home">
                <span aria-hidden="true" class="material-symbols-rounded">home</span>
            </a>
            <a href="/view/article.php" aria-label="Go articles">
                <span aria-hidden="true" class="material-symbols-rounded">list_alt_add</span>
            </a>
        <?php if ($usuario != "none") :?>
            <button aria-label="More">
                <span aria-hidden="true" class="material-symbols-rounded">add</span>
            </button>
            <button aria-label="Notifications">
                <span aria-hidden="true" class="material-symbols-rounded">notifications</span>
            </button>
            <a href="/view/profile.php?id=<?= $_SESSION['usuario_id'] ?>" aria-label="Go profile" class="selected">
                <img src="../<?=$_SESSION['image'] ?>" alt="Profile photo">
            </a>
        <?php else : ?>
            <a href="/view/login.php" aria-label="Go login" class="selected">
                <span class="material-symbols-rounded">account_circle</span>
            </a>
        <?php endif ?>
        </nav>

        <div class="container">
            <section class="estudios">
            <?php
                $sql_art = 'SELECT posts.id, posts.title, posts.content, posts.imagen, posts.materia, usuarios.id as user_id, usuarios.name, usuarios.prole, usuarios.profileimage, posts.date_created FROM posts INNER JOIN usuarios ON posts.authorid = usuarios.id where posts.visibility = 1 and posts.publictype = "s" and usuarios.id = ? ORDER BY posts.date_created DESC; ';
                if ($stmt_art = mysqli_prepare($link, $sql_art)) {
                    mysqli_stmt_bind_param($stmt_art, 'i', $perfil_usuario_id);
                    if (mysqli_stmt_execute($stmt_art)) {
                        $result_art = mysqli_stmt_get_result($stmt_art);

                        while ($row = mysqli_fetch_assoc($result_art)) {
                            $fecha_creacion = date('Y-m-d', strtotime($row['date_created']));

                            $fecha_actual = date('Y-m-d');

                            $subfechapost = date('d', strtotime($row['date_created']));
                            $subfechaact = date('d');
                            $subfechatotal = $subfechaact - $subfechapost;
                            $day = "";

                            if ($fecha_actual === $fecha_creacion) {
                                $formato_fecha = 'g:i A';
                                $day = "Hoy ";
                            } else if ($subfechatotal === 1){
                                $day = "Ayer ";
                                $formato_fecha = 'g:i A';
                            } else if ($subfechatotal === 2) {
                                $day = "Antier ";
                                $formato_fecha = 'g:i A';
                            } else {
                                //Only date, no time
                                $formato_fecha = 'M d, Y'; 
                            }

                            $sub = substr($row["content"], 0, 342);

                            $classRole = match ($row["prole"]) {
                                1 => 'Student',
                                2 => 'Teacher',
                                3 => 'Developer',
                                4 => 'Graduate',
                                5 => 'Directive',
                                default => 0,
                            };
                            ?>
                            <article class="article" data-article-id="<?= $row["id"] ?>">
                                <section class="art-info">
                                    <div class="head-user" data-user-id=<?= $row["user_id"] ?>>
                                        <img src="../<?= $row["profileimage"] ?>" alt="Profile image">
                                        <div class="user-date">
                                            <a <?php if ($classRole != 0) echo "class='$classRole'" ?> role="h2" href="profile.php?id=<?= $row["user_id"] ?>"><?= $row["name"] ?></a>
                                            <span title="<?= date("D • g:i A", strtotime($row['date_created']))?>"><?= $day, date($formato_fecha, strtotime($row['date_created'])); ?></span>
                                        </div>
                                        <button class="btn-more" aria-label="Options">
                                            <span aria-hidden="true" class="material-symbols-rounded">more_horiz</span>
                                        </button>
                                    </div>
                                    <div class="art-text">
                                        <a role="h1" href="view.php?id=<?= $row["id"] ?>"><?= $row["title"] ?></a>
                                        <a role="p" tabindex="-1" href="view.php?id=<?= $row["id"] ?>"><?= $sub ?></a>
                                    </div>
                                    <div class="art-ctg">
                                        <?php 
                                        $categoryClass = match ($row['materia']) {
                                            'Filosofia' => 'green',
                                            'Sociales' => 'red',
                                            'Matematicas' => 'blue',
                                            'Tecnologia' => 'purple',
                                            'Castellano' => 'yellow',
                                            'Fisica' => 'rose',
                                            'Quimica' => 'green',
                                            default => 'gray'
                                        };
                                        ?>
                                        <span class="<?= $categoryClass ?>"><?= $row["materia"] ?></span>
                                    </div>
                                </section>
                                <?php
                                 if ($row["imagen"] != 0 && $row['imagen'] != "public/0") : ?>
                                    <img class="art-img" src="<?= $row['imagen'] ?>" alt="Post image">
                                <?php endif ?>
                            </article> 
                            <?php
                        }
                    }
                }
            ?>
            </section>
            <section></section>
            <section  id="profile-info">
                <?php if (isset($usuario)) : ?>
                <img id="user-photo" src="../<?= $usuario['profileimage'] ?>" alt="Profile image">
                <h2 id="user-name"><?= $usuario['name'] ?></h2>
                <p id="user-description"><?= $usuario['bio'] ? $usuario['bio'] : "Living.."; ?></p>
                <?php if ($edit_profile) : ?>
                    <button id="btn-edit" aria-label="btn-edit-profile">
                        Editar perfil
                        <span aria-hidden="true" class="material-symbols-rounded">edit</span>
                    </button>
                <?php endif; else : ?>
                <p>No se encontraron datos de usuario.</p>
                <?php endif ?>
            </section>
        </div>
    </main>
<script>
    const USER_IS_MOD = Boolean(<?= $_SESSION['sudo'] ?>);
    const USER_ID = "<?= $_SESSION['usuario_id'] ?>";
    const USER_NAME = "<?= isset($_SESSION['name']) ? $_SESSION['name'] : 'none' ?>";
    const USER_IMG = "<?= isset($_SESSION['image']) ? "../" . $_SESSION['image'] : 0 ?>";
</script>
</body>
</html>
