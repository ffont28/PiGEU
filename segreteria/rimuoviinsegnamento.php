<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <title>Rimuovi insegnamento · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>RIMOZIONE INSEGNAMENTO</h1>

<div>
    <div>
        <label for="exampleFormControlInput1" class="form-label">Ricerca un insegnamento da rimuovere:</label>
    </div>
    <label for="cdl" >Utente:</label>
    <input type="insertText" id="daricercare" placeholder="🔍 RICERCA per NOME, CODICE o RESPONSABILE..." name="utente">

</div>
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>⚠️ ATTENZIONE</h2>
        <p id="popup-text"></p>
        <button id="confirmBtn" class="btn-confirm rounded-pill">Conferma</button>
        <button id="cancelBtn" class="btn-cancel rounded-pill">Annulla</button>
    </div>
</div>

<div id="tabellautenti">

</div>


<script>
    function updateCorsiTrovati() {
        console.log("richiesta funzione123"); //////////
        var sezioneHtml = document.getElementById("tabellautenti");
        var search = document.getElementById("daricercare").value ;
        console.log("RICERCA SU >>"+search);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                console.log("qui nell'XMLHTTP...."); ////////////////////////////////////////////////////////////////////////////////////////
                if (xhr.status === 200) {
                    console.log("CONNESSO OK"); /////////////////////////////////////////////////////////////////////
                    // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                    sezioneHtml.innerHTML = xhr.responseText;
                } else {
                    // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };

        // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
        xhr.open("POST", "tabellainsegnamentiperrimozione.php", true);
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

            xhttp2.open('POST', 'eliminadefinitivamenteinsegnamento.php', true);
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
            const insegnamento = event.target.getAttribute('ins');
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

                xhttp2.open('POST', 'eliminadefinitivamenteinsegnamento.php', true);
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


    updateCorsiTrovati();
</script>



</body>

</html>