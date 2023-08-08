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
    <title>Modifica Insegnamento · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
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
        <a class="nav-link active" href="#" aria-disabled="true">Modifica Insegnamento</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="aggiungicdl.php">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="modificacdl.php" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
</ul>
<!-- FINE NAVBAR -->

<h1>MODIFICA UN INSEGNAMENTO</h1>

<div class="alert alert-primary" role="alert">
    Benvenuto <?php echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?> !
</div>
<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona l'Insegnamento che vuoi modificare:</label>
    <form id="ricercaInsegnamento" action="" method="POST">
        <label for="cdl" >Insegnamento:</label>
        <select type='insegnamento' id="insegnamento" name="insegnamento">
            <?php

            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // vedo TUTTI gli insegnamenti
                $query = "  SELECT distinct i.nome, i.codice  FROM insegnamento i";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
                    echo "<option ";
                    if ($_POST['insegnamento'] == $row['codice']){ echo "selected";}
                    echo " value=\"".$row['codice']."\">".$row['nome']."</option> ";
                }
                echo ' </select>';

            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }

            ?>
        </select>
        <input type="submit" class="button1 green" value="CARICA INFORMAZIONI" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
    $codiceInsegnamento = $_POST['insegnamento'];

    if ($_POST['action'] == 'MODIFICA INSEGNAMENTO') {
        $db = open_pg_connection();
        echo "sono qui";
        // definisco le variabili

        $nome = $_POST['nome'];
        $codice = $_POST['codice'];
        $responsabile = $_POST['responsabile'];
        $descrizione = $_POST['descrizione'];
        $cfu = $_POST['cfu'];

        echo "<script>console.log('Debug Objects: >>" . $nome . " " . $tipo . " " . $codiceInsegnamento. "' );</script>";
        /////// AGGIORNO LA TABELLA INSEGNAMENTO
        try {

            $pdo = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $sql = "UPDATE insegnamento SET nome = :nome,
                                            codice = :codice,
                                            descrizione = :descrizione,
                                            cfu = :cfu
                            WHERE codice = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->bindParam(':cfu', $cfu, PDO::PARAM_STR);

            $stmt->execute();

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

        /////// AGGIORNO LA TABELLA RESPONSABILE
        try {

            $sql = "UPDATE docente_responsabile SET docente = :docente
                                            WHERE insegnamento = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':docente', $responsabile, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);

            $stmt->execute();

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
    }

    try {

        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);

        $query = "SELECT * FROM insegnamento i
                  INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                  WHERE i.codice = :i";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<form method=\"POST\" > <div class=\"center bblue\">";

        foreach ($results as $row) {
            $responsabile = $row['docente'];
            $anno = $row['anno'];
            $descrizione = $row['descrizione'];
            $cfu = $row['cfu'];

            echo "<div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Nome</label>
                      <input readonly type=\"text\" value=\"".$row['nome']."\" class=\"form-control\" id=\"nome\" name=\"nome\">
                        </div>
                       <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Codice</label>
                     <input type=\"text\" value=\"".$row['codice']."\" class=\"form-control\" id=\"codice\" name=\"codice\">
                       </div>
                       <div class=\"mb-3\">
                       <label for=\"exampleFormControlInput1\" class=\"form-label\">Docente Responsabile</label>
                     
                        <select class=\"form-select\" id=\"responsabile\" name=\"responsabile\">";

                    try {
                    // Connessione al database utilizzando PDO
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Query con CTE
                    $query = "     WITH selezione AS (
                                                      SELECT utente FROM docente
                                                      EXCEPT
                                                      SELECT docente FROM docente_responsabile
                                                      GROUP BY 1
                                                      HAVING count(*) >2
                                                      )
                                                      SELECT u.nome, u.cognome, u.email FROM utente u
                                                      INNER JOIN selezione s ON u.email = s.utente
                           ";

                           // Esecuzione della query e recupero dei risultati
                           $stmt2 = $conn->query($query);
                           $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);


                           foreach ($results as $row) {
                                 echo "<option ";
                                 if ($responsabile == $row['email']) {echo 'selected ';}
                                 echo "value=\"".$row['email']."\">".$row['nome']." ".$row['cognome']."</option> ";
                                 }
                            } catch (PDOException $e) {
                                echo "Errore: " . $e->getMessage();
                            }
                        echo "
                        </select>
                       </div>
                       <div class=\"mb-3\">
                       <label for=\"exampleFormControlInput1\" class=\"form-label\">Descrizione</label>
                     <input type=\"text\" value=\"".$descrizione."\" class=\"form-control\" id=\"descrizione\" name=\"descrizione\">
                       </div>
                     ";

            echo '<div id="cfu">
                        <label for="exampleFormControlInput1" class="form-label">CFU previsti per l\'insegnamento</label>
                        <select class="form-select" aria-label="Default select example" id="cfu" name="cfu">
                              <option ';
            if ($cfu == '6') {echo " selected ";}
            echo 'value="6">6</option>
                              <option ';
            if ($cfu== '9') {echo 'selected ';}
            echo 'value="9">9</option>
                              <option ';
            if ($cfu == '12') {echo 'selected ';}
            echo 'value="12">12</option>
                            </select>
                    </div>';

                                        //// VA TENUTO PER LA RICHIESTA AJAX ////
//            echo '<div id="anno">
//                    <label for="exampleFormControlInput1" class="form-label">Anno dell\'insegnamento</label>
//                    <select class="form-select" aria-label="Default select example" id="anno" name="anno">
//                              <option ';
//            if ($cfu == '6') {echo " selected ";}
//            echo 'value="6">6</option>
//                              <option ';
//            if ($cfu== '9') {echo 'selected ';}
//            echo 'value="9">9</option>
//                              <option ';
//            if ($cfu == '12') {echo 'selected ';}
//            echo 'value="12">12</option>
//                            </select>
//                    </div>';

            echo " <input type=\"submit\" class=\"button1 orange\" value=\"MODIFICA INSEGNAMENTO\" name='action'/>
                        </div>
                    </form>
                    ";

        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
///////////////////////////// CDL DI CUI QUESTO INSEGNAMENTO FA PARTE E CHE POSSO RIMUOVERE //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT c.codice, c.nome, ip.anno, ins.cfu, ins.codice codice_ins
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                        INNER JOIN insegnamento ins ON ip.insegnamento = ins.codice
                        WHERE ip.insegnamento = :i
                        ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Corsi di laurea che contemplano questo insegnamento</h3></label></div> 
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Corso di Laurea</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>';

        $counter = 1;
        foreach ($results as $row) {
            echo '  <tr>
                    <th scope="row">'.$counter++.'</th>
                    <td>'.$row["codice"].'</td>
                    <td>'.$row["nome"].'</td>
                    <td>'.$row["anno"].'</td>
                    <td>'.$row["cfu"].'</td>
                    <td style="text-align: center;">
                      <button class="button-canc" 
                              insegnamento="'. $codiceInsegnamento .'" 
                              cdl="' . $row["codice"] . '">rimuovi da questo CdL</button></td>
                    </tr> ';
        }
        echo '
            </tbody>
        </table>
    </div>';
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }

    echo "
        <script>
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
</script>";

    ///////////////////////////// CDL DI CUI QUESTO INSEGNAMENTO NON FA PARTE E CHE POSSO AGGIUNGERE ORA //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $strcfu = strval($cfu);
        $query = "SELECT c.codice, c.nome, ins.codice codice_ins, c.tipo
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento ins ON ins.codice = :i
                  EXCEPT
                        SELECT c.codice, c.nome, ins.codice codice_ins, c.tipo
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                        INNER JOIN insegnamento ins ON ip.insegnamento = ins.codice
                        WHERE ip.insegnamento = :i
                        ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Corsi di laurea che NON contemplano questo insegnamento</h3></label></div> 
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Corso di Laurea</th>
                <th scope="col">Tipo</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col">Propedeuticità</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>';

        $counter = 1;
        foreach ($results as $row) {
            $codiceCdL = $row["codice"];
            echo '  <tr>
                    <th scope="row">'.$counter++.'</th>
                    <td>'.$row["codice"].'</td>
                    <td>'.$row["nome"].'</td>
                    <td>'.$row["tipo"].'</td>

                    <td>
                    
                    <select class="form-control" id="anno" name="anno">';

                    try {
                    // Connessione al database utilizzando PDO
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Query con CTE
                    $query = "SELECT tipo
                              FROM corso_di_laurea
                              WHERE codice = :c";
                    $stmt = $conn->prepare($query);

                    $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $tipo = "";
                        foreach ($results as $row) {
                            echo '<option value="' . 1 . '">' . "primo" . '</option>';
                            echo '<option value="' . 2 . '">' . "secondo" . '</option>';
                            if ($row['tipo'] == 'magistrale a ciclo unico' || $row['tipo'] == 'triennale' ){
                                echo '<option value="' . 3 . '">' . "terzo" . '</option>';
                            }
                            if ($row['tipo'] == 'magistrale a ciclo unico'){
                                echo '<option value="' . 4 . '">' . "quarto" . '</option>';
                                echo '<option value="' . 5 . '">' . "quinto" . '</option>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo "Errore: " . $e->getMessage();
                    }
         echo '     </select>
                    </td>
                    <td>'.$cfu.'</td>
                    <td>
                    
                    <select class="form-control2" id="propedeuticita" name="propedeuticita">';

            try {
                // Connessione al database utilizzando PDO
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Query con CTE
                $query = "SELECT i.codice, i.nome
                          FROM insegnamento i 
                          INNER JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento 
                          WHERE ip.corso_di_laurea = :c
                         ";
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $tipo = "";
                echo "<option selected value=\"no\">nessuna propedeuticità</option>";
                foreach ($results as $row) {
                    echo '<option value="' . $row['codice'] . '">' . $row['nome'] . '</option>';
                }
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
            echo '  </select>
                    </td>
                    <td style="text-align: center;">
                      <button class="button-iscr" 
                              insegnamento="'. $codiceInsegnamento .'" 
                              cdl="' . $codiceCdL  . '">inserisci nel CdL</button></td>
                    </tr>';
        }
        echo '
            </tbody>
        </table>
    </div>';
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }

    echo "

        <script>
 document.addEventListener('DOMContentLoaded', function() {
  // Funzione per effettuare la richiesta AJAX
  function inserisciInsinCdL(insegnamento, cdl, anno, propedeuticita) {
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

    xhttp.open('POST', 'inseriscinelcdl.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'insegnamento=' + encodeURIComponent(insegnamento) + 
                    '&cdl=' + encodeURIComponent(cdl) +
                    '&anno=' + encodeURIComponent(anno) +
                    '&propedeuticita=' + encodeURIComponent(propedeuticita);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const addToCdlButtons = document.querySelectorAll('.button-iscr');
  addToCdlButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const cdl = this.getAttribute('cdl');
      const annoold = this.closest('tr').querySelector('select#anno.form-control');
      const anno = annoold.value;
      const propedeuticita = this.closest('tr').querySelector('select#propedeuticita.form-control2').value;
      
      console.log(anno);
      
      // Effettua la richiesta AJAX
      inserisciInsinCdL(insegnamento, cdl, anno, propedeuticita);
    });
  });
  
  });
</script>";


}

?>

</body>

</html>


