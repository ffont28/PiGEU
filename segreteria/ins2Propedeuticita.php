<?php
include('../functions.php');
include('../conf.php');

if(isset($_GET['cdl']) && isset($_GET['cod1'])) {
    $cdl = $_GET['cdl'];
    $cod1 = $_GET['cod1'];
    try {
        // Connessione al database utilizzando PDO
        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
        $stmt = $conn->prepare("
                            WITH anno1 AS (
                            SELECT anno FROM insegnamento_parte_di_cdl
                            WHERE insegnamento = :cod1 AND corso_di_laurea = :cdl
                            ), proibite AS (
                            SELECT insegnamento2 FROM propedeuticita
                            WHERE insegnamento1 = :cod1 AND corso_di_laurea = :cdl
                            )
                            SELECT distinct(i.nome), i.codice, p.anno FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl p ON p.corso_di_laurea = :cdl
                                                                    AND p.insegnamento = i.codice
                            WHERE i.codice <> :cod1 
                                  AND p.anno >= ALL (SELECT * FROM anno1)
                                  AND i.codice <> ALL (SELECT * FROM proibite)
                            ");
        $stmt->bindParam(':cdl', $cdl);
        $stmt->bindParam(':cod1', $cod1);
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
$conn = null;
?>