<?php session_start();
include('../functions.php');
include('../conf.php');
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
    <title>Info sui CdL · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="#">Homepage</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../modificaPassword.php">Modifica la tua password</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="iscrizioneEsame.php">Iscrizione Esami</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="infoCdL.php">Info sui Corsi di Laurea</a>
    </li>
    <li class="nav-item mr-2">
        <a class="nav-link" href="../index.php">LOGOUT</a>
    </li>
</ul>
<!-- FINE NAVBAR -->

<h1>INFORMAZIONI SUI CORSI DI LAUREA</h1>

<div class="alert alert-primary" role="alert">
    Benvenuto <?php echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?> !
</div>
<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona il Corso di Laurea del quale vuoi ottenere informazioni:</label>
    <form id="ricercaCdL" action="" method="POST">
        <label for="cdl" >Corso di Laurea:</label>
        <select type='insegnamento' id="cdl" name="cdl">
            <?php

          //  $docente = $_SESSION['username'];
          //  echo "<script>console.log('Debug Objects:>> " . $docente .  " ' );</script>";

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

    /*
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
    } */
    ///$_SESSION['dataimpostata'] = $_POST['data'];
    // echo $_SESSION['dataimpostata'];
    try {

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
}
?>

</body>

</html>

