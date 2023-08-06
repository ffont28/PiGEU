<?php session_start();
include('../functions.php');
include('../conf.php');
// Configura le informazioni di connessione al database PostgreSQL
//$dbhost = "localhost";  // Indirizzo del database
//$dbname = "pigeu";  // Nome del database
//$dbuser = "fontanaf";  // Nome utente del database
//$dbpass = "font";  // Password del database
//
//// Ottieni il valore inviato dalla richiesta AJAX
$insegnamento = $_GET['value'];
$datacfr = $_SESSION['dataimpostata'];
//echo $datacfr;
//echo "<script>console.log('ciaone');</script>";
//sleep(5);
try {
    // Connessione al database utilizzando PDO
    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
    $stmt = $conn->prepare("SELECT * FROM insegnamento i  
                                  INNER JOIN calendario_esami c ON i.codice = c.insegnamento
                                  WHERE i.codice = :valore");
    $stmt->bindParam(':valore', $insegnamento);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
        echo '<option ';
        $data = new DateTime($row['data']);
        if ($datacfr == $row['data']){ echo 'selected';}
        echo ' value="' . $row['data'] . '">' . $data->format("d/m/Y") . '</option>';

    }

    echo "<script>
    window.addEventListener('DOMContentLoaded', function() {
        // Ottieni il valore della data selezionata precedentemente
        var selectedDate = \"<?php echo isset($datacfr) ? $datacfr : ''; ?>\";

// Trova il campo select per la data
var selectElement = document.getElementById('data');

// Imposta l'attributo \"selected\" dell'opzione corrispondente alla data selezionata
if (selectedDate) {
for (var i = 0; i < selectElement.options.length; i++) {
if (selectElement.options[i].value === selectedDate) {
selectElement.options[i].selected = true;
break;
}
}
}
});
</script>";

} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}


?>



