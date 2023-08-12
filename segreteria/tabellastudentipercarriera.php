<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
    echo '<div style="margin-bottom: 20px"></div>';
    $search = $_POST['search'];
    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

        $query = "SELECT u.cognome cognome, u.nome nome, u.email, cdl.nome nomecdl 
                  FROM utente u INNER JOIN studente s ON u.email = s.utente
                                INNER JOIN corso_di_laurea cdl ON cdl.codice = s.corso_di_laurea
                  WHERE u.cognome ILIKE :search OR u.nome ILIKE :search OR u.email ILIKE :search
                  ORDER BY cognome";
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            echo '<div class="alert alert-warning" role="alert">
                            Nessun utente trovato
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
                <th scope="col">Cognome</th>
                <th scope="col">Nome</th>
                <th scope="col">Email Istituzionale </th>
                <th scope="col">Corso di Laurea</th>
                <th scope="col" style="text-align: center;">GENERA CARRIERA</th>
            </tr>
            </thead>
            <tbody>';

        $counter = 1;
        foreach ($results as $row) {
            $cognome = $row['cognome'];
            $nome = $row['nome'];
            $email = $row['email'];
            $tipo = $row['nomecdl'];

            echo '  <tr>
                    <th scope="row">' . $counter++ . '</th>
                    <td>' . $cognome . '</td>
                    <td>' . $nome . '</td>
                    <td>' . $email . '</td>
                    <td>' . $tipo . '</td>
                    <td style="text-align: center;">
                      <button class="button-verb" utente="' . $email . '">CARRIERA COMPLETA</button>
                      <button class="button-iscr" utente="' . $email . '"> CARRIERA VALIDA</button>
                      </td>
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
