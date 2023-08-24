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
    <title>Carriera · PiGEU</title>
</head>


<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarStudente($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>LA TUA CARRIERA COMPLETA</h1>

<div class="photo-container" style="margin: 30px">
    <div>

<?php
        try {
            $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $query = "SELECT c.nome nomecdl , c.tipo, s.matricola, u.nome, u.cognome FROM studente s
                      INNER JOIN utente u ON s.utente = u.email
                      INNER JOIN corso_di_laurea c ON s.corso_di_laurea = c.codice
                      WHERE s.utente = :studente";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':studente', $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
?>
               <a>CdL:<?php echo $result['nomecdl']."<br>"; ?></a>
               tipo: <?php echo $result['tipo'];
            } else {
?>              Sembra che <?php echo $_SESSION['username'] . "<br> non sia iscritto a nessun CdL";
            }
        } catch
        (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
?>
    </div>
    <div class="text" id="carriera">
        QUI LA TABELLA CON INSEGNAMENTI E VOTI E DATA
<?php
        try {

            $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

            $query = "SELECT * FROM carriera_completa(:studente)";
            $stmt = $conn->prepare($query);

            $stmt->bindParam(':studente', $_SESSION['username']);
                        // $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            // Esecuzione della query e recupero dei risultati
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
?>
                    <div class="alert alert-warning" role="alert">
                            Nessun insegnamento trovato in carriera
                    </div>
<?php
            } else {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Insegnamento</th>
                            <th scope="col">Codice</th>
                            <th scope="col">Valutazione</th>
                            <th scope="col">Data</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                $counter = 1;
                foreach ($results as $row) {
                    $insegnamento = $row['nomins'];
                    $codice = $row['codins'];
                    $voto = $row['voto'];
                    $data = $row['data'];
?>

                        <tr>
                            <th scope="row"><?php echo $counter++ ?></th>
                            <td><?php echo $insegnamento; ?></td>
                            <td><?php echo $codice; ?></td>
                            <td><?php echo $voto ?></td>
                            <td><?php echo $data ?></td>

                        </tr>
<?php           }
?>

                    </tbody>
                </table>
            </div>
<?php       }
        } catch
        (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
?>
    </div>
    <div>

        <div class="photo-container-my">
            Scarica la tua carriera in formato PDF
            <form action="../generaPDF2.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="carriera_completa">
                <input type="hidden" name="utente" value="<?php echo $_SESSION['username']?>">
                <button type="submit" name="submit" class="btn btn-primary mt-3 background-green">DOWNLOAD</button>
            </form>
        </div>

    </div>
</div>
</body>
<?php
$conn = null;
?>
</html>