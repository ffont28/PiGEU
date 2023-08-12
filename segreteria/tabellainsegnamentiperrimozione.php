<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
    echo '<div style="margin-bottom: 20px"></div>';
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
            echo '<div class="alert alert-warning" role="alert">
                            Nessun insegnamento trovato
                      </div>';
            die();
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


        echo '
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
            <tbody>';

        $counter = 1;
        foreach ($results as $row) {
            $codice = $row['codice'];
            $nomeIns = $row['nomeins'];
            $cognome = $row['cognomedoc'];
            $nome = $row['nomedoc'];
            $numcorsi = $row['conta'];

            echo '  <tr>
                    <th scope="row">' . $counter++ . '</th>
                    <td>' . $codice. '</td>
                    <td>' . $nomeIns . '</td>
                    <td>' . $cognome . " " . $nome. '</td>
                    <td>' . $numcorsi . '</td>
                    <td style="text-align: center;">
                      <button class="button-canc"
                              codice="' . $codice . '"
                              ins="' . $nomeIns . '">RIMUOVI INSEGNAMENTO</button></td>
                    </tr> ';
        }
        echo '
            </tbody>
        </table>
        </div>';
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}

?>
