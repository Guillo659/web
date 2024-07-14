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

window.onload = () => {
    //Espero que se cargue la web e inicio el interval para traer las notificaciones cada 1 seg
    id_interval = setInterval(getNotifications, 1000);
}