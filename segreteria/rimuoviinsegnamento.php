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
    <title>Rimuovi utente · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>RIMOZIONE UTENTE</h1>

<div>
    <label for="exampleFormControlInput1" class="form-label">Ricerca un utente da rimuovere:</label>
    <form id="ricercaInsegnamento" action="" method="POST">
        <label for="cdl" >Utente:</label>
        <input type="insertText" id="daricercare" placeholder="inserisci l'ID utente che si vuole rimuovere" name="utente">
        <button type="submit" class="button1 green" value="CARICA INFORMAZIONI" id="search" >🔍 RICERCA</button>
    </form>
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
    function updateUtentiTrovati() {
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
        xhr.open("POST", "tabellautenti.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'search=' + encodeURIComponent(search);
        xhr.send(params);
    }

    // Aggiungi un ascoltatore di eventi per il pulsante di ricerca
    document.getElementById("search").addEventListener('click', function(event) {
        event.preventDefault(); // Impedisce il comportamento predefinito del pulsante
        updateUtentiTrovati();
    });

    // Inizializza il contenuto del secondo menù a tendina inizialmente
    //updateUtentiTrovati();
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('button-canc')) {
            const utente = event.target.getAttribute('utente');
            const popup = document.getElementById('popup');
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const popupText = document.getElementById('popup-text');
            const nome = event.target.getAttribute('nome');
            const cognome = event.target.getAttribute('cognome');
            console.log(nome + " " + cognome);
            popupText.textContent = `confermi la rimozione definitiva per ${nome} ${cognome}?`;
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
                            updateUtentiTrovati();
                        }
                    }
                };

                xhttp2.open('POST', 'eliminadefinitivamenteutente.php', true);
                xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                const params = 'utente=' + encodeURIComponent(utente);
                xhttp2.send(params);
            });

            // Gestisci l'annullamento
            cancelBtn.addEventListener('click', function() {
                // Nascondi il popup con animazione
                popup.classList.remove('active');
            });
        }
    });


    // // Gestione evento di pressione del bottone
    // document.addEventListener('click', function(event) {
    //     if (event.target.classList.contains('button-canc')) {
    //         if (confirm('Sei sicuro?')) {
    //             const utente = event.target.getAttribute('utente');
    //
    //             // Seconda chiamata AJAX per cancellare la riga
    //             const xhttp2 = new XMLHttpRequest();
    //             xhttp2.onreadystatechange = function () {
    //                 if (this.readyState === 4 && this.status === 200) {
    //                     const response = JSON.parse(this.responseText);
    //                     if (response.success) {
    //                         // Richiama la funzione per aggiornare la tabella
    //                         updateUtentiTrovati();
    //                     }
    //                 }
    //             };
    //
    //             // Configura e invia la seconda chiamata AJAX per cancellare la riga
    //             xhttp2.open('POST', 'eliminadefinitivamenteutente.php', true);
    //             xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    //             const params = 'utente=' + encodeURIComponent(utente);
    //             xhttp2.send(params);
    //         }
    //     }
    // });

</script>



<script>
    //
    // function rimozioneUtente(utente) {
    //    const xhttp = new XMLHttpRequest();
    //
    //    xhttp.onreadystatechange = function() {
    //      if (this.readyState === 4) {
    //        if (this.status === 200) {
    //          const response = JSON.parse(this.responseText);
    //          console.log(response);
    //        if (response.success) {
    //            window.location.reload();
    //          }
    //        } else {
    //          console.error('Errore nella richiesta AJAX:', this.statusText);
    //         }
    //      }
    //    };
    //
    //    xhttp.open('POST', 'rimuoviinsdacdl.php', true);
    //    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    //    const params = 'utente=' + encodeURIComponent(utente);
    //    xhttp.send(params);
    //  }
    //
    //  const removeUserButtons = document.querySelectorAll('.button-canc');
    //  removeUserButtons.forEach(button => {
    //    button.addEventListener('click', function() {
    //      const utente = this.getAttribute('utente');
    //      rimozioneUtente(utente);
    //    });
    //  });
    //</script>



</body>

</html>


