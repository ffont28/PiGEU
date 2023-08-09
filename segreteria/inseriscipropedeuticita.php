<?php
include('../functions.php');
include('../conf.php');

if (isset($_POST['insegnamento1']) && isset($_POST['insegnamento2']) && isset($_POST['cdl'])) {
    $insegnamento1 = $_POST['insegnamento1'];
    $insegnamento2 = $_POST['insegnamento2'];
    $cdl = $_POST['cdl'];

    try {
        // Connessione al database utilizzando PDO
        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Esegui la query per cancellare la propedeuticità dal database
        $stmt = $conn->prepare("INSERT INTO propedeuticita (insegnamento1, insegnamento2, corso_di_laurea) 
                                      VALUES (:ins1,:ins2,:cdl)");
        $stmt->bindParam(':ins1', $insegnamento1);
        $stmt->bindParam(':ins2', $insegnamento2);
        $stmt->bindParam(':cdl', $cdl);
        $stmt->execute();

        // Invia una risposta JSON di successo al client
        echo json_encode(['success' => true, 'message' => 'Inserimento avvenuto con successo']);

    } catch (PDOException $e) {
        // Invia una risposta JSON di errore al client
        echo json_encode(['success' => false, 'message' => 'Errore nell\'inserimento: ' . $e->getMessage()]);
    }
} else {
    // Invia una risposta JSON di errore al client se i parametri non sono stati forniti
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti per l\'inserimento']);
}
?>
