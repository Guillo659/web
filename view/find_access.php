<?php
session_start();
if ($_SESSION['username'] != "none") {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../src/css/style-auth.css">
    <link rel="stylesheet" href="../src/css/style.css">
    <link rel="shortcut icon" href="https://res.cloudinary.com/dvdhtdzwp/image/upload/v1721276122/logoico.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Find Access</title>
</head>

<body>
    <main>
        <article>
            <h1>Find Access</h1>
            <form id="form-find-access" method="POST">
                <div class="inputs">
                    <input type="text" placeholder="Nombre" name="name" required>
                    <span aria-hidden="true" class="material-symbols-rounded">person</span>
                </div>
                <button type="submit">Buscar</button>
            </form>
        </article>
        <article class="resultado" style="margin-top: 20px;"></article>
    </main>
    <script>
        async function findAccess(e) {
            e.preventDefault();
            const data = new FormData(document.getElementById('form-find-access'));
            if (data.get('name') && data.get('name').length > 0) {
                await fetch('/controller/usuarioController.php?action=find_access', {
                    method: 'POST',
                    body: data,
                }).then(async response => {
                    await response.json().then(data => {
                        if (!data.error) {
                            if (data.data.length > 0) {
                                const resultado = document.querySelector('.resultado');
                                resultado.innerHTML = '';
                                resultado.innerHTML = '<h3>Credenciales de acceso</h3>';
                                data.data.forEach(user => {
                                    resultado.innerHTML += `
                                        <form>
                                            <div class="inputs">
                                                <input type="text" autocomplete="off" placeholder="Usuario" value="${user.username}" readonly required>
                                                <span aria-hidden="true" class="material-symbols-rounded">person</span>
                                            </div>
                                            <div class="inputs">
                                                <input type="text" autocomplete="off" value="${user.password}" aria-label="input-password" placeholder="Contraseña" readonly required>
                                                <span aria-label="true" class="material-symbols-rounded">lock</span>
                                            </div>
                                        </form>
                                    `
                                });
                            } else {
                                alert('No se encontró ningún usuario con el nombre ingresado');
                            }
                        } else {
                            alert(`Sucedió un error en la consulta: ${data.message}`);
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error al obtener los datos del usuario');
                    });
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al buscar el usuario');
                });
            } else {
                alert('Debes ingresar un nombre');
            }
        }
        window.onload = () => {
            document.querySelector('#form-find-access').addEventListener('submit', findAccess, false);
        }
    </script>
</body>

</html>