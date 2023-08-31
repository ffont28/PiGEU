<?php
include('../functions.php');
include('../conf.php');
if (isset($_POST['utente'])) {
    $utente = $_POST['utente'];
    try {
        // Connessione al database utilizzando PDO
        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Esegui la query per cancellare la propedeuticità dal database
        $stmt = $conn->prepare("DELETE FROM utente WHERE email = :utente");
        $stmt->bindParam(':utente', $utente);
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
$conn = null;
?>
