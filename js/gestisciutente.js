
function calcolaCampi(){
  //  alert("in esecuzione calcolaCampi");
    var nome = document.getElementById('hnome').value;
    var cognome = document.getElementById('hcognome').value;
    var cf = document.getElementById('hcodicefiscale').value;
    var indirizzo = document.getElementById('hindirizzo').value;
    var citta = document.getElementById('hcitta').value;
    var email = document.getElementById('hemailpersonale').value;

  //  document.getElementById('nome').placeholder = nome;
  //  document.cookie = "nome = " + nome;
    
    document.getElementById('cognome').placeholder = cognome;
    document.cookie = "cognome = " + cognome;
    
    document.getElementById('codicefiscale').placeholder = cf;
    document.cookie = "codicefiscale = " + "cf";
    
    document.getElementById('indirizzo').placeholder = indirizzo;
        document.cookie = "indirizzo = " + indirizzo;
        
    document.getElementById('citta').placeholder = citta;
            document.cookie = "citta = " + citta;
    
    document.getElementById('emailpersonale').placeholder = email;
            document.cookie = "emailpersonale = " + email;


}

/**********************************************************************/

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
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    tableSection.innerHTML = xhr.responseText;
                } else {
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };

        xhr.open("POST", "tabellautentidagestire.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'search=' + encodeURIComponent(search);
        xhr.send(params);
    }

    searchInput.addEventListener('input', function() {
        updateUtentiTrovati();
    });

// parte di gestione dei bottoni in "AZIONI"
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('button-verb')) {
            const utente = event.target.getAttribute('utente');
            utenteSelected = utente; // Memorizza l'utente selezionato
            console.log("UTENTE: "+utente);
            // Esegui l'azione associata al pulsante (es. generare carriera completa o valida)
            const actionType = 'gestisci';

   //         console.log('chiamo la performAction');

            performAction(utente);
        }
    });

    function performAction(utente) {
        // Esegui l'azione tramite la chiamata AJAX
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // Aggiorna la tabella con i risultati aggiornati
                tableSection.innerHTML = xhr.responseText;
            }
        };

        xhr.open('POST', 'gestioneutenterichiesto.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        // const params = 'action=' + encodeURIComponent(actionType) + '&utente=' + encodeURIComponent(utente);
        const params = 'utente=' + encodeURIComponent(utente);
        // console.log(params);
        xhr.send(params);
    }

    updateUtentiTrovati();

// gestione del bottone di spostamento in storico
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('button-info')) {
        const utente = event.target.getAttribute('utente');
        utenteToDelete = utente; // Memorizza l'utente da eliminare

        // Aggiorna il testo del popup in base ai dati
        popupText.textContent = `Sei sicuro di voler spostare in storico lo studente "${utente}"?`;

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

    xhttp2.open('POST', 'spostautenteinstorico.php', true);
    xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'utente=' + encodeURIComponent(utenteToDelete);
    xhttp2.send(params);
});

cancelBtn.addEventListener('click', function() {
    // Nascondi il popup con animazione
    popup.classList.remove('active');
});

});