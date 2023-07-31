<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
<!-- import di Bootstrap-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


<script src="../js/segreteria.js"></script>
<link rel="stylesheet" href="../css/cssSegreteria.css">
<link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Inserimento nuovo insegnamento</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR  -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/segreteria/aggiungiutente.php">Inserisci Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="segreteria/aggiungiinsegnamento.php">Inserisci Insegnamento</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="aggiungicdl.php">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>
  <!-- FINE NAVBAR -->
  INSERIMENTO DI UN NUOVO INSEGNAMENTO

<form method="post" >
    <div class="center">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome dell'insegnamento</label>
          <input type="text" class="form-control"  id="nome" placeholder="inserisci il Nome del CdL" name="nome">
        </div>

        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">CODICE INSEGNAMENTO</label>
          <input type="text" class="form-control" id="codice" placeholder="inserisci il codice del CdL" name="codice">
        </div>

    <label for="exampleFormControlInput1" class="form-label">Corso di Laurea a cui appartiene questo insegnamento:</label>


       <select class="form-select" id="cdl" name="cdl">
       <!--<option selected value="ciao">Open this select menu</option>-->
       <?php
            include('../conf.php');

        try {
            // Connessione al database utilizzando PDO
            $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query con CTE
            $query = " SELECT c.nome, c.codice, c.tipo FROM corso_di_laurea c";

            // Esecuzione della query e recupero dei risultati
            $stmt = $conn->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);




            // Elaborazione dei risultati
           foreach ($results as $row) {
                // Utilizza $row per accedere ai dati dei singoli record
                echo "<option value=\"".$row['codice']."\">".$row['nome']."</option> ";
            }
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
       ?>
        </select>


<label for="exampleFormControlInput1" class="form-label">Anno dell'insegnamento:</label>
        <select class="form-select" aria-label="Default select example" id="anno" name="anno">

        </select>

    <script>
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
                xhr.open("GET", "query.php?value=" + selezionecdl, true);
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


<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){

    include('../functions.php');
    $db = open_pg_connection();

    // definisco le variabili
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $codice = $_POST['codice'];
    $docenteresponsabile = $_POST['docenteresponsabile'];

    // inserimento del corso in corso_di_laurea
    $params = array ($codice, $nome ,$tipo);
    $sql = "INSERT INTO corso_di_laurea VALUES ($1,$2,$3)";
    $result = pg_prepare($db,'insCdL',$sql);
    $result = pg_execute($db,'insCdL', $params);

    //inserimento del docente responsabile
    $params = array ($codice, $docenteresponsabile);
    $sql = "INSERT INTO credenziali VALUES ($1,$2)";
    $result = pg_prepare($db,'insResp',$sql);
    $result = pg_execute($db,'insResp', $params);

}
 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>