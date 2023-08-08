<?php
include('../functions.php');
include('../conf.php');
if(isset($_GET['cdl'])) {
    $cdl = $_GET['cdl'];
    try {
        // Connessione al database utilizzando PDO
        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
        $stmt = $conn->prepare("SELECT distinct(i.nome), i.codice FROM insegnamento i
                                INNER JOIN insegnamento_parte_di_cdl p ON p.corso_di_laurea = :valore
                                                                            AND p.insegnamento = i.codice
                                ");
        $stmt->bindParam(':valore', $cdl);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // echo "<option selected value=\"no\">nessuna propedeuticità</option>";
        // Genera le opzioni per il secondo menù a tendina
        foreach ($results as $row) {
            echo '<option value="' . $row['codice'] . '">' . $row['nome'] . '</option>';
        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
?>