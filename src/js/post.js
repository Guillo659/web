async function setVisibilityPost(post_id = null, visibility = 0) {
    /* 0 - invisible
     * 1 - visible
     */
    const visibilityCode = [0, 1];
    if (visibilityCode.includes(visibility) && post_id != null) {
        const data = new FormData();
        data.append('post_id', parseInt(post_id));
        data.append('visibility', visibility);
        await fetch('/controller/postController.php?action=set_post_visibility', {
            method: 'POST',
            body: data
        }).then(async response => {
            await response.json().then(data => {
                alert(data.message);
                if (!data.error) {
                    window.location.reload();
                }
            }).catch(error => {
                console.log('Error obteniendo respuesta', error);
            });
        }).catch(error => {
            console.log('Error en la petición', error);
        });
    } else {
        alert('Error faltan datos o son incorrectos.');
    }
}