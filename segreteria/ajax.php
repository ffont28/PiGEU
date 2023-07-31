<?php
// Configura le informazioni di connessione al database
include('../conf.php');


// Ottieni il valore inviato dalla richiesta AJAX
$valore = $_GET['value'];

try {

    // Connessione al database utilizzando PDO
    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = " SELECT tipo FROM corso_di_laurea WHERE codice = $valore";

    // Esecuzione della query e recupero dei risultati
    $stmt = $conn->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
        echo '<option value="' . 1 . '"> primo </option>';
        echo '<option value="' . 2 . '"> secondo </option>';
        echo '<option value="' . 3 . '"> terzo </option>';
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>
