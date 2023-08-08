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
        $stmt = $conn->prepare("DELETE FROM propedeuticita
                                WHERE insegnamento1 = :ins1 AND insegnamento2 = :ins2 AND corso_di_laurea = :cdl");
        $stmt->bindParam(':ins1', $insegnamento1);
        $stmt->bindParam(':ins2', $insegnamento2);
        $stmt->bindParam(':cdl', $cdl);
        $stmt->execute();

        // Invia una risposta JSON di successo al client
        echo json_encode(['success' => true, 'message' => 'Cancellazione avvenuta con successo']);

    } catch (PDOException $e) {
        // Invia una risposta JSON di errore al client
        echo json_encode(['success' => false, 'message' => 'Errore nella cancellazione: ' . $e->getMessage()]);
    }
} else {
    // Invia una risposta JSON di errore al client se i parametri non sono stati forniti
    echo json_encode(['success' => false, 'message' => 'Parametri mancanti per la cancellazione']);
}
?>
