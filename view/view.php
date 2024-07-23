<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/style-view.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="shortcut icon" href="https://res.cloudinary.com/dvdhtdzwp/image/upload/v1721276122/logoico.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <script src="../src/js/script.js" defer></script>
    <?php
        require_once "../controller/config.php";

        session_start();
        $usuario = isset($_SESSION['username']) ? $_SESSION['username'] : "none";

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

    // Verificar si se ha proporcionado un ID en la URL
    if (isset($_GET['id'])) {
        $id_post = intval($_GET['id']);

        require_once "../controller/config.php";

        $sql = "SELECT posts.title, posts.authorid, contentpost.content, contentpost.materia, usuarios.name, usuarios.profileimage
                FROM posts
                INNER JOIN usuarios ON usuarios.id = posts.authorid
                INNER JOIN contentpost ON contentpost.post_id = posts.id
                WHERE posts.id = $id_post";

        $result = $link->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            echo "<title>" . $row['title'] . "</title>"; 
        } else {
            echo "<title>E Corp</title>";
        }
    }
    ?>
</head>
<body>
    <main>
        <header>
            <nav>
                <img src="https://res.cloudinary.com/dvdhtdzwp/image/upload/c_crop,g_auto,h_800,w_800/logo.jpg" alt="icon page">
                <div class="div-search">
                    <span class="material-symbols-rounded">search</span>
                    <input id="search" type="text" placeholder="Search">
                </div>
            </nav>
        </header>

        <nav class="nav-tools">
            <a href="/" aria-label="Go home">
                <span aria-hidden="true" class="material-symbols-rounded">home</span>
            </a>
            <a href="/view/article.php" aria-label="Go articles" class="selected">
                <span aria-hidden="true" class="material-symbols-rounded">list_alt_add</span>
            </a>
        <?php if ($_SESSION['usuario_id'] != "none") :?>
            <button aria-label="More">
                <span aria-hidden="true" class="material-symbols-rounded">add</span>
            </button>
            <button aria-label="Notifications">
                <span aria-hidden="true" class="material-symbols-rounded">notifications</span>
            </button>
            <a href="/view/profile.php?id=<?= $_SESSION['usuario_id'] ?>" aria-label="Go profile">
                <img src="../<?= $_SESSION['image'] ?>" alt="Profile photo">
            </a>
        <?php else : ?>
            <a href="/view/login.php" aria-label="Go profile">
                <span class="material-symbols-rounded">account_circle</span>
            </a>
        <?php endif ?>
        </nav>

        <div class="container">
        <?php if (isset($row)) :?>
            <article>
                <section class="head-content">
                    <h1><?= $row['title'] ?></h1>
                    <div class="head-author">
                        <img src="../<?= $row['profileimage'] ?>" alt="Imagen del autor">
                        <div>
                            <a href="profile.php?id=<?= $row['authorid'] ?>"><?= $row['name'] ?></a>
                            <span><?= $row['materia'] ?></span>
                        </div>
                    </div>
                </section>    
                <section class="article-content">
                    <?= $row['content'] ?>
                </section>
            </article>
        <?php elseif (isset($result)) :?>
            <h1>No se encontró el post asociado a esta id<h1>
        <?php else :?>
            <h1>No se proporcionó un id en la URL<h1>
        <?php endif ?>
        </div>
    </main>
<script>
    const USER_IS_MOD = Boolean(<?= $_SESSION['sudo'] ?>);
    const USER_ID = "<?= $_SESSION['usuario_id'] ?>";
    const USER_NAME = "<?= isset($_SESSION['name']) ? $_SESSION['name'] : 'none' ?>";
    const USER_IMG = "<?= isset($_SESSION['image']) ? '../' . $_SESSION['image'] : 0 ?>";
</script>
</body>
</html>