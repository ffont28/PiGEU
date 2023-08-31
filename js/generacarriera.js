document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('daricercare');
    const tableSection = document.getElementById('tabellautenti');
    const popup = document.getElementById('popup');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const popupText = document.getElementById('popup-text');
    let utenteSelected = ''; // Variabile per memorizzare l'utente selezionato

    function updateUtentiTrovati() {
        var search = searchInput.value;
        var xhr = new XMLHttpRequest();
        console.log(">" + search);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    tableSection.innerHTML = xhr.responseText;
                } else {
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };

        xhr.open("POST", "tabellastudentipercarriera.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'search=' + encodeURIComponent(search);
        xhr.send(params);
    }

    searchInput.addEventListener('input', function() {
        updateUtentiTrovati();
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('button-verb') || event.target.classList.contains('button-iscr')) {
            const utente = event.target.getAttribute('utente');
            const tipo = event.target.getAttribute('tipo');
            utenteSelected = utente; // Memorizza l'utente selezionato

            // Esegui l'azione associata al pulsante (es. generare carriera completa o valida)
            const actionType = event.target.classList.contains('button-verb') ? 'carriera_completa' : 'carriera_valida';

            console.log('chiamo la performAction');

            performAction(actionType, utente, tipo);
        }
    });

    function performAction(actionType, utente, tipo) {
        // Esegui l'azione tramite la chiamata AJAX
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // Aggiorna la tabella con i risultati aggiornati
                tableSection.innerHTML = xhr.responseText;
            }
        };

        xhr.open('POST', 'generacarrierarichiesta.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'action=' + encodeURIComponent(actionType) +
            '&utente=' + encodeURIComponent(utente) +
            '&tipo=' + encodeURIComponent(tipo);
        xhr.send(params);
    }

    updateUtentiTrovati();
});
