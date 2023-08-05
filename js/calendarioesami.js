const deleteButtons = document.querySelectorAll('.button-canc');
deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const cod = this.getAttribute('data-cod');
        const data = this.getAttribute('data-dat');
        const ora = this.getAttribute('data-ora');
        const formData = new FormData();
        formData.append('data-cod', cod);
        formData.append('data-dat', data);
        formData.append('data-ora', ora);

        fetch('cancella_esame.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Gestisci la risposta dal server (data)
                // Ad esempio, aggiorna la tabella o mostra un messaggio di successo/errore
                console.log(data);
            })
            .catch(error => {
                // Gestisci eventuali errori di rete o del server
                console.error('Errore nella richiesta AJAX:', error);
            });
    });
});