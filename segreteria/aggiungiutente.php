<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
      <?php importVari();?>
    <title>Inserimento nuovo utente</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR -->
  <?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
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
        <label for="exampleFormControlInput1" class="form-label">tipo di utente</label>
        <select class="form-select" onchange="computeEmailDomain()"  aria-label="Default select example" id="tipo" name="tipo">
      <!--    <option selected value="">seleziona un valore</option> -->
          <option value="studente">Studente</option>
          <option value="docente">Docente</option>
          <option value="segreteria">Segreteria</option>
        </select>
            <body onload="setup()">
        </div>

        <div id="cdl" class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Corso di Laurea a cui iscrivere lo studente:</label>
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
?>                      <option value="<?php echo $row['codice']?>"><?php echo $row['nome']?></option>
<?php               }
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
                  <option value="ordinario">Ordinario</option>
                  <option value="associato">Associato</option>
                  <option value="a contratto">A contratto</option>
                  <option value="ricercatore">Ricercatore</option>
                  <option value="ricercatore confermato">Ricercatore Confermato</option>
                  <option value="emerito">Emerito</option>
                  <option value="straordinario">Straordinario</option>

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

    $db = open_pg_connection();
   // echo "sono qui";

    // definisco le variabili
    $utente = $_COOKIE['username'];
        if ($_POST['username'] != ""){ $utente = $_POST['username'];}
    $dominio = $_COOKIE['dominio'];
    $istitemail = strtolower($utente.$dominio);

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

    pg_close($db);
}
 ?>

</body>
</html>