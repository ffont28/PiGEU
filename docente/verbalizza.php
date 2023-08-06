<?php
include('../functions.php');
include('../conf.php');
if (isset($_POST['insegnamento'])
    && isset($_POST['studente'])
    && isset($_POST['dataEsame'])
    && isset($_POST['valutazione'])) {

    $insegnamento = $_POST['insegnamento'];
    $studente = $_POST['studente'];
    $dataEsame = $_POST['dataEsame'];
    $valutazione = $_POST['valutazione'];

    // Esegui il codice per la connessione al database PostgreSQL come hai già fatto in precedenza
    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Query per eliminare la riga dal database
    $sql = "UPDATE carriera SET valutazione = :v, data = :d 
            WHERE studente = :s AND insegnamento = :i ";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':s', $studente, PDO::PARAM_STR);
    $stmt->bindParam(':i', $insegnamento, PDO::PARAM_STR);
    $stmt->bindParam(':d', $dataEsame, PDO::PARAM_STR);
    $stmt->bindParam(':v', $valutazione, PDO::PARAM_INT);

    // Esegui la query di aggiornamento
    if ($stmt->execute()) {
        // La riga è stata eliminata con successo
        // Puoi fare altre operazioni o restituire una risposta JSON per gestire la notifica lato client, se necessario
        echo json_encode(['success' => true, 'message' => 'Valori aggiornati in carriera con successo']);
    } else {
        // Si è verificato un errore durante l'eliminazione
        // Puoi restituire un messaggio di errore come risposta JSON, se necessario
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento della riga']);
    }
}
?>