<?php
include('functions.php');
include('conf.php');
if (isset($_POST['cod']) && isset($_POST['data']) && isset($_POST['ora'])) {
    $cod = $_POST['cod'];
    $data = $_POST['data'];
    $ora = $_POST['ora'];


    // Esegui il codice per la connessione al database PostgreSQL come hai già fatto in precedenza
    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query per eliminare la riga dal database
    $sql = "DELETE FROM calendario_esami WHERE insegnamento = :cod AND data = :data AND ora = :ora";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cod', $cod, PDO::PARAM_INT);
    $stmt->bindParam(':data', $data, PDO::PARAM_STR);
    $stmt->bindParam(':ora', $ora, PDO::PARAM_STR);

    // Esegui la query di eliminazione
    if ($stmt->execute()) {
        // La riga è stata eliminata con successo
        // Puoi fare altre operazioni o restituire una risposta JSON per gestire la notifica lato client, se necessario
        echo json_encode(['success' => true, 'message' => 'Riga eliminata con successo']);
    } else {
        // Si è verificato un errore durante l'eliminazione
        // Puoi restituire un messaggio di errore come risposta JSON, se necessario
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della riga']);
    }
}
?>