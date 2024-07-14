let id_post_report = null;
let modal_report = HTMLElement;


/**
 * 
 * @param {string} clase Nombre de la clase a quitar o agregar al modal
 */
function toggleClassModal(clase = 'visible') {
    if (modal_report) {
        const btn_send = modal_report.querySelector('button[type=submit]');
        btn_send.disabled = true;
        const text = modal_report.querySelector('#form-report>textarea#reason');

        if (text) {
            text.oninput = () => {
                btn_send.disabled = text.value.trim().length <= 0;
            }
        }
        modal_report.classList.toggle(clase);
        if (!modal_report.classList.contains('visible')) {
            id_post_report = null;
        }
        btn_send.addEventListener('click', crearReporte, false);
    }
}

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


window.onload = () => {
    //Creamos la estructura del modal para crear reportes
    modal_report = document.createElement('section');
    modal_report.id = 'modal_report';
    modal_report.classList.add('modal', 'fade');
    modal_report.innerHTML = `
        <article class="popup">
            <section class="popup-bar-top">
                <h1>Reportar publicación</h1>
                <button id="btn_close_report" onclick="toggleClassModal('visible')" aria-label="Close">
                    <span aria-hidden="true" class="material-symbols-rounded">close</span>
                </button>
            </section>
            <section class="popup-content">
                <div class="popup-form">
                    <form method="POST" id="form-report">
                        <textarea name="reason" id="reason" placeholder="¿Por qué reportas este post?" rows="7"></textarea>
                    </form>
                </div>
            </section>
            <section class="popup-bar-bottom">
                <div class="btn-actions popup-form-buttons">
                    <button type="submit" aria-label="Reportar" class="btn-send">
                        <span aria-hidden="true" class="material-symbols-rounded">send</span>
                    </button>
                </div>
            </section>
        </article>`;

    //Agregamos el modal al body
    document.body.prepend(modal_report);

}