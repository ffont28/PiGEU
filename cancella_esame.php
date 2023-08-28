<?php
include('functions.php');
include('conf.php');
if (isset($_POST['cod']) && isset($_POST['data']) && isset($_POST['ora'])) {
    $cod = $_POST['cod'];
    $data = $_POST['data'];
    $ora = $_POST['ora'];

    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "DELETE FROM calendario_esami WHERE insegnamento = :cod AND data = :data AND ora = :ora";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cod', $cod, PDO::PARAM_INT);
    $stmt->bindParam(':data', $data, PDO::PARAM_STR);
    $stmt->bindParam(':ora', $ora, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // La riga è stata eliminata con successo
        echo json_encode(['success' => true, 'message' => 'Riga eliminata con successo']);
    } else {
        // Si è verificato un errore durante l'eliminazione

        echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione della riga']);
    }
}
?>