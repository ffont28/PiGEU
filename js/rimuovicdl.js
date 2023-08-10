function updateCorsiTrovati() {

    var sezioneHtml = document.getElementById("tabellautenti");
    var search = document.getElementById("daricercare").value ;
    console.log("CHIAMATA AJAX con parametro :"+search);
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
           if (xhr.status === 200) {
                // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                sezioneHtml.innerHTML = xhr.responseText;
            } else {
                // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                console.error("Errore durante la richiesta AJAX");
            }
        }
    };

    // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
    xhr.open("POST", "../segreteria/AJAXtabellacdlperrimozione.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'search=' + encodeURIComponent(search);
    xhr.send(params);
}


//  Inizializza il contenuto del secondo menù a tendina inizialmente
updateCorsiTrovati();
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('daricercare');
    const popup = document.getElementById('popup');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const popupText = document.getElementById('popup-text');
    let codiceToDelete = '';

    // Funzione per mostrare il popup
    function showPopup(insegnamento, codice) {
        codiceToDelete = codice;
        popupText.textContent = `Confermi la rimozione definitiva per ${insegnamento}?`;
        popup.classList.add('active');
    }

    // Funzione per nascondere il popup
    function hidePopup() {
        popup.classList.remove('active');
    }

    // Listener per l'evento input sull'input di ricerca
    searchInput.addEventListener('input', function() {
        const searchValue = searchInput.value;
        // Esegui la chiamata AJAX con il valore di ricerca
        // e aggiorna la tabella con i risultati ottenuti
        updateCorsiTrovati(searchValue);
    });

    // Listener per il click sul pulsante di conferma nel popup
    confirmBtn.addEventListener('click', function() {
        // Esegui la cancellazione tramite la chiamata AJAX
        const xhttp2 = new XMLHttpRequest();
        xhttp2.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    // Richiama la funzione per aggiornare la tabella
                    updateCorsiTrovati(searchInput.value);
                }
            }
        };

        xhttp2.open('POST', 'AJAXeliminadefinitivamentecdl.php', true);
        xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'codice=' + encodeURIComponent(codiceToDelete);
        xhttp2.send(params);

        // Nascondi il popup
        hidePopup();
    });

    // Listener per il click sul pulsante di annullamento nel popup
    cancelBtn.addEventListener('click', function() {
        // Nascondi il popup
        hidePopup();
    });
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('button-canc')) {
        const codice = event.target.getAttribute('codice');
        const insegnamento = event.target.getAttribute('cdl');
        const popup = document.getElementById('popup');
        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const popupText = document.getElementById('popup-text');
        console.log(codice);
        // Aggiorna il testo del popup in base ai dati
        popupText.textContent = `Sei sicuro di voler cancellare l'insegnamento ${insegnamento}?`;

        // Mostra il popup con animazione
        popup.classList.add('active');

        // Gestisci la conferma
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
                        updateCorsiTrovati();
                    }
                }
            };

            xhttp2.open('POST', 'AJAXeliminadefinitivamentecdl.php', true);
            xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            const params = 'codice=' + encodeURIComponent(codice);
            xhttp2.send(params);
        });

        // Gestisci l'annullamento
        cancelBtn.addEventListener('click', function() {
            // Nascondi il popup con animazione
            popup.classList.remove('active');
        });
    }
});
