<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
    ?>  <div style="margin-bottom: 20px"></div>
    <?php
    $search = $_POST['search'];
    $codiceInsegnamento= $_POST['ci'];
    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

        $query = "SELECT u.nome, u.cognome, u.email
                               FROM utente u INNER JOIN docente d ON u.email = d.utente
                                             WHERE d.utente <> ALL (SELECT docente FROM insegna WHERE insegnamento = :i)
                                             AND (u.nome ILIKE :search OR u.cognome ILIKE :search)
                               EXCEPT
                               SELECT u.nome, u.cognome, u.email
                               FROM utente u INNER JOIN docente d ON u.email = d.utente
                                             INNER JOIN docente_responsabile dr ON d.utente = dr.docente
                                             WHERE dr.insegnamento = :i
                                             
                               ORDER BY cognome
                               LIMIT 10";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            ?>      <div class="alert alert-warning" role="alert">
                Nessun docente corrisponde ai parametri di ricerca
            </div>
            <?php   die();
        } else {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="table-container">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">DOCENTE </th>
                    <th scope="col" style="text-align: center;">AGGIUNGI</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $counter = 1;
                foreach ($results as $row) {
                    ?> <tr>
                        <th scope="row"><?php echo $counter++ ?></th>
                        <td><?php echo $row["cognome"]. " ".$row["nome"]?></td>
                        <td style="text-align: center;">
                            <button class="button-add-doc"
                                    docente="<?php echo $row['email'] ?>"
                                    insegnamento="<?php echo $codiceInsegnamento ?>">aggiungi docente</button></td>
                    </tr>
                <?php   }
                ?>
                </tbody>
            </table>
        </div>
        <?php
        }
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
$conn = null;
?>
