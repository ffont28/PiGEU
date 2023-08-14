<?php
include('../functions.php');
include('../conf.php');
if (isset($_POST['insegnamento']) && isset($_POST['docente'])) {
    $insegnamento = $_POST['insegnamento'];
    $docente = $_POST['docente'];

    // Esegui il codice per la connessione al database PostgreSQL come hai già fatto in precedenza
    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query per inserire la riga dal database
    $sql = "INSERT INTO insegna (docente, insegnamento)
            VALUES (:docente, :insegnamento)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
    $stmt->bindParam(':docente', $docente, PDO::PARAM_STR);


    if (!$stmt->execute()) {
        // La riga è stata eliminata con successo
        // Puoi fare altre operazioni o restituire una risposta JSON per gestire la notifica lato client, se necessario
        header('Content-Type: application/json'); // Imposta l'intestazione JSON
        echo json_encode(['success' => false, 'message' => 'Errore durante la INSERT in insegna']);
    } else {
        header('Content-Type: application/json'); // Imposta l'intestazione JSON
        echo json_encode(['success' => true, 'message' => 'Riga inserita con successo anche eventualmente nella propedeuticita']);
    }
}
?>