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
    <title>Info sui CdL · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarStudente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>INFORMAZIONI SUI CORSI DI LAUREA</h1>

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
                    ?> <option <?php
                    if ($_POST['cdl'] == $row['codice']){ ?> selected <?php }
                    ?> value="<?php echo $row['codice'] ?>"> <?php echo $row['nome'] ?> </option>
            <?php    } ?>
        </select>

<?php       } catch (PDOException $e) {
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
           $nomec =$row["nomec"];
           $codicec= $row["codicec"];
           $anno = $row["anno"];
           $cfu = $row["cfu"];
           $responsabile = $row["cognomedoc"]. " " . $row["nomedoc"];
           $descrizione = $row["descrizione"];


            if ($infoIniziali) {
                ?><div><label for="exampleFormControlInput1" class="form-label"><h4>Informazioni sul Corso di Laurea in </h4><h2><?php echo strtoupper($row['nome'])?></h2>
                      <h4>laurea <?php echo $row['tipo']?></h4><h5>codice: <?php echo $row['codice']?></h5> </label></div>
                <div>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" style="width: 220px" >Insegnamento</th>
                        <th scope="col">codice</th>
                        <th scope="col">anno</th>
                        <th scope="col">CFU</th>
                        <th scope="col" style="width: 200px" >Docente</th>
                        <th scope="col">Descrizione dell'insegnamento</th>
                    </tr>
                    </thead>
                    <tbody>
<?php           $infoIniziali = false;
            }
?>
                    <tr>
                        <th scope="row"><?php $counter++ ?></th>
                        <td><?php echo $nomec ?></td>
                        <td><?php echo $codicec ?></td>
                        <td><?php echo $anno ?></td>
                        <td><?php echo $cfu ?></td>
                        <td><?php echo $responsabile;
                                $query = "SELECT *
                                          FROM insegna i
                                          INNER JOIN utente u ON i.docente = u.email
                                          WHERE insegnamento = :ins ORDER BY u.cognome,u.nome";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':ins', $codicec, PDO::PARAM_STR);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $firstrow = true;
                                foreach ($results as $row) {
                                    if ($firstrow) {?> (Resp.) <?php $firstrow = false;}
                                    $docenteins = $row['cognome'] . " " .$row['nome'];
?>
                                    <br>
<?php                               echo $docenteins; }
?>
                        </td>
                        <td><?php echo $descrizione ?></td>
                        </tr>
<?php        }
?>
                    </tbody>
                </table>
            </div>
<?php } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}

$conn = null;
?>

</body>

</html>

