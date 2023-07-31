if (window.XMLHttpRequest) {
    console.log("uaooo");
     // Supports Ajax.

    } else {
    console.log("buuuuuhhh");

     //No.
}


//tipoDocente.style.display = "none";

function computeEmailDomain(){
//var tipoDocente = document.getElementById('tipodocente');
var tipo = document.getElementById('tipo').value;
var dominio = document.getElementById('dominio');
let tipodocente = document.getElementById("tipodocente");
let tiposegreteria = document.getElementById("tiposegreteria");
let hidden = tipodocente.getAttribute("hidden");

    document.cookie = "dominio = " + "@studenti.unimi.it";


    if (tipo == "studente"){
        console.log("è studente");
        dominio.innerText = "@studenti.unimi.it";
        document.cookie = "dominio = " + "@studenti.unimi.it";

        tipodocente.setAttribute("hidden", "hidden");
        tiposegreteria.setAttribute("hidden", "hidden");
    }

    if (tipo == "docente"){
        console.log("è docente");
        dominio.innerText = "@unimi.it";
        document.cookie = "dominio = " + "@unimi.it";

        tipodocente.removeAttribute("hidden");
        tiposegreteria.setAttribute("hidden", "hidden");
    }

    if (tipo == "segreteria"){
        console.log("è segreteria");
        dominio.innerText = "@unimi.it";
        document.cookie = "dominio = " + "@unimi.it";

        tiposegreteria.removeAttribute("hidden");
        tipodocente.setAttribute("hidden", "hidden");
    }

}

function computeEmailUser(){
    var nome = document.getElementById('nome').value;
    var cognome = document.getElementById('cognome').value;

    var dominio = document.getElementById('username');



    provvisorio = nome+ "." + cognome;
    console.log(cognome);
    console.log(nome);
    console.log(provvisorio);
    document.getElementById('username').placeholder = provvisorio;
    document.getElementById('username').innerText = provvisorio;
    document.cookie = "username = " + provvisorio;

}

function aggiungiCdL(){
    document.cookie = document.cookie + 1 + "@studenti.unimi.it"
}

function valutaAnno(){
    var tipo = document.getElementById('cdl').value;
    let tipoinsegnamento = document.getElementById("cdl");
    let anni = tipoinsegnamento.getAttribute("title");
 //   console.log(anni);
 //   console.log(tipoinsegnamento);
}

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