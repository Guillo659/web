let notifications = [];
let id_interval = null;
async function getNotifications() {
    await fetch('/controller/notificationController.php?action=get_all', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(async response => {
        await response.json().then(data => {
            console.log(data);
        });
    });
}

async function mostrarNotificacionesSinLeer() {
    await fetch('/controller/notificationController.php?action=get_all_unread', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(async response => {
        await response.json().then(data => {
            const section = document.querySelector('section.posts');
            if (section) {
                if (data.data.length > 0) {
                    section.innerHTML = '';
                    data.data.forEach(async notificacion => {
                        let rutaImg = 'null';
                        if (notificacion.usuario_imagen != null) {
                            rutaImg = notificacion.usuario_imagen.startsWith('..') ? notificacion.usuario_imagen.substring(2) : notificacion.usuario_imagen;
                        }
                        const existImgProfile = await checkFileExists(rutaImg);
                        let datePost = new Date(notificacion.post_date_created);
                        section.innerHTML += `
                    <article class="post" data-notificacion-id="${notificacion.report_id}">
                        <section class="head-user" data-user-id="${notificacion.usuario_id}">
                            <img src="${existImgProfile ? rutaImg:'/public/images/pred.jpeg'}" alt="Profile image">
                            <div class="user-date">
                                <a class="${classRol[notificacion.usuario_prole]}" role href="/view/profile.php?id=${notificacion.usuario_id}">${notificacion.usuario_name}</a>
                                <span title="${datePost.toLocaleDateString('en-us',optionsDateDay)}, ${datePost.toLocaleDateString('en-us',optionsDateHours)}">${datePost.toLocaleDateString('en-us',optionsDate)}</span>
                            </div>
                        </section>
                        <section class="post-content art-text">
                            <a role="h1" href="/view/view.php?id=${notificacion.post_id}">
                                ${notificacion.post_title}
                            </a>
                            <a role="p" href="/view/view.php?id=${notificacion.post_id}">
                                ${notificacion.post_content}
                            </a>
                        </section>
                        <div class="art-ctg">
                            <span class="${classMateria[notificacion.post_materia]}">${notificacion.post_materia}</span>
                        </div>
                        <div class="btn-actions">
                            <button class="review" title="Ver reportes" type="button" onclick="buscarReportesByPosts(${notificacion.post_id},${notificacion.notificacion_id});">
                                Review
                            </button>
                        </div>
                    </article>`;
                    });
                } else {
                    if (!data.error) {
                        section.innerHTML = '<h2>No hay notificaciones sin leer.</h2>';
                    }
                }
            }
        }).catch(error => {
            alert('Error convirtiendo los datos');
            console.log(error);
        });
    }).catch(error => {
        alert('Error obteniendo las notificaciones sin leer');
        console.log(error);
    });
}