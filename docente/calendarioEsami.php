<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("docente", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <title>Calendario Esami · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarDocente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

    <h1> PAGINA DI GESTIONE CALENDARIO ESAMI</h1>

    <div>
        <label for="exampleFormControlInput1" class="form-label">Inserisci la data e l'ora per l'esame</label>
            <form id="inserimentoDataOra" action="" method="POST">
                <label for="insegnamento" >Insegnamento:</label>
                <select type='insegnamento' id="insegnamento" name="insegnamento">
                <?php
                $docente = $_SESSION['username'];

                try {
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $query = "  SELECT i.codice, i.nome  FROM insegnamento i
                        INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                        WHERE d.docente = :docente";

                    $stmt = $conn->prepare($query);

                    $stmt->bindParam(':docente', $docente, PDO::PARAM_STR);
                    $stmt->execute();

                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($results as $row) {
?>
                    <option value="<?php echo $row['codice']?>"> <?php echo $row['nome'] ?> </option>
<?php               }
?>              </select>

<?php           } catch (PDOException $e) {
                    echo "Errore: " . $e->getMessage();
                }
?>
                <label for="data">Data:</label>
                <input type="date" id="data" name="data">
                <label for="time">Ora:</label>
                <input type="time" id="ora" name="ora">
                <input type="submit" class="button1 green" value="INSERISCI" >
            </form>
    </div>

<?php
    if($_SERVER['REQUEST_METHOD']=='POST'){
        if (isset($_POST['data']) && isset($_POST['ora'])) {
            if ($_POST['data'] == "") {
?>              <div class="alert alert-warning" role="alert" name="alert-message" >
                Attenzione: devi inserire una data e un'ora prima di selezionare INSERISCI
                </div>
<?php
            } else{
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
    ?>
                         <div class="alert alert-success" role="alert" name="alert-message" >
                            Inserimento dell'esame andato a buon fine
                         </div>
    <?php                break;
                        } else {
    ?>                   <div class="alert alert-danger" role="alert" name="alert-message" >
                            <?php echo $notify["payload"]?>
                         </div>
    <?php                break;
                        }
                    }
                } catch (PDOException $e) {
                   echo "Errore in inserimento: " . $e->getMessage();
                }
                $_POST['data'] = "";
            }
        }
        $_SERVER['REQUEST_METHOD']='null';
    }

    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $query = "SELECT DISTINCT i.codice, i.nome, c.data, c.ora FROM insegnamento i
          INNER JOIN calendario_esami c ON i.codice = c.insegnamento
          INNER JOIN docente_responsabile d ON d.docente = :docente AND d.insegnamento = i.codice
          ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':docente', $docente, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>      <div>
            <label for="exampleFormControlInput1" class="form-label"><h3>Esami attualmente calendarizzati</h3></label>
        </div>
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col">Data</th>
                <th scope="col">Ora</th>
                <th scope="col">CANC</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
?>                  <tr>
                    <th scope="row"><?php echo $counter++?></th>
                    <td><?php echo $row["codice"]?></td>
                    <td><?php echo $row["nome"]?></td>
                    <td><?php echo date("d/m/Y", strtotime($row['data']))?></td>
                    <td><?php echo $row["ora"]?></td>
                    <td>
                      <button class="button-canc" 
                              data-cod="<?php echo $row["codice"]?>"
                              data-dat="<?php echo $row["data"]?>"
                              data-ora="<?php echo $row["ora"]?>">🗑️</button></td>
                    </tr>
<?php   }
?>
            </tbody>
        </table>
    </div>
<?php
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
?>
<script>

document.addEventListener('DOMContentLoaded', function() {

    function deleteRow(cod, data, ora) {
        const xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                if (this.status === 200) {

                    const response = JSON.parse(this.responseText);
                    console.log(response);
                    if (response.success) {
                        window.location.reload();
                    }
                } else {

                    console.error('Errore nella richiesta AJAX:', this.statusText);
                }
            }
        };


        xhttp.open('POST', '../cancella_esame.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        const params = 'cod=' + encodeURIComponent(cod) + '&data=' + encodeURIComponent(data) + '&ora=' + encodeURIComponent(ora);
        xhttp.send(params);
    }

    let deleteButtons = document.querySelectorAll('.button-canc');
    function setDeleteButtons () {

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const cod = this.getAttribute('data-cod');
                const data = this.getAttribute('data-dat');
                const ora = this.getAttribute('data-ora');

                // Effettua la richiesta AJAX
                deleteRow(cod, data, ora);
            });
        });
    }
    setDeleteButtons();
    // Aggiungo un evento per tutti i pulsanti di classe \"button-canc\" quando vengono cliccati
    document.addEventListener('submit', function() {
    setDeleteButtons();
    });
    console.log(deleteButtons);
});
</script>
</body>
<?php
$conn = null;
$db = null;
?>
</html>
