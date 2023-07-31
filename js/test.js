console.log("ciaone");



function updateSecondMenutendina() {
            console.log("richiesta funzione"); ////////////////////////////////////////////////////////////////////////////
            var cdl = document.getElementById("cdl");
            var anno = document.getElementById("anno");

            // Ottieni il valore selezionato nel primo menù a tendina
            var selezionecdl = cdl.value;

            // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                console.log("qui"); ////////////////////////////////////////////////////////////////////////////////////////
                    if (xhr.status === 200) {
                           console.log("CONNESSO OK"); /////////////////////////////////////////////////////////////////////
                        // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                        anno.innerHTML = xhr.responseText;
                    } else {
                        // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                        console.error("Errore durante la richiesta AJAX");
                    }
                }
            };

            // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
            xhr.open("GET", "ajax.php?value=" + selezionecdl, true);
            xhr.send();
        }

        // Aggiungi un ascoltatore di eventi per il menù a tendina 1
        document.getElementById("cdl").addEventListener('change', updateSecondMenutendina);

        // Inizializza il contenuto del secondo menù a tendina inizialmente
        updateSecondMenutendina();