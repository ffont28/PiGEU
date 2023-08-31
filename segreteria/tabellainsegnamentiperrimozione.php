<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
?>  <div style="margin-bottom: 20px"></div>
<?php
    $search = $_POST['search'];
    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

        $query = "WITH numeroErogato AS (
                  SELECT insegnamento, COUNT (corso_di_laurea) conta FROM insegnamento_parte_di_cdl ip
                  GROUP BY insegnamento
                  )
                  SELECT i.codice, i.nome nomeins, u.nome nomedoc, u.cognome cognomedoc, n.conta 
                  FROM insegnamento i LEFT JOIN docente_responsabile d ON i.codice = d.insegnamento
                  INNER JOIN utente u ON d.docente = u.email
                  INNER JOIN numeroErogato n ON n.insegnamento = i.codice
                  WHERE i.nome ILIKE :search OR i.codice ILIKE :search OR u.cognome ILIKE :search
                  ORDER BY i.nome";
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
?>      <div class="alert alert-warning" role="alert">
            Nessun insegnamento trovato
        </div>
<?php   die();
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
            <div class="table-container">
            <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice</th>
                <th scope="col">Nome</th>
                <th scope="col">Docente Responsabile</th>
                <th scope="col">n° CdL che erogano questo insegnamento</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
            $codice = $row['codice'];
            $nomeIns = $row['nomeins'];
            $cognome = $row['cognomedoc'];
            $nome = $row['nomedoc'];
            $numcorsi = $row['conta'];

?>           <tr>
                <th scope="row"><?php echo $counter++ ?></th>
                <td><?php echo $codice?></td>
                <td><?php echo $nomeIns ?></td>
                <td><?php echo $cognome . " " . $nome?></td>
                <td><?php echo $numcorsi ?></td>
                <td style="text-align: center;">
                  <button class="button-canc"
                          codice="<?php echo $codice ?>"
                          ins="<?php echo $nomeIns ?>">RIMUOVI INSEGNAMENTO</button></td>
                </tr>
<?php   }
?>
            </tbody>
        </table>
        </div>
<?php
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
$conn = null;
?>
