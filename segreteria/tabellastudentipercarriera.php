<?php
include('../functions.php');
include('../conf.php');

if(isset($_POST['search'])) {
?>  <div style="margin-bottom: 20px"></div>
<?php
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
        $stmt->execute();
        $query2 = "SELECT u.cognome cognome, u.nome nome, u.email, cdl.nome nomecdl 
                  FROM utente_storico u INNER JOIN studente_storico s ON u.email = s.utente
                                INNER JOIN corso_di_laurea cdl ON cdl.codice = s.corso_di_laurea
                  WHERE u.cognome ILIKE :search OR u.nome ILIKE :search OR u.email ILIKE :search
                  ORDER BY cognome";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt2->execute();

        if (($stmt->rowCount() == 0) && ($stmt2->rowCount() == 0)) {
?>          <div class="alert alert-warning" role="alert">
                Nessun utente trovato
            </div>
<?php       die();
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
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
                <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
            $cognome = $row['cognome'];
            $nome = $row['nome'];
            $email = $row['email'];
            $tipo = $row['nomecdl'];
?>
                <tr>
                    <th scope="row"><?php echo $counter++ ?></th>
                    <td><?php echo $cognome ?></td>
                    <td><?php echo $nome ?></td>
                    <td><?php echo $email ?></td>
                    <td><?php echo $tipo ?></td>
                    <td style="text-align: center;">
                      <button class="button-verb" utente="<?php echo $email ?>" tipo="normal">CARRIERA COMPLETA</button>
                      <button class="button-iscr" utente="<?php echo $email ?>" tipo="normal"> CARRIERA VALIDA</button>
                      </td>
                </tr>
<?php   }
        foreach ($results2 as $row) {
            $cognome = $row['cognome'];
            $nome = $row['nome'];
            $email = $row['email'];
            $tipo = $row['nomecdl'];
?>
                <tr>
                    <th scope="row"><?php echo $counter++ ?> <a style="text-align: right">❗</a></th>
                    <td><?php echo $cognome ?></td>
                    <td><?php echo $nome ?></td>
                    <td><?php echo $email ?></td>
                    <td><?php echo $tipo ?></td>
                    <td style="text-align: center;">
                      <button class="button-verb" utente="<?php echo $email ?>" tipo="storico">CARRIERA COMPLETA</button>
                      <button class="button-iscr" utente="<?php echo $email ?>" tipo="storico"> CARRIERA VALIDA</button>
                      </td>
                </tr>
<?php  }
?>

                </tbody>
           </table>

        <?php if ($stmt2->rowCount() > 0){ ?>
            <div><b>NOTA:</b> il segno ❗ accanto al numero progressivo indica che lo studente contenuto in quella riga è uno <i>studente storico</i></div>
  <?php      }?>

        </div>
        <?php
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
$conn = null;
?>
