<?php
include('../functions.php');
include('../conf.php');

if (isset($_POST['id']) && isset($_POST['utente'])) {
    $id = $_POST['id'];
    $utente = $_POST['utente'];

    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO iscrizione (studente, esame) VALUES (:s, :e)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':s', $utente, PDO::PARAM_STR);
    $stmt->bindParam(':e', $id, PDO::PARAM_INT);


    if ($stmt->execute()) {
        // L'iscrizione è avvenuta con successo
        echo json_encode(['success' => true, 'message' => 'Iscrizione avvenuta con successo']);
    } else {
        // Si è verificato un errore durante l'iscrizione
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'iscrizione']);
    }
}
$db = null;
?>
