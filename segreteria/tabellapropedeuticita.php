<?php
session_start();
include('../functions.php');
include('../conf.php');

$selezioneCdL = $_GET['value'];

try {

    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
    $stmt = $conn->prepare("SELECT p.insegnamento1 cod1, i1.nome nom1, p.insegnamento2 cod2, i2.nome nom2
                                  FROM propedeuticita p INNER JOIN insegnamento i1 ON p.insegnamento1 = i1.codice
                                                        INNER JOIN insegnamento i2 ON p.insegnamento2 = i2.codice
                                  WHERE p.corso_di_laurea = :c");

    $stmt->bindParam(':c', $selezioneCdL);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $counter = 1;
    //echo "<option selected value=\"no\">nessuna propedeuticità</option>";
    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
?>
        <tr>
            <th scope="row"><?php echo $counter++ ?></th>
            <td><?php echo $row["cod1"] ?></td>
            <td><?php echo $row["nom1"] ?></td>
            <td style="text-align: center" > >>>> </td>
            <td><?php echo $row["cod2"] ?></td>
            <td><?php echo $row["nom2"] ?></td>
            <td>
            <button class="button-canc" 
                      ins1="<?php echo $row["cod1"] ?>"
                      ins2="<?php echo $row["cod2"] ?>"
                      cdl="<?php echo $selezioneCdL ?>">🗑️</button></td>
            </tr>


<?php
    }
}
catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
$conn = null;    
?>
