<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
    echo '<div style="margin-bottom: 20px"></div>';
    $search = $_POST['search'];
        try {

            $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);

            $query = "SELECT * FROM utente u
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
            <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Cognome</th>
                <th scope="col">Nome</th>
                <th scope="col">Email Istituzionale </th>
                <th scope="col">tipo utenza</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>';

            $counter = 1;
            foreach ($results as $row) {
                $cognome = $row['cognome'];
                $nome = $row['nome'];
                $email = $row['email'];
                $tipo = "UNDEFINED";

                $query = "SELECT * FROM docente WHERE utente = :email";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $tipo = "Docente";
                }

                $query = "SELECT * FROM studente WHERE utente = :email";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $tipo = "Studente";
                }

                $query = "SELECT * FROM segreteria WHERE utente = :email";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $tipo = "Segreteria";
                }


                echo '  <tr>
                    <th scope="row">' . $counter++ . '</th>
                    <td>' . $cognome . '</td>
                    <td>' . $nome . '</td>
                    <td>' . $email . '</td>
                    <td>' . $tipo . '</td>
                    <td style="text-align: center;">
                      <button class="button-canc"
                              utente="' . $email . '"
                              nome="' . $nome . '"
                              cognome="' . $cognome . '">RIMUOVI UTENTE</button></td>
                    </tr> ';
            }
            echo '
            </tbody>
        </table >';
            } catch
        (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
    }

?>
