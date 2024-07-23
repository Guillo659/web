let id_post_report = null;

const classRol = {
    '0': 'Hacker',
    '1': 'Student',
    '2': 'Teacher',
    '3': 'Developer',
    '4': 'Graduate',
    '5': 'Directive',
    'null': '',
    '': ''
};

const classMateria = {
    'Filosofia': 'green',
    'Sociales': 'red',
    'Matemáticas': 'blue',
    'Inglés': 'skyblue',
    'Tecnología': 'purple',
    'Castellano': 'yellow',
    'Quimica': 'green',
    'Fisica': 'rose',
    'null': 'gray',
    '': 'gray'
};

const optionsDate = {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
}

const optionsDateDay = {
    weekday: 'short',
};

const optionsDateHours = {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
};

async function crearReporte() {
    if (id_post_report != null) {
        //Obtenemos la información del formulario
        const reporte = new FormData(modal_report.querySelector('#form-report'));
        if (reporte.get('reason') && reporte.get('reason').trim().length > 0) {
            reporte.append('post_id', id_post_report);
            await fetch('/controller/reportController.php?action=reportar', {
                method: 'POST',
                body: reporte
            }).then(async response => {
                await response.json().then(data => {
                    if (!data.error) {
                        modal_report.querySelector('#form-report>textarea#reason').value = '';
                        id_post_report = null;
                        //Ocultamos el modal
                        toggleClassModal('visible');
                        //Si el post se ocultó, recargamos la página
                        if (data.is_add_blacklist) {
                            window.location.reload();
                        }
                    }
                    alert(data.message);
                }).catch(error => {
                    alert(error.message);
                });
            }).catch(error => {
                alert(error.message);
            });
        }
    }
}

async function buscarReportesByPosts(post = null, notificacion_id = null) {
    //Primero mandamos a marcar la notificacion como leída
    if (notificacion_id != null) {
        const dataNoti = new FormData();
        dataNoti.append('id', parseInt(notificacion_id));
        await fetch('/controller/notificationController.php?action=marcar_notification_readed', {
            method: 'POST',
            body: dataNoti
        }).then(async response => {
            await response.json().then(data => {
                console.log(data);
            }).catch(error => {
                console.log('Error enviando la petición');
            });
        }).catch(error => {
            console.log('Error enviando la petición');
        });
    }
    if (post != null) {
        const data = new FormData();
        data.append('post_id', parseInt(post));
        await fetch('/controller/reportController.php?action=obtener_reportes_post', {
            method: 'POST',
            body: data
        }).then(async response => {
            await response.json().then(data => {
                if (!data.error) {
                    //Obtener la visibilidad del post
                    let visible = 0;
                    if (data.data.length > 0) {
                        visible = data.data[0].post_visibility;
                    }
                    //Cambiamos el titulo del modal
                    modal_report.querySelector('h1.title').textContent = 'Reportes del post';
                    //Limpiamos el contenido del modal
                    modal_report.querySelector('.popup-content').innerHTML = '';
                    //Limpiamos las acciones del modal
                    modal_report.querySelector('.popup-bar-bottom').innerHTML = '';
                    //Agregamos los reportes al modal
                    data.data.forEach(async reporte => {
                        let rutaImg = 'null';
                        if (reporte.usuario_imagen != null) {
                            rutaImg = reporte.usuario_imagen.startsWith('..') ? reporte.usuario_imagen.substring(2) : reporte.usuario_imagen;
                        }
                        const existImgProfile = await checkFileExists(rutaImg);
                        let dateReport = new Date(reporte.report_date_created);
                        modal_report.querySelector('.popup-content').innerHTML += `
                            <article class="post" data-reporte-id="${reporte.report_id}">
                                <section class="head-user" data-user-id="${reporte.usuario_id}">
                                    <img src="${existImgProfile ? rutaImg:'/public/images/pred.jpeg'}" alt="Profile image">
                                    <div class="user-date">
                                        <a class="${classRol[reporte.usuario_prole]}" role href="/view/profile.php?id=${reporte.usuario_id}" target="_blank">${reporte.usuario_name}</a>
                                        <span title="${dateReport.toLocaleDateString('en-us',optionsDateDay)}, ${dateReport.toLocaleDateString('en-us',optionsDateHours)}">${dateReport.toLocaleDateString('en-us',optionsDate)}</span>
                                    </div>
                                </section>
                                <section class="post-content art-text text-complete">
                                    <a role="p" href="/view/view.php?id=${reporte.post_id}" title="Ver posts" target="_blank">
                                        <p>${reporte.reason}</p>
                                    </a>
                                </section>
                            </article>`;
                    });
                    modal_report.querySelector('.popup-bar-bottom').innerHTML = `
                    <div class="btn-actions popup-form-buttons justify-between">
                        <span class="color-text">Estado: ${visible==0 ? 'Oculto':'Visible'}</span>
                        <button type="button" aria-label="post-visible" onclick="setVisibilityPost(${post},0);" title="Hacer visible el post" class="btn btn-eye available">
                            <span aria-hidden="true" class="material-symbols-rounded">visibility</span>
                        </button>
                    </div>
                    `;
                    //Mostramos el modal
                    //Para reiniciar el contenido del modal cuándo se cierre
                    resetContentModal = true;
                    toggleClassModal('visible');
                }

            }).catch(error => {
                alert('Error convirtiendo los datos');
                console.log(error);
            });
        }).catch(error => {
            alert('Error buscando reportes del post');
            console.log('Error buscando reportes post', error);
        });
    }
}

async function checkFileExists(url = null) {

    if (url != null) {
        return await fetch(url, {
            method: 'HEAD',
            mode: 'no-cors'
        }).then(response => {
            return response.ok;
        }).catch(() => {
            return false;
        });
    }
    return false;
}