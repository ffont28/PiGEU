<?php
session_start();
include('../functions.php');
controller("docente", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <title>Verbalizzazione · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarDocente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1> PAGINA DI VERBALIZZAZIONE ESITI</h1>


<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona l'appello per cui si vuole procedere alla verbalizzazione</label>
    <form id="inserimentoInsegnamentoEData" action="" method="POST">
        <label for="insegnamento" >Appello di:</label>
        <select type='insegnamento' id="insegnamento" name="insegnamento">
            <?php
            $docente = $_SESSION['username'];
            echo "<script>console.log('Debug Objects:>> " . $docente .  " ' );</script>";

            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                // vedo TUTTI gli appelli, anche quelli a cui non si è iscritto nessuno
                $query = "  SELECT distinct i.nome, i.codice  FROM insegnamento i
                            INNER JOIN calendario_esami c ON c.insegnamento = i.codice
                            INNER JOIN docente_responsabile d ON d.docente = :docente AND d.insegnamento = i.codice                      
                            ";
                //echo "<script>console.log('Qui3');</script>";
                // Esecuzione della query e recupero dei risultati
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':docente', $docente, PDO::PARAM_STR);
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


            ?>
            <label for="data">svolto in data:</label>
            <select type="data" id="data" name="data">

            </select>

                <script>
    function aggiornaData() {
                console.log("richiesta funzione"); ////////////////////////////////////////////////////////////////////////////
                var insegnamento = document.getElementById("insegnamento").value;
                var data = document.getElementById("data");

                // Ottieni il valore selezionato nel primo menù a tendina
                //insegnamento.value;

                // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                    console.log("qui"); ////////////////////////////////////////////////////////////////////////////////////////
                        if (xhr.status === 200) {
                               console.log("CONNESSO OK"); /////////////////////////////////////////////////////////////////////
                            // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                            data.innerHTML = xhr.responseText;
                        } else {
                            // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                            console.error("Errore durante la richiesta AJAX");
                        }
                    }
                };

                // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
                console.log("data->>>" + data.value);
                xhr.open("GET", "dataEsamePerVerbalizzazione.php?value=" + insegnamento + "&data=" + data.value, true);
                xhr.send();
            }

            // Aggiungi un ascoltatore di eventi per il menù a tendina 1
            document.getElementById("insegnamento").addEventListener('change', aggiornaData);

            // Inizializza il contenuto del secondo menù a tendina inizialmente
            aggiornaData();
        </script>


           <input type="submit" class="button1 green" value="RICERCA STUDENTI ISCRITTI" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
        $insegnamento = $_POST['insegnamento'];
        $data = $_POST['data'];
        $_SESSION['dataimpostata'] = $_POST['data'];

            try {


                $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              //  echo " -->>".$data;
                $query = "SELECT DISTINCT u.email, u.cognome, u.nome, s.matricola, c.insegnamento, c.data
                          FROM studente s
                          INNER JOIN utente u ON u.email = s.utente
                          INNER JOIN calendario_esami c ON c.insegnamento = :insegnamento
                                                        AND c.data = :data
                          INNER JOIN iscrizione i ON i.studente = u.email
                         -- INNER JOIN carriera ca ON ca.data <> :data 
                         --                        AND ca.studente = u.email
                         --                        AND ca.insegnamento = c.insegnamento
                          ";

                $stmt = $conn->prepare($query);

                $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
                $stmt->bindParam(':data', $data, PDO::PARAM_STR);
           //     $stmt->bindParam(':dataimpostata', $_SESSION['dataimpostata'], PDO::PARAM_STR);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo ' <div><label for="exampleFormControlInput1" class="form-label"><h3>Studenti che hanno sostenuto l\'esame, in attesa di verbalizzazione</h3></label></div> 
                <div>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Matricola </th>
                        <th scope="col">Cognome e nome studente </th>
                        <th scope="col">Esito da verbalizzare</th>
                        <th scope="col">VERBALIZZA</th>
                    </tr>
                    </thead>
                    <tbody>';

                $counter = 1;
                foreach ($results as $row) {
                    echo '  <tr>
                            <th scope="row">' . $counter++ . '</th>
                            <td>' . $row["matricola"] . '</td>
                            <td>' . $row["cognome"] . " " . $row["nome"] . '</td>
                            <td><input type="text" class="form-control"  placeholder="valutazione espressa in trentesimi" id="votoDaVerbalizzare" name="votoDaVerbalizzare"></td>
                            <td>
                              <button class="button-verb" 
                                      insegnamento="' . $row["insegnamento"] . '" 
                                      studente="' . $row["email"] . '"
                                      dataEsame="' . $row["data"] . '">VERBALIZZA</button></td>
                            </tr> ';
                }


                echo '
                    </tbody>
                </table>
            </div>';
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
}
    echo "
        <script>
  // Funzione per effettuare la richiesta AJAX per verbalizzare
  function verbalizzaEsame(insegnamento, studente, dataEsame, valutazione) {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState === 4) {
        if (this.status === 200) {
          // Gestisci la risposta del server
          const response = JSON.parse(this.responseText);
          console.log(response);
        if (response.success) {
           window.location.href = 'verbalizzazione.php';
          }
        } else {
          // Gestisci eventuali errori
          console.error('Errore nella richiesta AJAX:', this.statusText);
          window.location.reload();
         }
      }
    };

    
    xhttp.open('POST', 'verbalizza.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'insegnamento=' + encodeURIComponent(insegnamento) + 
                    '&studente=' + encodeURIComponent(studente) + 
                    '&dataEsame=' + encodeURIComponent(dataEsame) +
                    '&valutazione=' + encodeURIComponent(valutazione);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const verbButtons = document.querySelectorAll('.button-verb');
  verbButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const studente = this.getAttribute('studente');
      const dataEsame = this.getAttribute('dataEsame');
      var valutazione = this.closest('tr').querySelector('.form-control').value;
      console.log(insegnamento + \" \" + studente + \" \" + dataEsame + \" \" +valutazione);
      
      // Effettua la richiesta AJAX
      verbalizzaEsame(insegnamento, studente, dataEsame, valutazione);
    });
  });
</script>";

?>

</body>

</html>

