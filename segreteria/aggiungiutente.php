<?php session_start();?>

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

    <title>Inserimento nuovo utente</title>


  </head>
  <body>
 <!-- INIZIO NAVBAR -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="aggiungiutente.php">Aggiungi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="gestisciutente.php">Gestisci Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="rimuoviutente.php">Rimuovi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="aggiungiinsegnamento.php">Inserisci Insegnamento</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="aggiungicdl.php">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>


  <!-- FINE NAVBAR -->
  PAGINA DI NUOVA UTENZA

<form method="post" >
    <div class="center">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome</label>
          <input type="text" class="form-control" onchange="computeEmailUser()" id="nome" placeholder="inserisci il Nome dell'utente" name="nome">
        </div>

        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Cognome</label>
          <input type="text" class="form-control" onchange="computeEmailUser()" id="cognome" placeholder="inserisci il Cognome dell'utente" name="cognome">
        </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">CODICE FISCALE</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci il Codice Fiscale dell'utente" name="codicefiscale">
            </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">indirizzo</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci l'indirizzo dell'utente" name="indirizzo">
            </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">città</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci la città di residenza dell'utente" name="citta">
            </div>

        <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Seleziona un'utenza</label>
        <select class="form-select" onchange="computeEmailDomain()"  aria-label="Default select example" id="tipo" name="tipo">
      <!--    <option selected value="">seleziona un valore</option> -->
          <option value="studente">Studente</option>
          <option value="docente">Docente</option>
          <option value="segreteria">Segreteria</option>
        </select>
            <body onload="setup()">
        </div>

        <div id="cdl" class="mb-3">
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
        </div>

        <div id="tipodocente" hidden="hidden">
        Tipo di contratto:
        <select class="form-select" aria-label="Default select example" id="tipo" name="tipodocente">
                <!--  <option selected>Open this select menu</option> -->
                  <option value="a contratto">A contratto</option>
                  <option value="associato">Associato</option>
                  <option value="ordinario">Ordinario</option>
                  <option value="ricercatore">Ricercatore</option>

                </select>
        </div>

        <div id="tiposegreteria" hidden="hidden">
                Tipo di segreteria:
                <select class="form-select" aria-label="Default select example" id="tipo" name="tiposegreteria">
                        <!--  <option selected>Open this select menu</option> -->
                          <option value="didattica">didattica</option>
                          <option value="alunni">alunni</option>
                          <option value="personale">personale</option>
                        </select>
                </div>

          Indirizzo email istituzionale e username di Istituto
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Inserisci l'username" aria-label="Recipient's username" aria-describedby="basic-addon2" name="username" id ="username">
            <span class="input-group-text" id="dominio" name="dominio">@studenti.unimi.it</span>
            <input type="hidden" name="dominionascosto" id="dominionascosto">
          </div>

         Indirizzo email personale: è l'account di recupero e la prima password di default dell'utente
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Inserisci l'indirizzo email personale" aria-label="Recipient's username" aria-describedby="basic-addon2" name="emailpersonale" id ="username">
          </div>
  <input type="submit" class="button1 green" value="INSERISCI UTENTE" />
    </div>
</form>

<?php

 if($_SERVER['REQUEST_METHOD']=='POST'){  //if(isset($_POST)){
  //  echo "sono qui con dominio ".$_COOKIE['dominio']."  .....  ";
  //  echo $_POST['username'].$_POST['dominionascosto']." ".$_POST['nome']." ".$_POST['cognome']." ".$_POST['indirizzo']." ".$_POST['citta']." ".$_POST['codicefiscale']." ".$_POST['emailpersonale'];

    include('../functions.php');
    $db = open_pg_connection();
   // echo "sono qui";

    // definisco le variabili
    $utente = $_COOKIE['username'];
        if ($_POST['username'] != ""){ $utente = $_POST['username'];}
    $dominio = $_COOKIE['dominio'];
    $istitemail = $utente.$dominio;

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $indirizzo = $_POST['indirizzo'];
    $citta = $_POST['citta'];
    $cf = $_POST['codicefiscale'];
    $persemail = $_POST['emailpersonale'];
    $tipo = $_POST['tipo'];
    $tipodocente = $_POST['tipodocente'];
    $tiposegreteria = $_POST['tiposegreteria'];
    $cdl = $_POST['cdl'];
    ///////echo $_POST['tipo'];

    // inserimento generale a livello di utente sia che sia docente, studente o segreteria
    $params = array ($istitemail, $nome, $cognome, $indirizzo, $citta, $cf, $persemail);
    $sql = "INSERT INTO utente VALUES ($1,$2,$3,$4,$5,$6,$7)";
    $result = pg_prepare($db,'ins',$sql);
    $result = pg_execute($db,'ins', $params);

    //inserimento delle credenziali di primo accesso
    $params = array ($istitemail, md5($persemail));
    $sql = "INSERT INTO credenziali VALUES ($1,$2)";
    $result = pg_prepare($db,'inscred',$sql);
    $result = pg_execute($db,'inscred', $params);


    // casistica di inserimento
    $params = array ($istitemail,$cdl);
    // STUDENTE
    if ($tipo == "studente"){
    inserisciStudente($params);
    }

    // DOCENTE
    if ($tipo == "docente"){
    $params = array ($istitemail, $tipodocente);
    inserisciDocente($params);
    }

    // SEGRETERIA
    if ($tipo == "segreteria"){
    $params = array ($istitemail, $tiposegreteria);
    inserisciSegreteria($params);
    }
}
 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>