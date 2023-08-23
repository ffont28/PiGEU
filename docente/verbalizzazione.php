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
<?php setNavbarDocente($_SERVER['REQUEST_URI']);
?>
<!-- FINE NAVBAR -->

<h1> PAGINA DI VERBALIZZAZIONE ESITI</h1>

<?php
    $docente = $_SESSION['username'];

    if($_SERVER['REQUEST_METHOD']=='POST') {

        if ($_POST['op'] == 'VERBALIZZA' &&
            isset($_POST['insegnamento']) &&
            isset($_POST['studente']) &&
            isset($_POST['data']) &&
            isset($_POST['votoDaVerbalizzare'])) {

            $insegnamento = $_POST['insegnamento'];
            $studente = $_POST['studente'];
            $dataEsame = $_POST['data'];
            $valutazione = $_POST['votoDaVerbalizzare'];

            try {
                $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $db->query("LISTEN notifica");

                $sql = "INSERT INTO carriera (studente, insegnamento, valutazione, data)  VALUES (:s, :i, :v, :d)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':s', $studente, PDO::PARAM_STR);
                $stmt->bindParam(':i', $insegnamento, PDO::PARAM_STR);
                $stmt->bindParam(':d', $dataEsame, PDO::PARAM_STR);
                $stmt->bindParam(':v', $valutazione, PDO::PARAM_INT);

                $stmt->execute();

                while (true) {

                    $notify = $db->pgsqlGetNotify(PDO::FETCH_ASSOC, 50);
                    if ($notify === false) {
?>                      <div class="alert alert-success" role="alert" name="alert-message" >
                         Voto registrato correttamente in carriera
                        </div>
<?php                   break;
                    } else {
?>                      <div class="alert alert-danger" role="alert" name="alert-message" >
                          <?php echo $notify["payload"] ?>
                        </div>
<?php                   break;
                    }
                }
            } catch (PDOException $e) {

            }
        }

    }

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
?>
<div>
<label for="exampleFormControlInput1" class="form-label">Seleziona l'appello per cui si vuole procedere alla verbalizzazione</label>
<form id="inserimentoInsegnamentoEData" action="" method="POST">
<label for="insegnamento" >Appello di:</label>
        <select type='insegnamento' id="insegnamento" name="insegnamento">

<?php
        foreach ($results as $row) {
?>      <option
<?php       if ($_POST['insegnamento'] == $row['codice']){?> selected <?php }
?>          value="<?php echo $row['codice']?>"> <?php echo $row['nome']?></option>
<?php   }
?>
        </select>
<?php
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
?>
            <label for="data">svolto in data:</label>
            <select type="data" id="data" name="data">

            </select>

                <script>
    function aggiornaData() {
                var insegnamento = document.getElementById("insegnamento").value;
                var data = document.getElementById("data");

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {

                        if (xhr.status === 200) {
                            data.innerHTML = xhr.responseText;
                        } else {
                            console.error("Errore durante la richiesta AJAX");
                        }
                    }
                };

                console.log("data->>>" + data.value);
                xhr.open("GET", "dataEsamePerVerbalizzazione.php?value=" + insegnamento + "&data=" + data.value, true);
                xhr.send();
            }

            document.getElementById("insegnamento").addEventListener('change', aggiornaData);

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
                                                  AND i.esame = c.id
                          EXCEPT 
                          SELECT DISTINCT u.email, u.cognome, u.nome, s.matricola, c.insegnamento, c.data
                          FROM studente s
                          INNER JOIN utente u ON u.email = s.utente
                          INNER JOIN calendario_esami c ON c.insegnamento = :insegnamento
                                                        AND c.data = :data
                          INNER JOIN carriera ca ON ca.studente = s.utente
                                                 AND ca.data = :data
                                                 AND ca.insegnamento = :insegnamento
                          ";

                $stmt = $conn->prepare($query);

                $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
                $stmt->bindParam(':data', $data, PDO::PARAM_STR);
           //     $stmt->bindParam(':dataimpostata', $_SESSION['dataimpostata'], PDO::PARAM_STR);
                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>              <div>
                    <label for="exampleFormControlInput1" class="form-label"><h3>Studenti che hanno sostenuto l\'esame, in attesa di verbalizzazione</h3></label>
                </div>
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
                    <tbody>
<?php
                $counter = 1;
                foreach ($results as $row) {
?>                  <tr> <form action="" method="POST">
                    <th scope="row"><?php echo $counter++ ?></th>
                    <td><?php echo $row["matricola"] ?></td>
                    <td><?php echo $row["cognome"] . " " . $row["nome"]?></td>
                    <td><input type="text" class="form-control"  placeholder="valutazione espressa in trentesimi" id="votoDaVerbalizzare" name="votoDaVerbalizzare"></td>
                    <td>
                            <input type="text" id="insegnamento" name="insegnamento" value="<?php echo $row["insegnamento"]?>" hidden>
                            <input type="text" id="studente" name="studente" value="<?php echo $row["email"]?>" hidden>
                            <input type="text" id="data" name="data" value="<?php echo $row["data"]?>" hidden>
                            <input type="text" id="op" name="op" value="VERBALIZZA" hidden>
                            <button type="submit2" class="button-verb">VERBALIZZA</button>
                        </form>

                    </td>
                    </tr>
<?php           }
?>
                  </tbody>
                </table>
            </div>
<?php       } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
}

$conn = null;
$db = null;
?>
</body>
</html>