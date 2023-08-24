<?php
include('../functions.php');
include('../conf.php');
importVari();

try {
    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM corso_di_laurea WHERE codice = :valore");
    $stmt->bindParam(':valore', $selezioneMenutendina1);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
?>      <option value="1">primo</option>
        <option value="2">secondo</option>
<?php   if ($row['tipo'] == 'magistrale a ciclo unico' || $row['tipo'] == 'triennale' ){
?>      <option value="3">terzo</option>
<?php   }
        if ($row['tipo'] == 'magistrale a ciclo unico'){
?>      <option value="4">quarto</option>
        <option value="5">quinto</option>
<?php   }
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}

$conn = null;
?>
