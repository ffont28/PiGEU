<?php
include('../functions.php');
include('../conf.php');
importVari();

$selezioneMenutendina1 = $_GET['value'];

try {

    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT distinct(i.nome), i.codice FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl p ON p.corso_di_laurea = :valore 
                                                                        AND p.insegnamento = i.codice 
                            ");
    $stmt->bindParam(':valore', $selezioneMenutendina1);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>  <option selected value="no">nessuna propedeuticità</option>
    // Genero le opzioni per il secondo menù a tendina
<?php
    foreach ($results as $row) {
?>      <option value="<?php echo $row['codice']?>"><?php echo $row['nome'] ?></option>
<?php
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
$conn = null;
?>
