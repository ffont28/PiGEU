// Funzione che viene chiamata quando cambia il valore del primo menù a tendina
        function updateSecondMenutendina() {
            var menutendina1 = document.getElementById("menutendina1");
            var menutendina2 = document.getElementById("menutendina2");

            // Ottieni il valore selezionato nel primo menù a tendina
            var selezioneMenutendina1 = menutendina1.value;
               console.log(selezioneMenutendina1);
            // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log("eccoci qui");
                        // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                        console.log(xhr.responseText);
                        menutendina2.innerHTML = xhr.responseText;
                    } else {
                        // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                        console.error("Errore durante la richiesta AJAX");
                    }
                }
            };

            // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
            xhr.open("GET", "query.php?value=" + selezioneMenutendina1, true);
            xhr.send();
        }

        // Aggiungi un ascoltatore di eventi per il menù a tendina 1
        document.getElementById("menutendina1").addEventListener("change", updateSecondMenutendina);

        // Inizializza il contenuto del secondo menù a tendina inizialmente
        updateSecondMenutendina();