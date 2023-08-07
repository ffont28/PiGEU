<?php
// Configura le informazioni di connessione al database PostgreSQL
$dbhost = "localhost";  // Indirizzo del database
$dbname = "pigeu";  // Nome del database
$dbuser = "fontanaf";  // Nome utente del database
$dbpass = "font";  // Password del database

// Ottieni il valore inviato dalla richiesta AJAX
$selezioneMenutendina1 = $_GET['value'];

try {
    // Connessione al database utilizzando PDO
    $conn = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
    $stmt = $conn->prepare("SELECT distinct(i.nome), i.codice FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl p ON p.corso_di_laurea = :valore 
                                                                        AND p.insegnamento = i.codice 
                            ");
    $stmt->bindParam(':valore', $selezioneMenutendina1);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo "<option selected value=\"no\">nessuna propedeuticità</option>";
    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
        echo '<option value="' . $row['codice'] . '">' . $row['nome'] . '</option>';
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>
