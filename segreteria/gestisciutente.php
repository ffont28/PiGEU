<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
<!-- import di Bootstrap-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


<script src="../js/gestisciutente.js"></script>
<script src="../js/segreteria.js"></script>
<link rel="stylesheet" href="../css/cssSegreteria.css">
<link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Gestione utente</title>

  </head>
  <body>
  <!-- INIZIO NAVBAR  -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="/segreteria/aggiungiutente.php">Aggiungi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>
  <!-- FINE NAVBAR -->
  <h1>PAGINA DI GESTIONE UTENZA</h1>

<!-- RICERCA DI UN UTENTE PER MODIFICARLO -->
<form method="GET" >
    <div class="center">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">RICERCA UTENTE DA MODIFICARE</label>
          <input type="text" class="form-control" id="search" placeholder="inserisci l'inditizzo email dell'utente da modificare" name="search">
        </div>
<input type="submit" class="button1 green" value="RICERCA UTENTE" />
</form>

<?php
     include('../conf.php');
     include('../functions.php');
     $ricercato="";
     $upd = false;

echo '<script>console.log("sono qui")</script>'; //////////////////////////////////////////////////////////////
 if($_SERVER['REQUEST_METHOD']=='GET'){
        echo '<script>console.log("sono qui dentro al $server")</script>'; //////////////////////////////////////////////////////////////



    if (!empty($_GET["search"])){
    $ricercato = $_GET['search'];



    try {
        // Connessione al database utilizzando PDO
        echo '<script>console.log("sono qui nel try")</script>'; //////////////////////////////////////////////////////////////

        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);

        // Query
        $query = "SELECT * FROM utente where email = :value";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':value', $ricercato, PDO::PARAM_STR);
               // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo '<script>console.log("sono qui prima dell\'echo")</script>'; //////////////////////////////////////////////////////////////

        echo "<form method=\"POST\" > <div class=\"center bblue\">";

           foreach ($results as $row) {

                 echo "<div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Nome</label>
                      <input hidden type=\"text\" value=\"".$row['email']."\" class=\"form-control\" id=\"hricercato\" name=\"hricercato\">
                      <input hidden type=\"text\" value=\"".$row['nome']."\" class=\"form-control\" id=\"hnome\" name=\"hnome\">
                      <input type=\"text\" class=\"form-control\" id=\"nome\" name=\"nome\">
                        </div>
                       <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Cognme</label>
                     <input hidden type=\"text\" value=\"".$row['cognome']."\" class=\"form-control\" id=\"hcognome\" name=\"hcognome\">
                     <input type=\"text\"  class=\"form-control\" id=\"cognome\" name=\"cognome\">
                       </div>
                       <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">CODICE FISCALE</label>
                     <input hidden type=\"text\" value=\"".$row['codicefiscale']."\" class=\"form-control\" id=\"hcodicefiscale\" name=\"hcodicefiscale\">
                     <input type=\"text\" class=\"form-control\" id=\"codicefiscale\" name=\"codicefiscale\">
                       </div>
                     <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">indirizzo</label>
                      <input hidden type=\"text\" value=\"".$row['indirizzo']."\" class=\"form-control\" id=\"hindirizzo\" name=\"hindirizzo\">
                      <input type=\"text\" class=\"form-control\" id=\"indirizzo\" name=\"indirizzo\">
                        </div>
                     <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">città</label>
                     <input hidden type=\"text\" value=\"".$row['citta']."\" class=\"form-control\" id=\"hcitta\" name=\"hcitta\">
                     <input type=\"text\" class=\"form-control\" id=\"citta\" name=\"citta\">
                       </div>


                     <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">email personale</label>
                     <input hidden type=\"text\" value=\"".$row['emailpersonale']."\" class=\"form-control\" id=\"hemailpersonale\" name=\"hemailpersonale\">
                     <input type=\"text\" class=\"form-control\" id=\"emailpersonale\" name=\"emailpersonale\">
                       </div>
                      <input type=\"submit\" class=\"button1 orange\" value=\"MODIFICA UTENTE\" />
                        </div>
                    </form>
                    <body onload='calcolaCampi()'>

                    ";

                $upd = true;
               //  echo "<option value=\"".$row['email']."\">".$row['nome']." ".$row['cognome']."</option> ";
                 }
       } catch (PDOException $e) {
           echo "Errore: " . $e->getMessage();
       }
   }

 }

?>


<!--
        Seleziona un'utenza
        <select class="form-select" onchange="computeEmailDomain()"  aria-label="Default select example" id="tipo" name="tipo">
        <!--  <option selected>Open this select menu</option>
          <option value="studente">Studente</option>
          <option value="docente">Docente</option>
          <option value="segreteria">Segreteria</option>
        </select>

        <div id="tipodocente" hidden="hidden">
        Tipo di contratto:
        <select class="form-select" aria-label="Default select example" id="tipo" name="tipodocente">
                <!--  <option selected>Open this select menu</option>
                  <option value="a contratto">A contratto</option>
                  <option value="ricercatore">Ricercatore</option>
                  <option value="associato">Associato</option>
                </select>
        </div>

        <div id="tiposegreteria" hidden="hidden">
                Tipo di segreteria:
                <select class="form-select" aria-label="Default select example" id="tipo" name="tiposegreteria">
                        <!--  <option selected>Open this select menu</option>
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
-->

<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){


        $db = open_pg_connection();
        echo "sono qui";
        // definisco le variabili
        $targ = $_POST['hricercato'];
        $nome = $_COOKIE['nome'];
                if ($_POST['nome'] != ""){ $nome = $_POST['nome'];}
        $cognome = $_COOKIE['cognome'];
                if ($_POST['cognome'] != ""){ $cognome = $_POST['cognome'];}
        $indirizzo= $_COOKIE['indirizzo'];
                if ($_POST['indirizzo'] != ""){ $indirizzo = $_POST['indirizzo'];}
        $citta= $_COOKIE['citta'];
                if ($_POST['citta'] != ""){ $citta = $_POST['citta'];}        
        $cf= $_COOKIE['codicefiscale'];
                if ($_POST['codicefiscale'] != ""){ $cf = $_POST['codicefiscale'];}
        $persemail= $_COOKIE['emailpersonale'];
                if ($_POST['emailpersonale'] != ""){ $persemail = $_POST['emailpersonale'];}
      //  $tipo = $_POST['tipo'];
      //  $tipodocente = $_POST['tipodocente'];
      //  $tiposegreteria = $_POST['tiposegreteria'];
      //  $cf = $_POST['codicefiscale'];
        ///////echo $_POST['tipo'];
        echo "<script>console.log('Debug Objects: " . $nome. " ". $cognome ." ". $cf." ".$targ. " ' );</script>";
        sleep(3);
      //  echo "elems= ".$nome.$cognome.$cf.$indirizzo.$citta.$persemail.$ricercato;
        // inserimento generale a livello di utente sia che sia docente, studente o segreteria
        try {
              ///////////////////////////

                $pdo = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                  $sql = "UPDATE utente SET nome = :nome,
                                            cognome = :cognome,
                                            codicefiscale = :cf,
                                            indirizzo = :indirizzo,
                                            citta = :citta,
                                            emailpersonale = :persemail
                                            WHERE email = :ricercato";

                    // Step 3: Create a prepared statement
                    $stmt = $pdo->prepare($sql);

                    // Step 4: Bind parameters to the prepared statement
                    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
                    $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
                    $stmt->bindParam(':cf', $cf, PDO::PARAM_STR);
                    $stmt->bindParam(':indirizzo', $indirizzo, PDO::PARAM_STR);
                    $stmt->bindParam(':citta', $citta, PDO::PARAM_STR);
                    $stmt->bindParam(':persemail', $persemail, PDO::PARAM_STR);
                    $stmt->bindParam(':ricercato', $targ, PDO::PARAM_STR);

                    // Step 5: Execute the prepared statement
                    $stmt->execute();

                    // Step 6: Check for success or handle any errors
                    $rowCount = $stmt->rowCount();
                    if ($rowCount > 0) {
                        echo "Update successful.";
                    } else {
                        echo "No rows updated.";
                    }
                } catch (PDOException $e) {
                    // Handle any errors that occurred during the process
                    echo "Error: " . $e->getMessage();
                }


/*
        $params = array ($nome, $cognome, $cf ,$indirizzo, $citta, $persemail ,$ricercato);
        $sql = "UPDATE utente SET nome = $1,
                                cognome = $2,
                                codicefiscale = $3,
                                indirizzo = $4,
                                citta = $5,
                                emailpersonale = $6
                                WHERE email = $7";
        $result = pg_prepare($db,'updateUser',$sql);
        $result = pg_execute($db,'updateUser', $params);
    /*
        //inserimento delle credenziali di primo accesso
        $params = array ($istitemail, $persemail);
        $sql = "INSERT INTO credenziali VALUES ($1,$2)";
        $result = pg_prepare($db,'inscred',$sql);
        $result = pg_execute($db,'inscred', $params);


        // casistica di inserimento
        $params = array ($istitemail);
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
        */

}
 ?>
-->

<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>