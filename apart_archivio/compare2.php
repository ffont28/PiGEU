<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <!-- Includi jQuery dalla rete tramite un link CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="/css/cssSegreteria.css">
    <link rel="stylesheet" href="/css/from-re.css">
    <link rel="stylesheet" href="/css/calendarioesami.css">
    <link rel="stylesheet" href="/css/general.css">
    <script src="../js/segreteria.js"></script>
    <script src="../js/general.js"></script>

    <meta charset="utf-8">      <title>Inserimento nuovo insegnamento</title>
</head>
<body>

<!-- INIZIO NAVBAR -->
<div class="container">
    <ul class="nav nav-tabs">

        <li class="nav-item">
            <a class="nav-link  " aria-current="page" href="main.php"><strong>🏠 HOME</strong></a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle " id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                <strong>🧑‍🔧 GESTIONE UTENZE</strong>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                <li><a class="dropdown-item " href="aggiungiutente.php">Inserimento Utente</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="gestisciutente.php">Modifica Utente</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="rimuoviutente.php">Rimuozione Utente</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle " id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                <strong>🧑‍🏫 GESTIONE INSEGNAMENTI</strong>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                <li><a class="dropdown-item " href="aggiungiinsegnamento.php">Inserimento Insegnamento</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="modificainsegnamento.php">Modifica Insegnamento</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="rimuoviinsegnamento.php">Rimozione Insegnamento</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle " id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                <strong>🎓 GESTIONE CORSI DI LAUREA</strong>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                <li><a class="dropdown-item " href="aggiungicdl.php">Inserimento Corso di Laurea</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="modificacdl.php">Modifica Corso di Laurea</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item " href="rimuovicdl.php">RimozioneCorso di Laurea</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled" aria-current="page" href="main.php">👤 Fontana  Francesco Stephan Maria</a>
        </li>
        <li class="nav-item dropdown">
            <div class="ml-auto logout-button">
                <a class="nav-link rounded-pill" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true"
                   href="logout.php" class="btn btn-danger"><strong>🚪 LOGOUT</strong></a>
            </div>
        </li>
    </ul>

</div>
<!-- FINE NAVBAR -->
INSERIMENTO DI UN NUOVO INSEGNAMENTO

<form method="post" action="#" >
    <div class="center">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Nome dell'insegnamento</label>
            <input type="text" class="form-control"  id="nome" placeholder="inserisci il Nome dell'insegnamento" name="nome">
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">CODICE INSEGNAMENTO</label>
            <input type="text" class="form-control" id="codice" placeholder="inserisci il codice dell'insegnamento" name="codice">
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">DOCENTE RESPONSABILE:</label>


            <select class="form-select" id="responsabile" name="responsabile">
                <!--<option selected value="ciao">Open this select menu</option>-->
                <option value="stefano.aguzzoli@unimi.it">Stefano Aguzzoli</option> <option value="paolo.boldi@unimi.it">Paolo Boldi</option> <option value="nunzioalberto.borghese@unimi.it">Nunzio Alberto Borghese</option> <option value="cecilia.cavaterra@unimi.it">Cecilia Cavaterra</option> <option value="dario.malchiodi@unimi.it">Dario Malchiodi</option> <option value="stefano.montanelli@unimi.it">Stefano Montanelli</option> <option value="paularne.oestvaer@unimi.it">Paul Arne Oestvaer</option> <option value="beatricesanta.palano@unimi.it">Beatrice Santa Palano</option> <option value="giovanni.pighizzini@unimi.it">Giovanni Pighizzini</option> <option value="vincenzo.piuri@unimi.it">Vincenzo Piuri</option> <option value="massimo.santini@unimi.it">Massimo Santini</option>                 </select>
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">descrizione dell'insegnamento</label>
            <input type="text" class="form-control" id="descrizione" placeholder="inserisci la descrizione dell'insegnamento" name="descrizione">
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">CFU previsti per l'insegnamento</label>
            <select class="form-select"  aria-label="Default select example" id="cfu" name="cfu">
                <!--  <option selected>Open this select menu</option> -->
                <option value="6">6</option>
                <option value="9">9</option>
                <option value="12">12</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Corso di Laurea a cui appartiene questo insegnamento:</label>


            <select class="form-select" id="cdl" name="cdl">
                <!--<option selected value="ciao">Open this select menu</option>-->
                <option value="F1X">informatica</option> <option value="F1XM">sicurezza dei sistemi informatici</option> <option value="MED">Medicina</option> <option value="PROVA">Corso Prova</option> <option value="P2">Prova2</option> <option value="G1">gabriele2020U2</option>         </select>
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Anno dell'insegnamento:</label>
            <select class="form-select" aria-label="Default select example" id="anno" name="anno">

            </select>
        </div>
        <script>
            function updateSecondMenutendina1() {
                console.log("richiesta funzione1"); ////////////////////////////////////////////////////////////////////////////
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
                            console.log("CONNESSO OK QUERY ANNO LAUREA"); /////////////////////////////////////////////////////////////////////
                            // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                            anno.innerHTML = xhr.responseText;
                        } else {
                            // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                            console.error("Errore durante la richiesta AJAX");
                        }
                    }
                };

                // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
                xhr.open("GET", "query.php?value=" + selezionecdl, true);
                xhr.send();
            }

            // Aggiungi un ascoltatore di eventi per il menù a tendina 1
            document.getElementById("cdl").addEventListener('change', updateSecondMenutendina1);

            // Inizializza il contenuto del secondo menù a tendina inizialmente
            updateSecondMenutendina1();
        </script>


        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Propedeuticità - per questa materia è necessario aver superato l'esame di:</label>
            <select class="form-select" aria-label="Default select example" id="prop" name="prop">

            </select>
        </div>

        <script>
            function updateSecondMenutendina() {
                console.log("richiesta funzione"); ////////////////////////////////////////////////////////////////////////////
                var cdl = document.getElementById("cdl");
                var prop = document.getElementById("prop");

                // Ottieni il valore selezionato nel primo menù a tendina
                var selezionecdl = cdl.value;

                // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        console.log("qui"); ////////////////////////////////////////////////////////////////////////////////////////
                        if (xhr.status === 200) {
                            console.log("CONNESSO OK PROP"); /////////////////////////////////////////////////////////////////////
                            // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                            prop.innerHTML = xhr.responseText;
                        } else {
                            // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                            console.error("Errore durante la richiesta AJAX");
                        }
                    }
                };

                // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
                xhr.open("GET", "propedeuticita.php?value=" + selezionecdl, true);
                xhr.send();
            }

            // Aggiungi un ascoltatore di eventi per il menù a tendina 1
            document.getElementById("cdl").addEventListener('change', updateSecondMenutendina);

            // Inizializza il contenuto del secondo menù a tendina inizialmente
            updateSecondMenutendina();
        </script>




        <input type="submit" class="button1 green" value="INSERISCI L'INSEGNAMENTO NEL CORSO DI LAUREA" />
    </div>
</form>




<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
</form>
</body>
</html>