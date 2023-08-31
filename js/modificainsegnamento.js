document.addEventListener('DOMContentLoaded', function() {
    let addDocIntoIns;
    function updateDocentiTrovati() {
        var sezioneHtml = document.getElementById("tabelladocenti");
        var search = document.getElementById("docdaricercare").value;
        var ci = document.getElementById("codice").value;
        console.log("RICERCA SU ><>>" + search);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                    sezioneHtml.innerHTML = xhr.responseText;
                    addDocIntoIns = document.querySelectorAll('.button-add-doc');
                    console.log(addDocIntoIns);
                    addDocIntoIns.forEach(button => {
                        button.addEventListener('click', function() {
                            const insegnamento = this.getAttribute('insegnamento');
                            const docente = this.getAttribute('docente');
                            // Effettua la richiesta AJAX
                            inserisciDocInIns(insegnamento, docente);
                        });
                    });
                } else {
                    // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };


        // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
        xhr.open("POST", "tabelladocentipermodificainsegnamento.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'search=' + encodeURIComponent(search)
                              +'&ci=' + encodeURIComponent(ci);
        xhr.send(params);

    }
    updateDocentiTrovati();
    addDocIntoIns = document.querySelectorAll('.button-add-doc');

    const searchInput = document.getElementById('docdaricercare');
    searchInput.addEventListener('input', function() {
        const searchValue = searchInput.value;
        updateDocentiTrovati(searchValue);
        addDocIntoIns = document.querySelectorAll('.button-add-doc');
    });

    // RIMUOVI IL DOCENTE DALLA TABELLA INSEGNA
    function cancellaDocDaIns(insegnamento, docente) {
        const xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    // Gestisci la risposta del server
                    const response = JSON.parse(this.responseText);
                    console.log(response);
                    if (response.success) {
                        window.location.reload();
                    }
                } else {
                    // Gestisci eventuali errori
                    console.error('Errore nella richiesta AJAX 338:', this.statusText);
                    window.location.reload();
                }
            }
        };

        xhttp.open('POST', 'rimuovidocdains.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'insegnamento=' + encodeURIComponent(insegnamento) + '&docente=' + encodeURIComponent(docente);
        xhttp.send(params);
    }

    // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
    const removeDocDaIns = document.querySelectorAll('.button-canc-doc');
    removeDocDaIns.forEach(button => {
        button.addEventListener('click', function() {
            const insegnamento = this.getAttribute('insegnamento');
            const docente = this.getAttribute('docente');

            // Effettua la richiesta AJAX
            cancellaDocDaIns(insegnamento, docente);
        });
    });

/// AGGIUNGI IL DOCENTE ALLA TABELLA INSEGNA

    function inserisciDocInIns(insegnamento, docente) {
        const xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    // Gestisci la risposta del server
                    const response = JSON.parse(this.responseText);
                    console.log(response);
                    if (response.success) {
                        window.location.reload();
                    }
                } else {
                    // Gestisci eventuali errori
                    console.error('Errore nella richiesta AJAX:', this.statusText);
                }
            }
        };

        xhttp.open('POST', 'aggiungidocains.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'insegnamento=' + encodeURIComponent(insegnamento) + '&docente=' + encodeURIComponent(docente);
        xhttp.send(params);
    }

    // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
    addDocIntoIns.forEach(button => {
        button.addEventListener('click', function() {
            const insegnamento = this.getAttribute('insegnamento');
            const docente = this.getAttribute('docente');
            // Effettua la richiesta AJAX
            inserisciDocInIns(insegnamento, docente);
        });
    });

    // Funzione per effettuare la richiesta AJAX
    function cancellaInsdaCdl(insegnamento, cdl) {
        const xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    // Gestisci la risposta del server
                    const response = JSON.parse(this.responseText);
                    console.log(response);
                    if (response.success) {
                        window.location.reload();
                    }
                } else {
                    // Gestisci eventuali errori
                    console.error('Errore nella richiesta AJAX:', this.statusText);
                }
            }
        };

        xhttp.open('POST', 'rimuoviinsdacdl.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'insegnamento=' + encodeURIComponent(insegnamento) + '&cdl=' + encodeURIComponent(cdl);
        xhttp.send(params);
    }

    // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
    const removeFromCdlButtons = document.querySelectorAll('.button-canc');
    removeFromCdlButtons.forEach(button => {
        button.addEventListener('click', function() {
            const insegnamento = this.getAttribute('insegnamento');
            const cdl = this.getAttribute('cdl');

            // Effettua la richiesta AJAX
            cancellaInsdaCdl(insegnamento, cdl);
        });
    });

    console.log(removeFromCdlButtons);
});