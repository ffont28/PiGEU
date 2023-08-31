<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
?>  <div style="margin-bottom: 20px"></div>
<?php
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
?>          <div class="alert alert-warning" role="alert">
                Nessun utente trovato
            </div>
<?php       die();
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>          <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Cognome</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Email Istituzionale </th>
                        <th scope="col">tipo utenza</th>
                        <th scope="col" style="text-align: center;">AZIONI</th>
                    </tr>
                </thead>
            <tbody>
<?php
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


            ?> <tr>
                    <th scope="row"><?php echo $counter++ ?></th>
                    <td><?php echo $cognome ?></td>
                    <td><?php echo $nome ?></td>
                    <td><?php echo $email ?></td>
                    <td><?php echo $tipo ?></td>
                    <td style="text-align: center;">
<?php if ($tipo == "Studente")
                            { ?>
                      <button class="button-info"
                      utente="<?php echo $email ?>">SPOSTA IN STORICO</button>
<?php   }
?>
                      <button class="button-verb"
                              utente="<?php echo $email ?>">GESTISCI UTENTE</button></td>
                      
                    </tr> <?php
        }
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
