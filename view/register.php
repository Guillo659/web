<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="stylesheet" href="../src/css/style-auth.css">
    <link rel="shortcut icon" href="../public/images/logo.webp" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Sing Up</title>
<body>
    <main>
        <article>
            <?php if (isset($_GET['error']) && $_GET['error'] == 1) : ?>    
                <p style="color: #ff2755"><span class="material-symbols-rounded">error</span> Username in use</p>
            <?php elseif (isset($_GET['error']) && $_GET['error'] == 2) : ?>    
                <p style="color: #eee047"><span class="material-symbols-rounded">warning</span> Password required min 6 characters</p>
            <?php elseif (isset($_GET['error']) && $_GET['error'] == 3) : ?>   
                <p style="color: #eee047"><span class="material-symbols-rounded">warning</span> Password are to be equal</p>
            <?php endif ?>
            <h1>Labs Register</h1>
            <form action="../model/register.php" method="POST">
                <div class="inputs">
                    <input type="text" placeholder="Nombre de usuario" name="username" required>
                    <span aria-hidden="true" class="material-symbols-rounded">person</span>
                </div>
                <div class="inputs">
                    <input type="password" autocomplete="off" aria-label="input-password" placeholder="Contraseña" name="password" minlength="6" required>
                    <span aria-label="true" class="material-symbols-rounded">lock</span>
                    <span role="button" tabindex="0" class="material-symbols-rounded eye-pass">visibility_off</span>
                </div>
                <div class="inputs">
                    <input type="password" autocomplete="off" aria-label="input-password" placeholder="Confirmar contraseña" name="confirm_password" minlength="6" required>
                    <span aria-label="true" class="material-symbols-rounded">lock</span>
                    <span role="button" tabindex="0" class="material-symbols-rounded eye-pass">visibility_off</span>
                </div>
                <button type="submit">Registrar</button>
            </form>
            <a href="login.php">Iniciar sesión</a>
        </article>
    </main>
</body>
<script>
    document.querySelector('form').onmousedown = e => {
        showPass(e.target)
    }
    document.querySelector('form').onkeydown = e => {
        if (e.key === 'Enter') showPass(e.target)
    }
    function showPass(i) {
        if (i.matches('.eye-pass')) {
            const input = i.parentNode.querySelector('input')
            if (i.innerText != 'visibility') {
                i.innerText = 'visibility'
                input.setAttribute('type', 'text')
            } else {
                i.innerText = 'visibility_off'
                input.setAttribute('type', 'password')
            }
        }
    }
</script>
</html>
