<?php session_start(); ?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/from-re.css">
    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/calendarioesami.css">
    <script src="../js/general.js"></script>
 <!--   <script src="../js/calendarioesami.js"></script> -->

    <meta charset="utf-8">
    <title>Iscrizione a un esame · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="main.php">Homepage</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../modificaPassword.php">Modifica la tua password</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#">Iscrizione Esami</a>
    </li>
    <li class="nav-item mr-2">
        <a class="nav-link" href="../index.php">LOGOUT</a>
    </li>
</ul>
<!-- FINE NAVBAR -->

    <h1> PAGINA DI ISCRIZIONE A UN ESAME</h1>

    <div class="alert alert-primary" role="alert">
        Benvenuto <?php echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?> !
    </div>
    <!--
    <div>
        <label for="exampleFormControlInput1" class="form-label">Inserisci la data e l'ora per l'esame</label>
            <form id="inserimentoDataOra" action="" method="POST">
                <label for="insegnamento" >Insegnamento:</label>
                <select type='insegnamento' id="insegnamento" name="insegnamento">
                <?php /*
                include('../functions.php');
                include('../conf.php');
                $docente = $_SESSION['username'];
                echo "<script>console.log('Debug Objects:>> " . $docente .  " ' );</script>";

                try {
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                    // Query con CTE
                    $query = "  SELECT i.codice, i.nome  FROM insegnamento i
                        INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                        WHERE d.docente = :docente";
                    //echo "<script>console.log('Qui3');</script>";
                    // Esecuzione della query e recupero dei risultati
                    $stmt = $conn->prepare($query);

                    $stmt->bindParam(':docente', $docente, PDO::PARAM_STR);
                    $stmt->execute();

                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    // Elaborazione dei risultati
                    foreach ($results as $row) {

                        // Utilizza $row per accedere ai dati dei singoli record
                        echo "<option value=\"".$row['codice']."\">".$row['nome']."</option> ";
                    }
                    echo ' </select>';

                } catch (PDOException $e) {
                    echo "Errore: " . $e->getMessage();
                }

                */
                ?>
                <label for="data">Data:</label>
                <input type="date" id="data" name="data">
                <label for="time">Ora:</label>
                <input type="time" id="ora" name="ora">
            <label for="submit">conferma l'inserimento</label>
                <input type="submit" class="button1 green" value="INSERISCI" onclick="showAlertMessage()">
            </form>
    </div>
    -->
<?php
/*
    if($_SERVER['REQUEST_METHOD']=='POST'){

        if (isset($_POST['data']) && isset($_POST['ora'])) {
            if ($_POST['data'] == "") {
                echo '<div class="alert alert-warning" role="alert" name="alert-message" >
                Attenzione: devi inserire una data e un\'ora prima di selezionare INSERISCI
                      </div>';
            }
            try {
                $insegnamento = $_POST['insegnamento'];
                $data = $_POST['data'];
                $ora = $_POST['ora'];

                $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->query("LISTEN notifica");
                $sql = "INSERT INTO calendario_esami (insegnamento, data, ora) VALUES (:insegnamento, :data, :ora)";

                $stmt = $db->prepare($sql);

                $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
                $stmt->bindParam(':data', $data, PDO::PARAM_STR);
                $stmt->bindParam(':ora', $ora, PDO::PARAM_STR);


                $stmt->execute();

                while (true) {
                    $notify = $db->pgsqlGetNotify(PDO::FETCH_ASSOC, 50); // Aspetta per la notifica per 50 millisecondi
                    if ($notify === false) {
                        echo '  <div class="alert alert-success" role="alert" name="alert-message" >
                                  Inserimento dell\'esame andato a buon fine 
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
            $_POST['data'] = "";
        }
    }*/
include('../functions.php');
include('../conf.php');

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

            $tableHTML .= '<tr>
                    <th scope="row">' . $counter++ . '</th>
                    <td>' . $row["codice"] . '</td>
                    <td>' . $row["nome"] . '</td>
                    <td>' . $row["data"] . '</td>
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



///////////////////////////////////////////////////////////////////////////////////
/*
echo "
        <script>
// Funzione per effettuare la richiesta AJAX
function iscriviti(utente, id) {
  const xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {
    if (this.readyState === 4) {
      if (this.status === 200) {
        // Gestisci la risposta del server
        const response = JSON.parse(this.responseText);
        console.log(response);

        // Ricarica la pagina indipendentemente dal successo o dall'errore
        window.location.reload();
      } else {
        // Gestisci eventuali errori
        console.error('Errore nella richiesta AJAX:', this.statusText);
      }
    }
  };

  xhttp.open('POST', 'iscriviti.php', true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  const params = 'id=' + encodeURIComponent(id) + '&utente=' + encodeURIComponent(utente);
  xhttp.send(params);
}

// Aggiungi un evento clic per i pulsanti di classe \"button-iscr\"
const iscrButtons = document.querySelectorAll('.button-iscr');
iscrButtons.forEach(button => {
  button.addEventListener('click', function() {
    const id = this.getAttribute('data-cod');
    const utente = this.getAttribute('data-user');

    // Effettua la richiesta AJAX
    iscriviti(utente, id);
  });
});
</script>

<!-- Assicurati di includere jQuery prima di includere il tuo script JavaScript -->
<script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>

<!-- Il tuo script JavaScript -->
<script>
$(document).ready(function() {
  // Aggiungi un gestore di eventi al click del bottone \"cancella iscrizione\"
  $('.button-canc').on('click', function() {
    // Ottieni i dati dal bottone cliccato
    var codice = $(this).data('cod');
    var user = $(this).data('user');

    // Crea l'oggetto di dati da inviare al server
    var dataToSend = {
      codice: codice,
      user: user
    };

    // Effettua la chiamata AJAX per cancellare l'iscrizione
    $.ajax({
      url: 'cancellati.php', // Sostituisci con il percorso del tuo script PHP per la cancellazione
      type: 'POST', // O 'GET' a seconda del metodo del tuo script PHP
      data: dataToSend,
      success: function(response) {
        // La risposta dal server dopo la cancellazione (puoi gestirla qui se necessario)
        console.log(response);
        
        // Aggiorna l'interfaccia utente, nascondendo il bottone \"cancella iscrizione\"
        $(this).hide();
        location.reload();
      },
      error: function(error) {
        // Gestisci eventuali errori durante la chiamata AJAX
        console.error('Errore durante la chiamata AJAX:', error);
      }
    });
  });
});
</script>";
*/
?>

</body>

</html>

