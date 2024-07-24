let modal_report = HTMLElement;
let resetContentModal = false;

/**
 * 
 * @param {string} clase Nombre de la clase a quitar o agregar al modal
 * @param {boolean} resetContentModal Reinicia el contenido del modal solo cuando se está viendo el reporte de una publicación
 */
function toggleClassModal(clase = 'visible') {
    if (modal_report) {
        //Cuando resetContentModal es true, es porque se estaba viendo los reportes de una publicación
        if (resetContentModal) {
            modal_report.innerHTML = `
            <article class="popup">
                <section class="popup-bar-top">
                    <h1 class="title">Reportar publicación</h1>
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
            //Actualizamos la lista de publicaciones sin leer
            mostrarNotificacionesSinLeer();
        }
        const btn_send = modal_report.querySelector('button[type=submit]');
        if (btn_send) {
            btn_send.disabled = true;
        }
        const text = modal_report.querySelector('#form-report>textarea#reason');

        if (text) {
            text.oninput = () => {
                btn_send.disabled = text.value.trim().length <= 0;
            }
        }
        modal_report.classList.toggle(clase);
        if (!modal_report.classList.contains('visible')) {
            id_post_report = null;
            resetContentModal = false;
        }
        if (btn_send) {
            btn_send.addEventListener('click', crearReporte, false);
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
                <h1 class="title">Reportar publicación</h1>
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