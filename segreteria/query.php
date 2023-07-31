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
    $stmt = $conn->prepare("SELECT * FROM corso_di_laurea WHERE nome = :valore");
    $stmt->bindParam(':valore', $selezioneMenutendina1);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
        echo '<option value="' . 1 . '">' . "primo" . '</option>';
        echo '<option value="' . 2 . '">' . "secondo" . '</option>';
        echo '<option value="' . 3 . '">' . "terzo" . '</option>';
        if ($row['tipo'] == 'magistrale'){
        echo '<option value="' . 4 . '">' . "quarto" . '</option>';
        echo '<option value="' . 5 . '">' . "quinto" . '</option>';
        }
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>
