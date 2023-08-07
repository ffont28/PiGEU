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
        <input type="submit" class="button1 green" value="CARICA LE INFORMAZIONI DEL CDL" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
    $codiceCdL = $_POST['cdl'];

    if ($_POST['action'] == 'MODIFICA CORSO DI LAUREA') {
        $db = open_pg_connection();
        echo "sono qui";
        // definisco le variabili

        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];

        echo "<script>console.log('Debug Objects: >>" . $nome . " " . $tipo . " " . $codiceCdL. "' );</script>";

        try {

            $pdo = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $sql = "UPDATE corso_di_laurea SET nome = :nome,
                                            tipo = :tipo
                                            WHERE codice = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codiceCdL, PDO::PARAM_STR);

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

        $query = "SELECT * FROM corso_di_laurea c
                  WHERE c.codice = :c";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<form method=\"POST\" > <div class=\"center bblue\">";

           foreach ($results as $row) {
               $tipoinq = $row['tipo'];

                 echo "<div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Codice</label>
                      <input readonly type=\"text\" value=\"".$row['codice']."\" class=\"form-control\" id=\"cdl\" name=\"cdl\">
                        </div>
                       <div class=\"mb-3\">
                                <label for=\"exampleFormControlInput1\" class=\"form-label\">Nome</label>
                     <input type=\"text\" value=\"".$row['nome']."\" class=\"form-control\" id=\"nome\" name=\"nome\">
                       </div>
                     ";

                 echo '<div id="tipocorso">
                       Tipo di corso di studi:
                        <select class="form-select" aria-label="Default select example" id="tipo" name="tipo">
                              <option ';
                            if ($tipoinq == "triennale") {echo "triennale";}
                                echo 'value="triennale">triennale</option>
                              <option ';
                            if ($tipoinq == "magistrale") {echo 'selected ';}
                                echo 'value="magistrale">magistrale</option>
                              <option ';
                            if ($tipoinq == "magistrale a ciclo unico") {echo 'selected ';}
                                echo 'value="magistrale a ciclo unico">magistrale a ciclo unico</option>
                            </select>
                    </div>';

               echo " <input type=\"submit\" class=\"button1 orange\" value=\"MODIFICA CORSO DI LAUREA\" name='action'/>
                        </div>
                    </form>
                    ";

                 }
       } catch (PDOException $e) {
           echo "Errore: " . $e->getMessage();
       }
///////////////////////////// INSEGNAMENTI CHE FANNO PARTE DEL CDL E CHE POSSO RIMUOVERE //////////////////////
try {


    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = "SELECT DISTINCT i.codice, i.nome, i.anno, i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                            WHERE ip.corso_di_laurea = :c ORDER BY anno, cfu";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Insegnamenti che fanno parte del corso di laurea</h3></label></div> 
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col">Docente Responsabile</th>
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
                    <td>'.$row["cognome"]. " " . $row['nomedoc'].'</td>
                    <td style="text-align: center;">
                      <button class="button-canc" 
                              insegnamento="'. $row["codice"] .'" 
                              cdl="' . $codiceCdL . '">rimuovi dal CdL</button></td>
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

    ///////////////////////////// INSEGNAMENTI CHE NON FANNO PARTE DEL CDL E CHE POSSO AGGIUNGERE ORA //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $query = "SELECT DISTINCT i.codice, i.nome, i.anno, i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            LEFT JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                  EXCEPT
                            SELECT DISTINCT i.codice, i.nome, i.anno, i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            LEFT JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                            WHERE ip.corso_di_laurea = :c 
                  ORDER BY nome";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Insegnamenti che NON fanno parte del corso di laurea</h3></label></div> 
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col">Docente Responsabile</th>
                <th scope="col" style="text-align: center;">AGGIUINGI</th>
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
                    <td>'.$row["cognome"]. " " . $row['nomedoc'].'</td>
                    <td style="text-align: center;">
                      <button class="button-iscr" 
                              insegnamento="'. $row["codice"] .'" 
                              cdl="' . $codiceCdL . '">inserisci nel CdL</button></td>
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
  function inserisciInsinCdL(insegnamento, cdl) {
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
    const params = 'insegnamento=' + encodeURIComponent(insegnamento) + '&cdl=' + encodeURIComponent(cdl);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const addToCdlButtons = document.querySelectorAll('.button-iscr');
  addToCdlButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const cdl = this.getAttribute('cdl');

      // Effettua la richiesta AJAX
      inserisciInsinCdL(insegnamento, cdl);
    });
  });
</script>";


}

?>

</body>

</html>


