<?php
include('../functions.php');
include('../conf.php');
try {
    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore nella connessione al database: " . $e->getMessage());
}


$codice = $_POST['codice'];
$user = $_POST['user'];

try {
    $query = "DELETE FROM iscrizione WHERE esame = :codice AND studente = :user";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':codice', $codice);
    $stmt->bindParam(':user', $user);
    $stmt->execute();

    echo "Iscrizione cancellata con successo!";
} catch (PDOException $e) {
    // In caso di errori durante la cancellazione, invia una risposta di errore al client
    echo "Errore durante la cancellazione dell'iscrizione: " . $e->getMessage();
}
$conn = null;
?>