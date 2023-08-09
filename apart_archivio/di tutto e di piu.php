<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/from-re.css">
    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/calendarioesami.css">
    <link rel="stylesheet" href="../css/general.css">
    <script src="../js/general.js"></script>
    <!--   <script src="../js/calendarioesami.js"></script> -->

    <meta charset="utf-8">
    <title>Modifica CdL · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="../segreteria/main.php">Homepage</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="aggiungiutente.php">Aggiungi Utenza</a>
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
        <a class="nav-link active" href="#" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
</ul>

<h1>MODIFICA UN CORSO DI LAUREA</h1>

<div class="alert alert-primary" role="alert">
    Benvenuto <?php echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?> !
</div>
<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona il Corso di Laurea che vuoi modificare:</label>
    <form id="ricercaCdL" action="" method="POST">
        <label for="cdl" >Corso di Laurea:</label>
        <select type='insegnamento' id="cdl" name="cdl">
            <?php

            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // vedo TUTTI i corsi di laurea
                $query = "  SELECT distinct c.nome, c.codice  FROM corso_di_laurea c";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
                    echo "<option ";
                    if ($_POST['cdl'] == $row['codice']){ echo "selected";}
                    echo " value=\"".$row['codice']."\">".$row['nome']."</option> ";
                }
                echo ' </select>';

            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }

            ?>
        </select>
        <input type="submit" class="button1 green" value="CERCA INFORMAZIONI" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
    $codiceCdL = $_POST['cdl'];

    try {
        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        // vedo TUTTI gli appelli, anche quelli a cui non si è iscritto nessuno
        $query = "SELECT i.codice, i.nome FROM corso_di_laurea c
                                INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                                INNER JOIN insegnamento i ON ip.insegnamento = i.codice
                            --  LEFT JOIN propedeuticita p ON i.codice = p.insegnamento2
                  WHERE c.codice = :c";

        // Esecuzione della query e recupero dei risultati
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Elaborazione dei risultati
        foreach ($results as $row) {
            // Utilizza $row per accedere ai dati dei singoli record
            echo "<option ";
            if ($_POST['insegnamento'] == $row['codice']){ echo "selected";}
            echo " value=\"".$row['codice']."\">".$row['nome']."</option> ";
        }
        echo ' </select>';

    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }



///////////////////////////////////////////////////////////////////////////////////////////////
    try {




        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);

        // Query
        $query = "SELECT * FROM corso_di_laurea c
                                INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                                INNER JOIN insegnamento i ON ip.insegnamento = i.codice
                            --  LEFT JOIN propedeuticita p ON i.codice = p.insegnamento2
                  WHERE c.codice = :c";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
        echo "<form method=\"POST\" > <div class=\"center bblue\">";

        foreach ($results as $row) {
            $corsoinq = $row['corso_di_laurea'];
            $tipoinq = $row['tipo'];
            $email = $row['email'];
            echo '<script>console.log(\'>>'.$corsoinq." & ".$tipoinq.'<<\')</script>';
            echo "<div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Nome</label>
                      <input hidden type=\"text\" value=\"".$row['email']."\" class=\"form-control\" id=\"hricercato\" name=\"hricercato\">
                      <input type=\"text\" value=\"".$row['nome']."\" class=\"form-control\" id=\"nome\" name=\"nome\">
                        </div>
                       <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Cognome</label>
                     <input type=\"text\" value=\"".$row['cognome']."\" class=\"form-control\" id=\"cognome\" name=\"cognome\">
                       </div>
                       <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">CODICE FISCALE</label>
                     <input type=\"text\" value=\"".$row['codicefiscale']."\" class=\"form-control\" id=\"codicefiscale\" name=\"codicefiscale\">
                       </div>
                     <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">indirizzo</label>
                      <input  type=\"text\" value=\"".$row['indirizzo']."\" class=\"form-control\" id=\"indirizzo\" name=\"indirizzo\">
                       </div>
                     <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">città</label>
                     <input type=\"text\" value=\"".$row['citta']."\" class=\"form-control\" id=\"citta\" name=\"citta\">
                      </div>


                     <div class=\"mb-3\">
                               <label for=\"exampleFormControlInput1\" class=\"form-label\">email personale</label>
                     <input type=\"text\" value=\"".$row['emailpersonale']."\" class=\"form-control\" id=\"emailpersonale\" name=\"emailpersonale\">
                       </div> ";


            // caso in cui l'utente sia uno STUDENTE
            if ($corsoinq != ""){
                echo '  <div id="cdl" class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Corso di Laurea a cui lo studente risulta iscritto:</label>
                        <select class="form-select" id="cdl" name="cdl">';
                //   <!--<option selected value="ciao">Open this select menu</option>-->


                try {
                    // Connessione al database utilizzando PDO
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Query con CTE
                    $query = " SELECT c.nome, c.codice FROM corso_di_laurea c";

                    // Esecuzione della query e recupero dei risultati
                    $stmt = $conn->query($query);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Elaborazione dei risultati
                    foreach ($results as $row) {
                        // Utilizza $row per accedere ai dati dei singoli record
                        echo "<option ";
                        if ($row['codice'] == $corsoinq){ echo "selected ";}
                        echo "value=\"".$row['codice']."\">".$row['nome']."</option> ";
                    }
                } catch (PDOException $e) {
                    echo "Errore: " . $e->getMessage();
                }

                echo ' </select>
                        </div>';
            }
            // caso in cui l'utente sia un DOCENTE
            if ($tipoinq != "") {
                echo '<script>console.log(\'>>'.$corsoinq." &2 ".$tipoinq.'<<\')</script>';

                echo '<div id="tipodocente">
                       Tipo di contratto che ha il docente:
                        <select class="form-select" aria-label="Default select example" id="tipo" name="tipodocente">
                              <option ';
                if ($tipoinq == "a contratto") {echo "selected ";}
                echo 'value="a contratto">A contratto</option>
                              <option ';
                if ($tipoinq == "ricercatore") {echo 'selected ';}
                echo 'value="ricercatore">Ricercatore</option>
                              <option ';
                if ($tipoinq == "associato") {echo 'selected ';}
                echo 'value="associato">Associato</option>
                            </select>
                    </div>';
            }
            $query2 = "SELECT * FROM studente WHERE utente = :email";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':email', $email);
            $stmt2->execute();
            //  echo "------->>>".$email;
            $isStudente = $stmt2->rowCount() > 0;

            if($isStudente){
                echo " <input type=\"submit\" class=\"button1 red\" value=\"SPOSTA STUDENTE IN STORICO\" name='action' />";
            }
            echo " <input type=\"submit\" class=\"button1 orange\" value=\"MODIFICA ANAGRAFICA UTENTE\" name='action'/>
                        </div>
                    </form>
                    ";

        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/*   try {

       $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       $query = "SELECT * FROM informazioni_cdl WHERE codice = :c ORDER BY anno, cfu";
       $stmt = $conn->prepare($query);
       $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
       $stmt->execute();

       $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

       $infoIniziali = true;
       $counter = 1;
       $numeroinsegnamenti = 0;

       foreach ($results as $row) {

           if ($infoIniziali) {
//                $query2 = "SELECT * FROM informazioni_cdl i WHERE i.codice = :c";
//                $stmt2 = $conn->prepare($query2);
//                $stmt2->bindParam(':c', $row['codice']);
//                $stmt2->execute();
//
//                $numeroinsegnamenti = $stmt2->rowCount();

               echo '<div><label for="exampleFormControlInput1" class="form-label"><h4>Informazioni sul Corso di Laurea in </h4><h2>' . strtoupper($row['nome']) . '</h2>
                     <h4>laurea ' . $row['tipo'] . '</h4><h5>codice: ' . $row['codice'] . '</h5> </label></div>';
               echo '
               <div>
               <table class="table">
                   <thead>
                   <tr>
                       <th scope="col">#</th>
                       <th scope="col">Insegnamento</th>
                       <th scope="col">codice</th>
                       <th scope="col">anno</th>
                       <th scope="col">CFU</th>
                       <th scope="col">Responsabile</th>
                       <th scope="col">Descrizione dell\'insegnamento</th>
                   </tr>
                   </thead>
                   <tbody>';
               $infoIniziali = false;
           }
           echo '  <tr>
                           <th scope="row">' . $counter++ . '</th>
                           <td>' . $row["nomec"] . '</td>
                           <td>' . $row["codicec"] . '</td>
                           <td>' . $row["anno"] . '</td>
                           <td>' . $row["cfu"] . '</td>
                           <td>' . $row["cognomedoc"] . " " . $row["nomedoc"] . '</td>
                           <td>' . $row["descrizione"] . '</td>
                           </tr> ';
       }
       echo '
                   </tbody>
               </table>
           </div>';
   } catch (PDOException $e) {
       echo "Errore: " . $e->getMessage();
   }
}*/
?>

</body>

</html>


