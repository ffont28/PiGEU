<?php
session_start();
include('../functions.php');
include('../conf.php');
if (isset($_POST['action']) && isset($_POST['utente']) &&
    ($_POST['action'] == 'carriera_completa' || $_POST['action'] == 'carriera_valida')){

    $studente = $_POST['utente'];
    $nome = "";
    $cognome = "";
    $matricola = "";

    try {

    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $query = "SELECT * FROM carriera_completa_esami_sostenuti(:studente)";
    if ($_POST['action'] == 'carriera_completa'){
        $query = "SELECT * FROM carriera_completa_tutti(:studente)";
    }
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':studente', $studente);

    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        ?>
        <div class="alert alert-warning" role="alert">
            Nessun insegnamento trovato in carriera per <?php echo $studente ?>
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

         if ($counter == 1){   ?>
             <h3 style="margin: 13px">Carriera di
                 <?php echo $row['cogstu']." ".$row['nomstu']." - matricola ".$row['matr'] ?></h3>
<?php   }
?>

<?php
                    $insegnamento = $row['nomins'];
                    $codice = $row['codins'];
                    $voto = $row['voto'];
            $data = $row['data'] == "non sostenuto" ? "non sostenuto" : date("d/m/Y", strtotime($row['data']));
                    ?>

                    <tr>
                        <th scope="row"><?php echo $counter++ ?></th>
                        <td><?php echo $insegnamento; ?></td>
                        <td><?php echo $codice; ?></td>
                        <td><?php echo $voto ?></td>
                        <td><?php echo $data ?></td>

                    </tr>
                <?php }
                ?>

                </tbody>
            </table>
        </div>
    <?php }
} catch
(PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>
</div>
<div>

    <div class="photo-container-my">
        Scarica la carriera di in formato PDF
        <form action="../generaPDF2.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $_POST['action']?>">
            <input type="hidden" name="utente" value="<?php echo $_POST['utente']?>">
            <button type="submit" name="submit" class="btn btn-primary mt-3 background-green">DOWNLOAD</button>
        </form>
    </div>

</div>
</div>
<?php }
?>