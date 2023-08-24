<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
    echo '<div style="margin-bottom: 20px"></div>';
    $search = $_POST['search'];
    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

        $query = "WITH numeroCorsi AS (
                  SELECT corso_di_laurea, COUNT (insegnamento) conta FROM insegnamento_parte_di_cdl ip
                  GROUP BY corso_di_laurea
                  )
                  SELECT c.codice, c.nome, c.tipo, n.conta 
                  FROM corso_di_laurea c 
                  LEFT JOIN numeroCorsi n ON n.corso_di_laurea = c.codice
                  WHERE c.nome ILIKE :search OR c.codice ILIKE :search
                  ORDER BY c.nome";
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            echo '<div class="alert alert-warning" role="alert">
                            Nessun insegnamento trovato
                      </div>';
            die();
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
                <th scope="col">Tipo di Laurea</th>
                <th scope="col">n° Insegnamenti</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
            $codice = $row['codice'];
            $nome = $row['nome'];
            $tipo = $row['tipo'];
            $numinsegnamenti = ($row['conta'] == "" ? 0 : $row['conta']);

?>                  <tr>
                    <th scope="row"><?php echo $counter++ ?></th>
                    <td><?php echo $codice?></td>
                    <td><?php echo $nome ?></td>
                    <td><?php echo $tipo?></td>
                    <td><?php echo $numinsegnamenti ?></td>
                    <td style="text-align: center;">
                      <button class="button-canc"
                              codice="<?php echo $codice ?>"
                              cdl="<?php echo $nome ?>">RIMUOVI CORSO DI LAUREA</button></td>
                    </tr>
<?php   }
?>         </tbody>
        </table>
        </div>
<?php
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
$conn= null;
?>
