<?php
    session_start();
    include('../functions.php');
    include('../conf.php');
    controller("studente", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <title>Iscrizione a un esame · PiGEU</title>
</head>
<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarStudente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

    <h1> PAGINA DI ISCRIZIONE A UN ESAME</h1>

<!--    <div class="alert alert-primary" role="alert">-->
<!--        Benvenuto --><?php //echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?><!-- !-->
<!--    </div>-->

<?php
//include('../functions.php');
//include('../conf.php');

$studente = $_SESSION['username'];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $operazione = $_POST['op'];
    $studente = $_POST['studente'];
    $esame = $_POST['esame'];

    if ($operazione == "ISCR"){

        //echo "ciao ISCR=" . $_POST['esame'] . " CANC= " ;

        try {
            $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->query("LISTEN notifica");
            $sql = "INSERT INTO iscrizione (studente, esame) VALUES (:s, :e)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':s', $studente, PDO::PARAM_STR);
            $stmt->bindParam(':e', $esame, PDO::PARAM_INT);

            $stmt->execute();

            while (true) {
                $notify = $db->pgsqlGetNotify(PDO::FETCH_ASSOC, 50); // Aspetta per la notifica per 50 millisecondi
                if ($notify === false) {
                    //echo '<script> console.log("qui"); window.location.reload();</script>';
                    echo '  <div class="alert alert-success" role="alert" name="alert-message" >
                                  Iscrizione andata a buon fine 
                                </div>';
                    //  echo '<script> console.log("qui"); window.location.reload();</script>';
                    break;
                } else {
                    //echo '<script> console.log("qui"); window.location.reload();</script>';
                    echo '  <div class="alert alert-danger" role="alert" name="alert-message" >
                                  ' . $notify["payload"] . '
                                </div>';
                    break;
                }
            }
        } catch (PDOException $e) {

            // echo "Errore in inserimento: " . $e->getMessage();
        }
    }

    if ($operazione == "CANC"){

        //echo "ciao ISCR=" . $_POST['esame'] . " CANC= " ;

        try {
            $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $db->query("LISTEN notifica");
            $sql = "DELETE FROM iscrizione WHERE studente = :s AND esame = :e";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':s', $studente, PDO::PARAM_STR);
            $stmt->bindParam(':e', $esame, PDO::PARAM_INT);

            $stmt->execute();
            //echo '<script> console.log("qui"); window.location.reload();</script>';
            while (true) {
                $notify = $db->pgsqlGetNotify(PDO::FETCH_ASSOC, 50); // Aspetta per la notifica per 50 millisecondi
                if ($notify === false) {

                    echo '  <div class="alert alert-success" role="alert" name="alert-message" >
                                  Ti sei cancellato correttamene dall\'iscrizione 
                                </div>';
                    break;
                } else {
                    echo '  <div class="alert alert-danger" role="alert" name="alert-message" >
                                  ' . $notify["payload"] . '
                                </div>';
                    break;
                }
            }
        } catch (PDOException $e) {

            // echo "Errore in inserimento: " . $e->getMessage();
        }
    }

}



    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $query = "SELECT DISTINCT i.codice, i.nome, c.data, c.ora, c.id FROM insegnamento i
          INNER JOIN calendario_esami c ON i.codice = c.insegnamento
          INNER JOIN insegnamento_parte_di_cdl ip ON ip.insegnamento = i.codice
          INNER JOIN studente s ON s.corso_di_laurea = ip.corso_di_laurea
          WHERE s.utente = :studente
          ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':studente', $studente, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Esami attualmente calendarizzati</h3></label></div> 
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col">Data</th>
                <th scope="col">Ora</th>
                <th scope="col">ISCRIZIONE</th>
            </tr>
            </thead>
            <tbody>';

        $counter = 1;
        foreach ($results as $row) {
            // VERIFICO ISISCRITTO
            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Errore nella connessione al database: " . $e->getMessage());
            }

        // Query per verificare se lo studente è iscritto all'esame corrente
            $esameId = $row["id"]; // L'ID dell'esame corrente preso dal ciclo foreach

            $query = "SELECT * FROM iscrizione WHERE studente = :studente AND esame = :esame";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':studente', $studente);
            $stmt->bindParam(':esame', $esameId);
            $stmt->execute();

            $isIscritto = $stmt->rowCount() > 0;

            // Query per verificare l'esame corrente è già stato verbalizzato
            $esameCodice = $row["codice"]; // il codice dell'esame corrente preso dal ciclo foreach
            $esameData= $row["data"]; // a data dell'esame corrente preso dal ciclo foreach

            $query2 = "SELECT * FROM carriera WHERE
                       studente = :studente AND insegnamento = :insegnamento AND data = :data";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':studente', $studente);
            $stmt2->bindParam(':insegnamento', $esameCodice);
            $stmt2->bindParam(':data', $esameData);
            $stmt2->execute();

            $isVerbalizzato = $stmt2->rowCount() > 0; // true se l'utente è iscritto, false altrimenti
            $dataf = $row['data'] == "non sostenuto" ? "non sostenuto" : date("d/m/Y", strtotime($row['data']));

            $tableHTML .= '<tr>
                    <th scope="row">' . $counter++ . '</th>
                    <td>' . $row["codice"] . '</td>
                    <td>' . $row["nome"] . '</td>
                    <td>' . $dataf . '</td>
                    <td>' . $row["ora"] . '</td>
                    <td>';
            if ($isVerbalizzato) {
                // Se alll'utente è stato già verbalizzato il voto per quella data, non può fare nulla
                $tableHTML .= '<form action="" method="POST">
                              <input type="text" id="esame" name="esame" value="' . $row["id"] . '" hidden>
                              <input type="text" id="studente" name="studente" value="' . $studente . '" hidden>
                              <input type="text" id="op" name="op" value="CANC" hidden>
                              <button type="submit" class="button-info" disabled>ESITO GIA VERBALIZZATO</button></form>';
            } else
            if ($isIscritto) {
                // Se l'utente è iscritto, mostra il bottone "cancella iscrizione"
                $tableHTML .= '<form action="" method="POST">
                              <input type="text" id="esame" name="esame" value="'.$row["id"].'" hidden>
                              <input type="text" id="studente" name="studente" value="'.$studente.'" hidden>
                              <input type="text" id="op" name="op" value="CANC" hidden>
                              <button type="submit" class="button-canc">cancella iscrizione</button></form>';
            } else {
                // Altrimenti, mostra il bottone "iscriviti"
                $tableHTML .= '<form action="" method="POST">
                              <input type="text" id="esame" name="esame" value="'.$row["id"].'" hidden>
                              <input type="text" id="studente" name="studente" value="'.$studente.'" hidden>
                              <input type="text" id="op" name="op" value="ISCR" hidden>
                              <button type="submit" class="button-iscr">ISCRIVITI</button></form>';
            }
            $tableHTML .= '</td>
                    </tr>';
        }

        $tableHTML .= '</tbody></table></div>';

        echo $tableHTML;
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
?>

</body>

</html>

