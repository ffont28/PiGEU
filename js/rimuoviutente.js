document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('daricercare');
    const popup = document.getElementById('popup');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const popupText = document.getElementById('popup-text');
    let utenteToDelete = ''; // Variabile per memorizzare l'utente da eliminare

    function updateUtentiTrovati() {
        var sezioneHtml = document.getElementById("tabellautenti");
        var search = searchInput.value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    sezioneHtml.innerHTML = xhr.responseText;
                } else {
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };

        xhr.open("POST", "tabellautentiperrimozione.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'search=' + encodeURIComponent(search);
        xhr.send(params);
    }

    searchInput.addEventListener('input', function() {
        updateUtentiTrovati();
    });
    updateUtentiTrovati();

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('button-canc')) {
            const utente = event.target.getAttribute('utente');
            utenteToDelete = utente; // Memorizza l'utente da eliminare

            // Aggiorna il testo del popup in base ai dati
            popupText.textContent = `Sei sicuro di voler cancellare l'utente "${utente}"?`;

            // Mostra il popup con animazione
            popup.classList.add('active');
        }
    });

    confirmBtn.addEventListener('click', function() {
        // Nascondi il popup con animazione
        popup.classList.remove('active');

        // Esegui la cancellazione tramite la chiamata AJAX
        const xhttp2 = new XMLHttpRequest();
        xhttp2.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    // Richiama la funzione per aggiornare la tabella
                    updateUtentiTrovati();
                }
            }
        };

        xhttp2.open('POST', 'eliminadefinitivamenteutente.php', true);
        xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'utente=' + encodeURIComponent(utenteToDelete);
        xhttp2.send(params);
    });

    cancelBtn.addEventListener('click', function() {
        // Nascondi il popup con animazione
        popup.classList.remove('active');
    });
});